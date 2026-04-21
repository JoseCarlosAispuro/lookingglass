<?php

namespace DOSpaces\Integration\Upload;

use DOSpaces\Integration\Settings\SettingsManager;
use DOSpaces\Integration\Logger\Logger;
use Aws\S3\S3Client;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * S3 Client Factory
 * Shared factory for creating and caching S3Client instances and uploading files to Spaces
 */
class S3ClientFactory {

    /**
     * @var SettingsManager
     */
    private $settings_manager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var S3Client|null
     */
    private $s3_client = null;

    /**
     * @param SettingsManager $settings_manager
     * @param Logger $logger
     */
    public function __construct(SettingsManager $settings_manager, Logger $logger) {
        $this->settings_manager = $settings_manager;
        $this->logger = $logger;
    }

    /**
     * Get or create a cached S3Client instance
     *
     * @return S3Client
     * @throws \Exception
     */
    public function get_client() {
        if ($this->s3_client !== null) {
            return $this->s3_client;
        }

        $settings = $this->settings_manager->get_settings();

        try {
            $this->s3_client = new S3Client([
                'version' => 'latest',
                'region' => $settings['region'],
                'endpoint' => $settings['endpoint'],
                'credentials' => [
                    'key' => $settings['access_key'],
                    'secret' => $settings['access_secret'],
                ],
                'use_path_style_endpoint' => false,
                'http' => [
                    'timeout' => 300,
                    'connect_timeout' => 30,
                ],
                'retries' => [
                    'mode' => 'adaptive',
                    'max_attempts' => 3,
                ],
            ]);

            return $this->s3_client;

        } catch (\Exception $e) {
            $this->logger->error('S3Client initialization error', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to initialize S3 client: ' . $e->getMessage());
        }
    }

    /**
     * Upload a file to Spaces
     *
     * @param string $file_path Local file path
     * @param string $mime_type File MIME type
     * @param string|null $spaces_key Optional explicit S3 key. If null, derived from file_path.
     * @return bool True on success
     * @throws \Exception
     */
    public function upload_file($file_path, $mime_type, $spaces_key = null) {
        if (!file_exists($file_path)) {
            $this->logger->error('File does not exist before upload', ['path' => $file_path]);
            throw new \Exception('File does not exist: ' . $file_path);
        }

        if (!is_readable($file_path)) {
            $this->logger->error('File is not readable', ['path' => $file_path]);
            throw new \Exception('File is not readable: ' . $file_path);
        }

        $file_size = filesize($file_path);
        $s3_client = $this->get_client();
        $bucket = $this->settings_manager->get('bucket');

        if ($spaces_key === null) {
            $spaces_key = $this->get_s3_key_from_path($file_path);
        }

        $this->logger->debug('Upload details', [
            'bucket' => $bucket,
            'key' => $spaces_key,
            'file_size' => $file_size,
            'mime_type' => $mime_type,
            'method' => $file_size > 5 * 1024 * 1024 ? 'multipart' : 'standard',
        ]);

        $start_time = microtime(true);

        if ($file_size > 5 * 1024 * 1024) {
            $this->logger->info('Using multipart upload', ['size' => $file_size]);

            $s3_client->upload(
                $bucket,
                $spaces_key,
                fopen($file_path, 'rb'),
                'public-read',
                [
                    'params' => [
                        'ContentType' => $mime_type,
                    ],
                ]
            );
        } else {
            $this->logger->info('Using standard upload', ['size' => $file_size]);

            $s3_client->putObject([
                'Bucket' => $bucket,
                'Key' => $spaces_key,
                'SourceFile' => $file_path,
                'ACL' => 'public-read',
                'ContentType' => $mime_type,
            ]);
        }

        $duration = microtime(true) - $start_time;
        $this->logger->info('Upload completed', [
            'duration' => round($duration, 2) . 's',
            'file_size' => $file_size,
        ]);

        return true;
    }

    /**
     * Update the Content-Type metadata of an existing object in Spaces
     * by copying the object to itself with new metadata.
     *
     * @param string $spaces_key S3 key of the object
     * @param string $mime_type New Content-Type
     * @return bool True on success
     * @throws \Exception
     */
    public function update_content_type($spaces_key, $mime_type) {
        $s3_client = $this->get_client();
        $bucket = $this->settings_manager->get('bucket');

        $s3_client->copyObject([
            'Bucket' => $bucket,
            'Key' => $spaces_key,
            'CopySource' => $bucket . '/' . $spaces_key,
            'ACL' => 'public-read',
            'ContentType' => $mime_type,
            'MetadataDirective' => 'REPLACE',
        ]);

        $this->logger->info('Updated Content-Type', [
            'key' => $spaces_key,
            'content_type' => $mime_type,
        ]);

        return true;
    }

    /**
     * Check if a file exists in Spaces via headObject
     *
     * @param string $spaces_key S3 key to check
     * @return bool
     */
    public function file_exists_in_spaces($spaces_key) {
        try {
            $this->get_client()->headObject([
                'Bucket' => $this->settings_manager->get('bucket'),
                'Key' => $spaces_key,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Download a file from Spaces to a local path
     *
     * @param string $spaces_key S3 key of the file
     * @param string $local_path Local path to save the file
     * @return string The local path on success
     * @throws \Exception
     */
    public function download_file($spaces_key, $local_path) {
        $s3_client = $this->get_client();
        $bucket = $this->settings_manager->get('bucket');

        // Ensure the target directory exists
        $dir = dirname($local_path);
        if (!is_dir($dir)) {
            wp_mkdir_p($dir);
        }

        $result = $s3_client->getObject([
            'Bucket' => $bucket,
            'Key' => $spaces_key,
            'SaveAs' => $local_path,
        ]);

        if (!file_exists($local_path)) {
            throw new \Exception('Download failed: file not written to ' . $local_path);
        }

        $this->logger->info('Downloaded file from Spaces', [
            'key' => $spaces_key,
            'local_path' => $local_path,
            'size' => filesize($local_path),
        ]);

        return $local_path;
    }

    /**
     * Generate S3 key from full file path
     *
     * @param string $file_path Full file path
     * @return string S3 key
     */
    public function get_s3_key_from_path($file_path) {
        $upload_dir = wp_upload_dir();
        $base_dir = trailingslashit($upload_dir['basedir']);
        $relative_path = str_replace($base_dir, '', $file_path);

        return $this->get_s3_key($relative_path);
    }

    /**
     * Generate S3 key with optional prefix
     *
     * @param string $relative_path Relative file path
     * @return string S3 key
     */
    public function get_s3_key($relative_path) {
        $prefix = $this->settings_manager->get('path_prefix', '');

        if (!empty($prefix)) {
            $prefix = trim($prefix, '/') . '/';
        }

        return $prefix . ltrim($relative_path, '/');
    }
}
