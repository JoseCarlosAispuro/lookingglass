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
 * Metadata Repair
 * Batch tool to regenerate metadata for images with broken dimensions/thumbnails
 */
class MetadataRepair {

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

    const META_KEY_REPAIRED = '_do_spaces_repaired';
    const BATCH_SIZE = 3;

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
        add_action('wp_ajax_do_spaces_get_repair_status', [$this, 'handle_get_repair_status']);
        add_action('wp_ajax_do_spaces_repair_batch', [$this, 'handle_repair_batch']);
        add_action('wp_ajax_do_spaces_reset_repair', [$this, 'handle_reset_repair']);
    }

    /**
     * Get repair status
     */
    public function handle_get_repair_status() {
        check_ajax_referer('do_spaces_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access.']);
        }

        $broken = $this->get_broken_attachments();
        $total_broken = count($broken);
        $repaired = $this->count_repaired();

        wp_send_json_success([
            'total' => $total_broken + $repaired,
            'broken' => $total_broken,
            'repaired' => $repaired,
        ]);
    }

    /**
     * Process a batch of broken images
     */
    public function handle_repair_batch() {
        check_ajax_referer('do_spaces_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access.']);
        }

        set_time_limit(180);

        if (!$this->settings_manager->is_configured()) {
            wp_send_json_error(['message' => 'Plugin is not properly configured.']);
        }

        // Need the image editing functions
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $broken = $this->get_broken_attachments(self::BATCH_SIZE);

        if (empty($broken)) {
            wp_send_json_success([
                'completed' => true,
                'processed' => 0,
                'message' => 'All images have been repaired!',
            ]);
        }

        $results = ['success' => [], 'errors' => []];

        foreach ($broken as $attachment_id) {
            try {
                $this->repair_attachment($attachment_id);
                $results['success'][] = $attachment_id;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'id' => $attachment_id,
                    'message' => $e->getMessage(),
                ];
                // Mark as repaired with error so we don't retry forever
                update_post_meta($attachment_id, self::META_KEY_REPAIRED, 'error');
                $this->logger->error('Repair failed for attachment', [
                    'attachment_id' => $attachment_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $all_broken = $this->get_broken_attachments();
        $remaining = count($all_broken);
        $repaired = $this->count_repaired();

        wp_send_json_success([
            'completed' => false,
            'processed' => count($results['success']),
            'errors' => count($results['errors']),
            'error_details' => $results['errors'],
            'broken' => $remaining,
            'repaired' => $repaired,
        ]);
    }

    /**
     * Reset repair meta so the tool can be re-run
     */
    public function handle_reset_repair() {
        check_ajax_referer('do_spaces_admin', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access.']);
        }

        global $wpdb;

        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->postmeta WHERE meta_key = %s",
                self::META_KEY_REPAIRED
            )
        );

        wp_send_json_success([
            'message' => "Reset repair status. Removed {$deleted} meta entries.",
        ]);
    }

    /**
     * Repair a single attachment: download from Spaces, regenerate metadata, upload thumbnails
     *
     * @param int $attachment_id
     * @throws \Exception
     */
    private function repair_attachment($attachment_id) {
        $attached_file = get_post_meta($attachment_id, '_wp_attached_file', true);
        if (!$attached_file) {
            throw new \Exception('No attached file meta for attachment ' . $attachment_id);
        }

        $spaces_key = $this->s3_factory->get_s3_key($attached_file);

        // Check the file exists in Spaces
        if (!$this->s3_factory->file_exists_in_spaces($spaces_key)) {
            throw new \Exception('Original file not found in Spaces: ' . $spaces_key);
        }

        // Download to the standard uploads location so WP can find it
        $upload_dir = wp_upload_dir();
        $local_path = trailingslashit($upload_dir['basedir']) . $attached_file;

        $already_local = file_exists($local_path);

        if (!$already_local) {
            $this->s3_factory->download_file($spaces_key, $local_path);
        }

        // Verify the downloaded file is a valid image
        if (!file_exists($local_path)) {
            throw new \Exception('File not found locally after download: ' . $local_path);
        }

        $image_size = @getimagesize($local_path);
        if (!$image_size) {
            throw new \Exception('getimagesize() failed - file may not be a valid image: ' . basename($local_path));
        }

        $this->logger->info('Repair: file downloaded and validated', [
            'attachment_id' => $attachment_id,
            'local_path' => $local_path,
            'file_size' => filesize($local_path),
            'dimensions' => $image_size[0] . 'x' . $image_size[1],
            'mime' => $image_size['mime'] ?? 'unknown',
        ]);

        // Temporarily unhook UploadHandler::handle_thumbnails to prevent it from
        // uploading thumbnails and deleting local files during wp_generate_attachment_metadata.
        // We handle uploads and cleanup ourselves.
        remove_all_filters('wp_generate_attachment_metadata');

        // Regenerate metadata (this creates thumbnails on disk)
        $new_metadata = wp_generate_attachment_metadata($attachment_id, $local_path);

        // Re-add our upload handler's filter
        $upload_handler = \DOSpaces\Integration\Plugin::instance()->get_upload_handler();
        add_filter('wp_generate_attachment_metadata', [$upload_handler, 'handle_thumbnails'], 10, 2);

        if (empty($new_metadata)) {
            throw new \Exception('wp_generate_attachment_metadata returned empty for ' . $attachment_id);
        }

        $this->logger->info('Repair: metadata regenerated', [
            'attachment_id' => $attachment_id,
            'width' => $new_metadata['width'] ?? 0,
            'height' => $new_metadata['height'] ?? 0,
            'sizes_count' => !empty($new_metadata['sizes']) ? count($new_metadata['sizes']) : 0,
            'file' => $new_metadata['file'] ?? 'none',
        ]);

        // Update metadata in DB
        wp_update_attachment_metadata($attachment_id, $new_metadata);

        // Upload new thumbnails to Spaces
        $uploaded_count = 0;
        if (!empty($new_metadata['sizes'])) {
            $file_dir = dirname($local_path);

            foreach ($new_metadata['sizes'] as $size => $size_data) {
                $thumb_path = $file_dir . '/' . $size_data['file'];
                if (file_exists($thumb_path)) {
                    $this->s3_factory->upload_file($thumb_path, $size_data['mime-type']);
                    $uploaded_count++;
                } else {
                    $this->logger->warning('Repair: thumbnail file missing locally', [
                        'size' => $size,
                        'expected_path' => $thumb_path,
                    ]);
                }
            }
        }

        $this->logger->info('Repair: thumbnails uploaded', [
            'attachment_id' => $attachment_id,
            'uploaded' => $uploaded_count,
        ]);

        // Clean up local files if we downloaded them (and keep_local is off)
        $keep_local = (bool) $this->settings_manager->get('keep_local', false);
        if (!$keep_local) {
            // Delete new thumbnails locally
            if (!empty($new_metadata['sizes'])) {
                $file_dir = dirname($local_path);
                foreach ($new_metadata['sizes'] as $size_data) {
                    $thumb_path = $file_dir . '/' . $size_data['file'];
                    if (file_exists($thumb_path)) {
                        unlink($thumb_path);
                    }
                }
            }

            // Delete original if we downloaded it
            if (!$already_local && file_exists($local_path)) {
                unlink($local_path);
            }
        }

        // Mark as repaired
        update_post_meta($attachment_id, self::META_KEY_REPAIRED, current_time('mysql'));

        $this->logger->info('Repaired attachment metadata', [
            'attachment_id' => $attachment_id,
            'width' => $new_metadata['width'] ?? 0,
            'height' => $new_metadata['height'] ?? 0,
            'sizes' => !empty($new_metadata['sizes']) ? count($new_metadata['sizes']) : 0,
        ]);
    }

    /**
     * Get image attachments with broken metadata (missing dimensions or sizes)
     *
     * @param int|null $limit Max results, null for all
     * @return array Array of attachment IDs
     */
    private function get_broken_attachments($limit = null) {
        global $wpdb;

        // Fetch all unrepaired image attachments. Use LEFT JOIN on metadata
        // because images affected by the early-deletion bug may have no
        // _wp_attachment_metadata row at all.
        $attachment_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT p.ID FROM $wpdb->posts p
                 LEFT JOIN $wpdb->postmeta pr ON p.ID = pr.post_id AND pr.meta_key = %s
                 WHERE p.post_type = 'attachment'
                 AND p.post_status = 'inherit'
                 AND p.post_mime_type LIKE 'image/%%'
                 AND p.post_mime_type NOT LIKE 'image/svg%%'
                 AND pr.meta_id IS NULL
                 ORDER BY p.ID ASC",
                self::META_KEY_REPAIRED
            )
        );

        // Filter to ones with actually broken metadata
        $broken = [];
        foreach ($attachment_ids as $id) {
            $metadata = wp_get_attachment_metadata($id);
            if ($this->is_metadata_broken($metadata)) {
                $broken[] = (int) $id;
            }
            // Stop if we've hit the limit
            if ($limit && count($broken) >= $limit) {
                break;
            }
        }

        return $broken;
    }

    /**
     * Check if attachment metadata indicates broken dimensions/thumbnails
     *
     * @param mixed $metadata
     * @return bool
     */
    private function is_metadata_broken($metadata) {
        // No metadata at all (file was deleted before WP could generate it)
        if (!is_array($metadata) || empty($metadata)) {
            return true;
        }

        // Missing or zero/one dimensions
        $width = $metadata['width'] ?? 0;
        $height = $metadata['height'] ?? 0;
        if ($width <= 1 || $height <= 1) {
            return true;
        }

        // Only flag missing sizes if the image is large enough to have thumbnails.
        // Small images (under 150px in both dimensions) correctly have no sizes.
        if (empty($metadata['sizes']) && ($width > 150 || $height > 150)) {
            return true;
        }

        return false;
    }

    /**
     * Count repaired attachments
     *
     * @return int
     */
    private function count_repaired() {
        global $wpdb;
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s",
                self::META_KEY_REPAIRED
            )
        );
    }
}
