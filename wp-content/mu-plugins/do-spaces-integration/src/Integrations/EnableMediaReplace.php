<?php

namespace DOSpaces\Integration\Integrations;

use DOSpaces\Integration\Settings\SettingsManager;
use DOSpaces\Integration\Admin\MediaMigration;
use DOSpaces\Integration\Logger\Logger;
use DOSpaces\Integration\Upload\S3ClientFactory;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enable Media Replace Integration
 * Cleans up old Spaces files when EMR replaces an attachment
 */
class EnableMediaReplace implements IntegrationInterface {

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
     * Check if Enable Media Replace is active
     *
     * @return bool
     */
    public function is_active(): bool {
        return class_exists('EnableMediaReplace\EnableMediaReplace');
    }

    /**
     * Register EMR hooks
     */
    public function init(): void {
        add_action('emr_after_remove_current', [$this, 'delete_old_from_spaces'], 10, 5);
        add_filter('emr/replace/file_is_movable', [$this, 'is_file_movable'], 10, 2);
        add_action('enable-media-replace-upload-done', [$this, 'on_replace_done'], 10, 3);

        $this->logger->info('Enable Media Replace integration loaded');
    }

    /**
     * Delete old file and thumbnails from Spaces when EMR removes them locally
     *
     * @param int $post_id Attachment post ID
     * @param array $meta Attachment metadata (old)
     * @param array $backup_sizes Backup sizes array
     * @param string $sourceFile Old file path
     * @param string $targetFile New file path
     */
    public function delete_old_from_spaces($post_id, $meta, $backup_sizes, $sourceFile, $targetFile) {
        if (!$this->settings_manager->is_enabled() || !$this->settings_manager->is_configured()) {
            return;
        }

        $this->logger->info('EMR replacement detected, cleaning old files from Spaces', [
            'attachment_id' => $post_id,
            'old_file' => basename($sourceFile),
            'new_file' => basename($targetFile),
        ]);

        // Get the old attached file path (still has the OLD value at this point)
        $old_file = get_post_meta($post_id, '_wp_attached_file', true);

        if (!$old_file) {
            $this->logger->warning('No _wp_attached_file meta found for attachment', [
                'attachment_id' => $post_id,
            ]);
            return;
        }

        try {
            $s3_client = $this->s3_factory->get_client();
            $bucket = $this->settings_manager->get('bucket');

            // Delete main file from Spaces
            $key = $this->s3_factory->get_s3_key($old_file);
            $s3_client->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            $this->logger->info('Deleted old main file from Spaces', [
                'key' => $key,
            ]);

            // Delete old thumbnails from Spaces
            if (!empty($meta['sizes'])) {
                $file_dir = dirname($old_file);

                foreach ($meta['sizes'] as $size => $size_data) {
                    $thumb_file = $file_dir . '/' . $size_data['file'];
                    $thumb_key = $this->s3_factory->get_s3_key($thumb_file);

                    $s3_client->deleteObject([
                        'Bucket' => $bucket,
                        'Key' => $thumb_key,
                    ]);

                    $this->logger->debug('Deleted old thumbnail from Spaces', [
                        'size' => $size,
                        'key' => $thumb_key,
                    ]);
                }
            }

            // Belt-and-suspenders: clean up old local files if keep_local is false
            if (!$this->should_keep_local()) {
                if (file_exists($sourceFile)) {
                    unlink($sourceFile);
                    $this->logger->debug('Deleted old local file (safety net)', [
                        'file' => basename($sourceFile),
                    ]);
                }

                if (!empty($meta['sizes'])) {
                    $local_dir = dirname($sourceFile);

                    foreach ($meta['sizes'] as $size => $size_data) {
                        $thumb_path = $local_dir . '/' . $size_data['file'];
                        if (file_exists($thumb_path)) {
                            unlink($thumb_path);
                            $this->logger->debug('Deleted old local thumbnail (safety net)', [
                                'size' => $size,
                                'file' => $size_data['file'],
                            ]);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('Failed to delete old files from Spaces during EMR replacement', [
                'attachment_id' => $post_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Prevent EMR from trying to move offloaded files between directories
     *
     * @param bool $movable Whether the file is movable
     * @param int $attachment_id Attachment post ID
     * @return bool
     */
    public function is_file_movable($movable, $attachment_id) {
        $is_migrated = get_post_meta($attachment_id, MediaMigration::META_KEY_MIGRATED, true);

        if ($is_migrated) {
            $this->logger->debug('Marking file as not movable (offloaded to Spaces)', [
                'attachment_id' => $attachment_id,
            ]);
            return false;
        }

        return $movable;
    }

    /**
     * Log replacement completion and update migration timestamp
     *
     * @param string $target_url New file URL
     * @param string $source_url Old file URL
     * @param int $post_id Attachment post ID
     */
    public function on_replace_done($target_url, $source_url, $post_id) {
        update_post_meta($post_id, MediaMigration::META_KEY_MIGRATION_DATE, current_time('mysql'));

        $this->logger->info('EMR replacement complete', [
            'attachment_id' => $post_id,
            'old_url' => $source_url,
            'new_url' => $target_url,
        ]);
    }

    /**
     * Check if local backup should be kept
     *
     * @return bool
     */
    private function should_keep_local() {
        return (bool) $this->settings_manager->get('keep_local', false);
    }
}
