<?php

namespace DOSpaces\Integration\Logger;

use DOSpaces\Integration\Settings\SettingsManager;
use Aws\Exception\AwsException;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Logger
 * Handles logging for Digital Ocean Spaces operations
 */
class Logger {

    /**
     * Log levels
     */
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';

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
     * Log debug message
     *
     * @param string $message
     * @param array $context
     */
    public function debug($message, $context = []) {
        if ($this->is_debug_enabled()) {
            $this->log(self::LEVEL_DEBUG, $message, $context);
        }
    }

    /**
     * Log info message
     *
     * @param string $message
     * @param array $context
     */
    public function info($message, $context = []) {
        if ($this->is_debug_enabled()) {
            $this->log(self::LEVEL_INFO, $message, $context);
        }
    }

    /**
     * Log warning message
     *
     * @param string $message
     * @param array $context
     */
    public function warning($message, $context = []) {
        $this->log(self::LEVEL_WARNING, $message, $context);
    }

    /**
     * Log error message
     *
     * @param string $message
     * @param array $context
     */
    public function error($message, $context = []) {
        $this->log(self::LEVEL_ERROR, $message, $context);
    }

    /**
     * Log AWS Exception with detailed information
     *
     * @param AwsException $exception
     * @param string $operation
     * @param array $context
     */
    public function log_aws_exception(AwsException $exception, $operation, $context = []) {
        $error_code = $exception->getAwsErrorCode();
        $error_message = $exception->getAwsErrorMessage();
        $status_code = $exception->getStatusCode();

        $aws_context = array_merge($context, [
            'error_code' => $error_code,
            'error_message' => $error_message,
            'status_code' => $status_code,
            'operation' => $operation,
        ]);

        // Get request ID if available
        if (method_exists($exception, 'getAwsRequestId')) {
            $aws_context['request_id'] = $exception->getAwsRequestId();
        }

        $this->error(
            sprintf('AWS Error during %s: [%s] %s', $operation, $error_code, $error_message),
            $aws_context
        );
    }

    /**
     * Core logging method
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function log($level, $message, $context = []) {
        $timestamp = current_time('Y-m-d H:i:s');

        // Format context as JSON if not empty
        $context_string = '';
        if (!empty($context)) {
            $context_string = ' | Context: ' . json_encode($context, JSON_UNESCAPED_SLASHES);
        }

        // Build log message
        $log_message = sprintf(
            '[%s] [%s] DO Spaces: %s%s',
            $timestamp,
            $level,
            $message,
            $context_string
        );

        // Log to PHP error log
        error_log($log_message);

        // For ERROR level, also create admin notice
        if ($level === self::LEVEL_ERROR && is_admin()) {
            $this->create_admin_notice($message, $context);
        }
    }

    /**
     * Create WordPress admin notice for errors
     *
     * @param string $message
     * @param array $context
     */
    private function create_admin_notice($message, $context) {
        add_action('admin_notices', function() use ($message, $context) {
            $display_message = $message;

            // Add filename to message if available in context
            if (isset($context['file'])) {
                $display_message .= ' (File: ' . basename($context['file']) . ')';
            }

            echo '<div class="notice notice-error"><p>';
            echo '<strong>Digital Ocean Spaces Error:</strong> ';
            echo esc_html($display_message);
            echo '</p></div>';
        });
    }

    /**
     * Check if debug logging is enabled
     *
     * @return bool
     */
    private function is_debug_enabled() {
        return (bool) $this->settings_manager->get('debug_mode', false);
    }
}
