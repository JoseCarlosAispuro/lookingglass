<?php

namespace DOSpaces\Integration\Admin;

use DOSpaces\Integration\Settings\SettingsManager;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Connection Test
 * Handles AJAX connection testing for Digital Ocean Spaces
 */
class ConnectionTest {

    /**
     * Settings Manager instance
     *
     * @var SettingsManager
     */
    private $settings_manager;

    /**
     * Constructor
     *
     * @param SettingsManager $settings_manager
     */
    public function __construct(SettingsManager $settings_manager) {
        $this->settings_manager = $settings_manager;
    }

    /**
     * Initialize hooks
     */
    public function init() {
        add_action('wp_ajax_do_spaces_test_connection', [$this, 'handle_ajax']);
    }

    /**
     * Handle AJAX connection test request
     */
    public function handle_ajax() {
        // Verify nonce
        check_ajax_referer('do_spaces_admin', 'nonce');

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => 'Unauthorized access.',
            ]);
        }

        // Get settings from POST data, falling back to wp-config.php constants
        // for credentials (the form renders masked dots when constants are defined)
        $settings = [
            'bucket' => sanitize_text_field($_POST['bucket'] ?? ''),
            'access_key' => $this->settings_manager->is_constant_defined('access_key')
                ? $this->settings_manager->get('access_key')
                : sanitize_text_field($_POST['access_key'] ?? ''),
            'access_secret' => $this->settings_manager->is_constant_defined('access_secret')
                ? $this->settings_manager->get('access_secret')
                : sanitize_text_field($_POST['access_secret'] ?? ''),
            'region' => sanitize_text_field($_POST['region'] ?? ''),
            'endpoint' => esc_url_raw($_POST['endpoint'] ?? ''),
        ];

        // Validate required fields
        if (empty($settings['bucket']) || empty($settings['access_key']) ||
            empty($settings['access_secret']) || empty($settings['region']) ||
            empty($settings['endpoint'])) {
            wp_send_json_error([
                'message' => 'All connection fields are required for testing.',
            ]);
        }

        // Test the connection
        $result = $this->test_connection($settings);

        if ($result['success']) {
            wp_send_json_success([
                'message' => $result['message'],
            ]);
        } else {
            wp_send_json_error([
                'message' => $result['message'],
            ]);
        }
    }

    /**
     * Test connection to Digital Ocean Spaces
     *
     * @param array $settings Connection settings
     * @return array Array with 'success' (bool) and 'message' (string)
     */
    private function test_connection($settings) {
        try {
            // Initialize S3Client
            $s3_client = new S3Client([
                'version' => 'latest',
                'region' => $settings['region'],
                'endpoint' => $settings['endpoint'],
                'credentials' => [
                    'key' => $settings['access_key'],
                    'secret' => $settings['access_secret'],
                ],
                'use_path_style_endpoint' => false,
                'http' => [
                    'timeout' => 10,
                    'connect_timeout' => 10,
                ],
            ]);

            // Test 1: Check if bucket exists and credentials are valid
            $s3_client->headBucket([
                'Bucket' => $settings['bucket'],
            ]);

            // Test 2: Try to upload a test file
            $test_key = 'test-connection-' . time() . '.txt';
            $s3_client->putObject([
                'Bucket' => $settings['bucket'],
                'Key' => $test_key,
                'Body' => 'Connection test from Digital Ocean Spaces Integration plugin',
                'ContentType' => 'text/plain',
            ]);

            // Test 3: Delete the test file
            $s3_client->deleteObject([
                'Bucket' => $settings['bucket'],
                'Key' => $test_key,
            ]);

            return [
                'success' => true,
                'message' => 'Connection successful! Bucket is accessible and credentials are valid.',
            ];

        } catch (AwsException $e) {
            $error_code = $e->getAwsErrorCode();
            $error_message = $e->getAwsErrorMessage();

            // Provide user-friendly error messages
            switch ($error_code) {
                case 'NoSuchBucket':
                    $message = 'Bucket does not exist. Please check the bucket name.';
                    break;
                case 'InvalidAccessKeyId':
                    $message = 'Invalid access key. Please check your credentials.';
                    break;
                case 'SignatureDoesNotMatch':
                    $message = 'Invalid secret key. Please check your credentials.';
                    break;
                case 'AccessDenied':
                    $message = 'Access denied. Please check your bucket permissions and credentials.';
                    break;
                default:
                    $message = 'Connection failed: ' . $error_message;
                    break;
            }

            error_log('DO Spaces Connection Test Failed: ' . $error_code . ' - ' . $error_message);

            return [
                'success' => false,
                'message' => $message,
            ];

        } catch (\Exception $e) {
            error_log('DO Spaces Connection Test Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }
}
