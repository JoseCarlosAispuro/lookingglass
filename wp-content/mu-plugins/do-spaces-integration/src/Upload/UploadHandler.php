<?php

namespace DOSpaces\Integration\Upload;

use DOSpaces\Integration\Settings\SettingsManager;
use DOSpaces\Integration\Admin\MediaMigration;
use DOSpaces\Integration\Logger\Logger;
use Aws\Exception\AwsException;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Upload Handler
 * Handles file uploads to Digital Ocean Spaces
 */
class UploadHandler {

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
     * Initialize hooks
     */
    public function init() {
        add_filter('wp_handle_upload', [$this, 'handle_upload'], 10, 2);
        add_filter('wp_generate_attachment_metadata', [$this, 'handle_thumbnails'], 10, 2);
        add_action('add_attachment', [$this, 'mark_as_migrated'], 10);
        add_action('delete_attachment', [$this, 'delete_from_spaces'], 10);
    }

    /**
     * Handle file upload to Spaces
     *
     * @param array $file Upload file data
     * @param string $action Upload action type
     * @return array Modified file data
     */
    public function handle_upload($file, $action = 'upload') {
        // Log entry point
        $this->logger->debug('handle_upload() called', [
            'file' => isset($file['file']) ? basename($file['file']) : 'unknown',
            'action' => $action,
            'enabled' => $this->settings_manager->is_enabled(),
            'configured' => $this->settings_manager->is_configured(),
        ]);

        // Only process if plugin is enabled and configured
        if (!$this->settings_manager->is_enabled() || !$this->settings_manager->is_configured()) {
            $this->logger->debug('Upload skipped - plugin not enabled or not configured');
            return $file;
        }

        // Process both standard uploads and sideloads
        if ($action !== 'upload' && $action !== 'sideload') {
            $this->logger->debug('Upload skipped - action is not "upload" or "sideload"', ['action' => $action]);
            return $file;
        }

        // Check for upload errors
        if (isset($file['error']) && $file['error']) {
            $this->logger->debug('Upload skipped - file has error', ['error' => $file['error']]);
            return $file;
        }

        try {
            // Log before upload
            $this->logger->info('Starting upload to Spaces', [
                'file' => basename($file['file']),
                'type' => $file['type'],
                'size' => file_exists($file['file']) ? filesize($file['file']) : 'unknown',
            ]);

            // Upload to Spaces
            $this->s3_factory->upload_file($file['file'], $file['type']);

            $this->logger->info('Upload to Spaces successful', [
                'file' => basename($file['file']),
            ]);

            // Keep local file until after WordPress generates thumbnails.
            // Deletion happens in handle_thumbnails() after all sizes are uploaded.
            $this->logger->debug('Keeping local file for WordPress metadata/thumbnail generation');

        } catch (AwsException $e) {
            $this->logger->log_aws_exception($e, 'upload', ['file' => basename($file['file'])]);
            $this->handle_aws_error($e, $file);
        } catch (\Exception $e) {
            $this->logger->error('Upload failed with exception', [
                'file' => basename($file['file']),
                'error' => $e->getMessage(),
            ]);
            $this->handle_error($e, $file);
        }

        return $file;
    }

    /**
     * Handle thumbnail uploads after generation
     *
     * @param array $metadata Attachment metadata
     * @param int $attachment_id Attachment post ID
     * @return array Modified metadata
     */
    public function handle_thumbnails($metadata, $attachment_id) {
        // Only process if plugin is enabled and configured
        if (!$this->settings_manager->is_enabled() || !$this->settings_manager->is_configured()) {
            return $metadata;
        }

        // Get the original file path
        $file_path = get_attached_file($attachment_id);

        if (!file_exists($file_path)) {
            return $metadata;
        }

        try {
            // Upload each thumbnail size
            $uploaded_thumbs = [];
            if (!empty($metadata['sizes'])) {
                $file_dir = dirname($file_path);

                foreach ($metadata['sizes'] as $size => $size_data) {
                    $thumb_path = $file_dir . '/' . $size_data['file'];

                    if (file_exists($thumb_path)) {
                        $this->s3_factory->upload_file($thumb_path, $size_data['mime-type']);
                        $uploaded_thumbs[] = $thumb_path;
                    }
                }
            }

            // All thumbnails uploaded. Now clean up local files if not keeping local copies.
            if (!$this->should_keep_local()) {
                // Delete thumbnails
                foreach ($uploaded_thumbs as $thumb_path) {
                    $spaces_key = $this->s3_factory->get_s3_key_from_path($thumb_path);
                    if ($this->s3_factory->file_exists_in_spaces($spaces_key)) {
                        $this->delete_local_file($thumb_path);
                    }
                }

                // Delete original file
                $spaces_key = $this->s3_factory->get_s3_key_from_path($file_path);
                if ($this->s3_factory->file_exists_in_spaces($spaces_key)) {
                    $this->logger->debug('Deleting original local file after thumbnail processing');
                    $this->delete_local_file($file_path);
                } else {
                    $this->logger->warning('Original file not verified in Spaces, keeping local copy', [
                        'file' => basename($file_path),
                    ]);
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('Thumbnail upload error', [
                'attachment_id' => $attachment_id,
                'error' => $e->getMessage(),
            ]);
        }

        return $metadata;
    }

    /**
     * Mark a newly uploaded attachment as migrated so the migration tool skips it.
     * Fires on `add_attachment` which runs for all file types including SVGs.
     *
     * @param int $attachment_id Attachment post ID
     */
    public function mark_as_migrated($attachment_id) {
        if (!$this->settings_manager->is_enabled() || !$this->settings_manager->is_configured()) {
            return;
        }

        update_post_meta($attachment_id, MediaMigration::META_KEY_MIGRATED, true);
        update_post_meta($attachment_id, MediaMigration::META_KEY_MIGRATION_DATE, current_time('mysql'));
    }

    /**
     * Delete file from Spaces when attachment is deleted
     *
     * @param int $attachment_id Attachment post ID
     */
    public function delete_from_spaces($attachment_id) {
        // Only process if plugin is enabled and configured
        if (!$this->settings_manager->is_enabled() || !$this->settings_manager->is_configured()) {
            return;
        }

        $file = get_post_meta($attachment_id, '_wp_attached_file', true);

        if (!$file) {
            return;
        }

        try {
            $s3_client = $this->s3_factory->get_client();
            $bucket = $this->settings_manager->get('bucket');

            // Delete main file
            $key = $this->s3_factory->get_s3_key($file);
            $s3_client->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            // Delete thumbnails
            $metadata = wp_get_attachment_metadata($attachment_id);
            if (!empty($metadata['sizes'])) {
                $file_dir = dirname($file);

                foreach ($metadata['sizes'] as $size_data) {
                    $thumb_file = $file_dir . '/' . $size_data['file'];
                    $thumb_key = $this->s3_factory->get_s3_key($thumb_file);

                    $s3_client->deleteObject([
                        'Bucket' => $bucket,
                        'Key' => $thumb_key,
                    ]);
                }
            }

            // Belt-and-suspenders: clean up local files if keep_local is false
            if (!$this->should_keep_local()) {
                $local_path = get_attached_file($attachment_id);
                if ($local_path && file_exists($local_path)) {
                    $this->delete_local_file($local_path);
                }

                if (!empty($metadata['sizes'])) {
                    $local_dir = dirname(get_attached_file($attachment_id));

                    foreach ($metadata['sizes'] as $size_data) {
                        $thumb_path = $local_dir . '/' . $size_data['file'];
                        if (file_exists($thumb_path)) {
                            $this->delete_local_file($thumb_path);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->logger->error('Deletion from Spaces failed', [
                'attachment_id' => $attachment_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if local backup should be kept
     *
     * @return bool
     */
    private function should_keep_local() {
        return (bool) $this->settings_manager->get('keep_local', false);
    }

    /**
     * Delete local file
     *
     * @param string $file_path File path to delete
     * @return bool True on success
     */
    private function delete_local_file($file_path) {
        if (file_exists($file_path)) {
            $result = unlink($file_path);

            if (!$result) {
                $this->logger->warning('Failed to delete local file', [
                    'file' => basename($file_path),
                ]);
            } else {
                $this->logger->debug('Deleted local file', [
                    'file' => basename($file_path),
                ]);
            }

            return $result;
        }

        return false;
    }

    /**
     * Handle AWS-specific errors
     *
     * @param AwsException $e AWS Exception object
     * @param array $file File data
     */
    private function handle_aws_error(AwsException $e, $file) {
        $error_code = $e->getAwsErrorCode();
        $error_message = $e->getAwsErrorMessage();

        switch ($error_code) {
            case 'NoSuchBucket':
                $message = 'Bucket does not exist. Please check your settings.';
                break;
            case 'InvalidAccessKeyId':
                $message = 'Invalid access key. Please check your credentials.';
                break;
            case 'SignatureDoesNotMatch':
                $message = 'Invalid secret key. Please check your credentials.';
                break;
            case 'AccessDenied':
                $message = 'Access denied. Your credentials may not have write permission.';
                break;
            default:
                $message = 'Upload to Spaces failed: ' . $error_message;
                break;
        }

        // Add admin notice
        add_action('admin_notices', function() use ($message, $file) {
            $filename = basename($file['file']);
            echo '<div class="notice notice-error"><p>';
            echo '<strong>Digital Ocean Spaces:</strong> Failed to upload "' . esc_html($filename) . '". ';
            echo esc_html($message);
            echo ' File saved locally as fallback.';
            echo '</p></div>';
        });
    }

    /**
     * Handle upload errors
     *
     * @param \Exception $e Exception object
     * @param array $file File data
     */
    private function handle_error(\Exception $e, $file) {
        // Add admin notice
        add_action('admin_notices', function() use ($e, $file) {
            $filename = basename($file['file']);
            echo '<div class="notice notice-error"><p>';
            echo '<strong>Digital Ocean Spaces:</strong> Failed to upload "' . esc_html($filename) . '". ';
            echo 'File saved locally. Error: ' . esc_html($e->getMessage());
            echo '</p></div>';
        });
    }
}
