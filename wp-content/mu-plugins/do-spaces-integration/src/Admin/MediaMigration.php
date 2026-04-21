<?php

namespace DOSpaces\Integration\Admin;

use DOSpaces\Integration\Settings\SettingsManager;
use DOSpaces\Integration\Logger\Logger;
use DOSpaces\Integration\Upload\S3ClientFactory;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Media Migration
 * Handles migration of existing WordPress media files to Digital Ocean Spaces
 */
class MediaMigration {

    /**
     * @var SettingsManager
     */
    private $settings_manager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var S3ClientFactory
     */
    private $s3_factory;

    /**
     * Post meta keys
     */
    const META_KEY_MIGRATED = '_do_spaces_migrated';
    const META_KEY_MIGRATION_DATE = '_do_spaces_migration_date';
    const META_KEY_MIGRATION_ERROR = '_do_spaces_migration_error';

    /**
     * Batch size for processing
     */
    const BATCH_SIZE = 5;

    /**
     * @param SettingsManager $settings_manager
     * @param Logger $logger
     * @param S3ClientFactory $s3_factory
     */
    public function __construct(SettingsManager $settings_manager, Logger $logger, S3ClientFactory $s3_factory) {
        $this->settings_manager = $settings_manager;
        $this->logger = $logger;
        $this->s3_factory = $s3_factory;
    }

    /**
     * Initialize hooks
     */
    public function init() {
        add_action('wp_ajax_do_spaces_get_migration_status', [$this, 'handle_get_status']);
        add_action('wp_ajax_do_spaces_migrate_batch', [$this, 'handle_migrate_batch']);
        add_action('wp_ajax_do_spaces_reset_migration', [$this, 'handle_reset_migration']);
        add_action('wp_ajax_do_spaces_fix_svg_status', [$this, 'handle_fix_svg_status']);
        add_action('wp_ajax_do_spaces_fix_svg_batch', [$this, 'handle_fix_svg_batch']);
    }

    /**
     * Get migration status (total, migrated, remaining)
     */
    public function handle_get_status() {
        check_ajax_referer('do_spaces_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access.']);
        }

        $total = $this->count_total_attachments();
        $migrated = $this->count_migrated_attachments();
        $remaining = $total - $migrated;

        wp_send_json_success([
            'total' => $total,
            'migrated' => $migrated,
            'remaining' => $remaining,
            'percentage' => $total > 0 ? round(($migrated / $total) * 100, 1) : 0
        ]);
    }

    /**
     * Process a batch of unmigrated attachments
     */
    public function handle_migrate_batch() {
        check_ajax_referer('do_spaces_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access.']);
        }

        // Extend execution time for migration uploads
        set_time_limit(120);

        // Check if plugin is configured
        if (!$this->settings_manager->is_configured()) {
            wp_send_json_error(['message' => 'Plugin is not properly configured.']);
        }

        try {
            $this->s3_factory->get_client();
        } catch (\Exception $e) {
            wp_send_json_error(['message' => 'Failed to connect to Spaces: ' . $e->getMessage()]);
        }

        // Get batch of unmigrated attachments
        $attachments = $this->get_unmigrated_attachments(self::BATCH_SIZE);

        if (empty($attachments)) {
            wp_send_json_success([
                'completed' => true,
                'processed' => 0,
                'message' => 'Migration complete!'
            ]);
        }

        $results = [
            'success' => [],
            'errors' => [],
        ];

        foreach ($attachments as $attachment_id) {
            try {
                $this->migrate_attachment($attachment_id);
                $results['success'][] = $attachment_id;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'id' => $attachment_id,
                    'message' => $e->getMessage()
                ];
                // Mark as failed so it's skipped on retry
                update_post_meta($attachment_id, self::META_KEY_MIGRATION_ERROR, $e->getMessage());
                update_post_meta($attachment_id, self::META_KEY_MIGRATED, 'error');
                $this->logger->error('Migration failed for attachment', [
                    'attachment_id' => $attachment_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Get updated status
        $total = $this->count_total_attachments();
        $migrated = $this->count_migrated_attachments();

        wp_send_json_success([
            'completed' => false,
            'processed' => count($results['success']),
            'errors' => count($results['errors']),
            'error_details' => $results['errors'],
            'total' => $total,
            'migrated' => $migrated,
            'percentage' => $total > 0 ? round(($migrated / $total) * 100, 1) : 0
        ]);
    }

    /**
     * Reset migration status for all attachments
     */
    public function handle_reset_migration() {
        check_ajax_referer('do_spaces_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access.']);
        }

        global $wpdb;

        $meta_keys = [
            self::META_KEY_MIGRATED,
            self::META_KEY_MIGRATION_DATE,
            self::META_KEY_MIGRATION_ERROR,
        ];

        $placeholders = implode(',', array_fill(0, count($meta_keys), '%s'));
        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->postmeta WHERE meta_key IN ($placeholders)",
                ...$meta_keys
            )
        );

        wp_send_json_success([
            'message' => "Reset migration status. Removed {$deleted} meta entries."
        ]);
    }

    /**
     * Migrate a single attachment and all its thumbnails
     *
     * @param int $attachment_id Attachment post ID
     * @throws \Exception If file not found or upload fails
     */
    private function migrate_attachment($attachment_id) {
        $file_path = get_attached_file($attachment_id);

        if (!$file_path || !file_exists($file_path)) {
            throw new \Exception('File not found: ' . $file_path);
        }

        // Get upload directory info
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];

        // Upload main file
        $relative_path = str_replace($base_dir . '/', '', $file_path);
        $mime_type = $this->get_mime_type($file_path);
        $spaces_key = $this->s3_factory->get_s3_key($relative_path);
        $this->s3_factory->upload_file($file_path, $mime_type, $spaces_key);

        // Upload thumbnails
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
            $file_dir = dirname($file_path);

            foreach ($metadata['sizes'] as $size => $size_data) {
                if (empty($size_data['file'])) {
                    continue;
                }

                $thumbnail_path = $file_dir . '/' . $size_data['file'];
                if (file_exists($thumbnail_path)) {
                    $thumbnail_relative = str_replace($base_dir . '/', '', $thumbnail_path);
                    $thumb_mime = $this->get_mime_type($thumbnail_path);
                    $thumb_key = $this->s3_factory->get_s3_key($thumbnail_relative);
                    $this->s3_factory->upload_file($thumbnail_path, $thumb_mime, $thumb_key);
                }
            }
        }

        // Mark as migrated
        update_post_meta($attachment_id, self::META_KEY_MIGRATED, true);
        update_post_meta($attachment_id, self::META_KEY_MIGRATION_DATE, current_time('mysql'));
        delete_post_meta($attachment_id, self::META_KEY_MIGRATION_ERROR);

        $this->logger->info('Migrated attachment to Spaces', [
            'attachment_id' => $attachment_id,
            'file' => basename($file_path)
        ]);
    }

    /**
     * Get SVG fix status (total SVGs that are migrated)
     */
    public function handle_fix_svg_status() {
        check_ajax_referer('do_spaces_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access.']);
        }

        $svg_ids = $this->get_svg_attachment_ids();

        wp_send_json_success([
            'total' => count($svg_ids),
        ]);
    }

    /**
     * Fix Content-Type for all SVG files already in Spaces
     */
    public function handle_fix_svg_batch() {
        check_ajax_referer('do_spaces_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access.']);
        }

        set_time_limit(120);

        if (!$this->settings_manager->is_configured()) {
            wp_send_json_error(['message' => 'Plugin is not properly configured.']);
        }

        try {
            $this->s3_factory->get_client();
        } catch (\Exception $e) {
            wp_send_json_error(['message' => 'Failed to connect to Spaces: ' . $e->getMessage()]);
        }

        $svg_ids = $this->get_svg_attachment_ids();

        if (empty($svg_ids)) {
            wp_send_json_success([
                'completed' => true,
                'fixed' => 0,
                'message' => 'No SVG files found in the media library.',
            ]);
        }

        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];
        $fixed = 0;
        $errors = [];

        foreach ($svg_ids as $attachment_id) {
            $file_path = get_attached_file($attachment_id);

            if (!$file_path) {
                $errors[] = ['id' => $attachment_id, 'message' => 'No file path found'];
                continue;
            }

            $relative_path = str_replace($base_dir . '/', '', $file_path);
            $spaces_key = $this->s3_factory->get_s3_key($relative_path);

            try {
                if ($this->s3_factory->file_exists_in_spaces($spaces_key)) {
                    $this->s3_factory->update_content_type($spaces_key, 'image/svg+xml');
                    $fixed++;

                    $this->logger->info('Fixed SVG Content-Type', [
                        'attachment_id' => $attachment_id,
                        'key' => $spaces_key,
                    ]);
                } else {
                    $errors[] = ['id' => $attachment_id, 'message' => 'File not found in Spaces: ' . $spaces_key];
                }
            } catch (\Exception $e) {
                $errors[] = ['id' => $attachment_id, 'message' => $e->getMessage()];
                $this->logger->error('Failed to fix SVG Content-Type', [
                    'attachment_id' => $attachment_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        wp_send_json_success([
            'completed' => true,
            'fixed' => $fixed,
            'total' => count($svg_ids),
            'errors' => count($errors),
            'error_details' => $errors,
            'message' => "Fixed Content-Type for {$fixed} of " . count($svg_ids) . " SVG file(s).",
        ]);
    }

    /**
     * Get all SVG attachment IDs
     *
     * @return array
     */
    private function get_svg_attachment_ids() {
        global $wpdb;

        return $wpdb->get_col(
            "SELECT ID FROM $wpdb->posts
             WHERE post_type = 'attachment'
             AND post_status = 'inherit'
             AND post_mime_type = 'image/svg+xml'"
        );
    }

    /**
     * Get MIME type for a file using WordPress functions
     *
     * @param string $file_path
     * @return string
     */
    private function get_mime_type($file_path) {
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        $extra_mimes = [
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'webp' => 'image/webp',
        ];

        if (isset($extra_mimes[$extension])) {
            return $extra_mimes[$extension];
        }

        $filetype = wp_check_filetype(basename($file_path));
        return $filetype['type'] ?: 'application/octet-stream';
    }

    /**
     * Count total attachments using a direct COUNT query
     *
     * @return int
     */
    private function count_total_attachments() {
        global $wpdb;
        return (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status = 'inherit'"
        );
    }

    /**
     * Count migrated attachments using a direct COUNT query
     *
     * @return int
     */
    private function count_migrated_attachments() {
        global $wpdb;
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->posts p
                 INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
                 WHERE p.post_type = 'attachment'
                 AND p.post_status = 'inherit'
                 AND pm.meta_key = %s",
                self::META_KEY_MIGRATED
            )
        );
    }

    /**
     * Get batch of unmigrated attachments
     *
     * @param int $limit Number of attachments to retrieve
     * @return array Array of attachment IDs
     */
    private function get_unmigrated_attachments($limit = 50) {
        $query = new \WP_Query([
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => $limit,
            'fields' => 'ids',
            'orderby' => 'ID',
            'order' => 'ASC',
            'meta_query' => [
                [
                    'key' => self::META_KEY_MIGRATED,
                    'compare' => 'NOT EXISTS'
                ]
            ]
        ]);

        return $query->posts;
    }
}
