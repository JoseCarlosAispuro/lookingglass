<?php

namespace DOSpaces\Integration\Settings;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings Manager
 * Handles storage, retrieval, validation, and sanitization of plugin settings
 */
class SettingsManager {

    /**
     * Option key for storing settings in wp_options
     */
    const OPTION_KEY = 'do_spaces_settings';

    /**
     * Map of setting keys to wp-config.php constant names
     */
    const CONSTANT_MAP = [
        'access_key'    => 'DO_SPACES_ACCESS_KEY',
        'access_secret' => 'DO_SPACES_ACCESS_SECRET',
    ];

    /**
     * Default settings
     *
     * @var array
     */
    private $defaults = [
        'enabled' => false,
        'bucket' => '',
        'access_key' => '',
        'access_secret' => '',
        'region' => 'nyc3',
        'endpoint' => 'https://nyc3.digitaloceanspaces.com',
        'path_prefix' => '',
        'keep_local' => false,
        'debug_mode' => false,
        'use_cdn' => false,
        'cdn_endpoint' => '',
    ];

    /**
     * Check if a setting is defined as a wp-config.php constant
     *
     * @param string $key Setting key (e.g., 'access_key')
     * @return bool
     */
    public function is_constant_defined($key) {
        if (!isset(self::CONSTANT_MAP[$key])) {
            return false;
        }
        return defined(self::CONSTANT_MAP[$key]);
    }

    /**
     * Get values from wp-config.php constants
     *
     * @return array Key-value pairs for any defined constants
     */
    public function get_constant_overrides() {
        $overrides = [];
        foreach (self::CONSTANT_MAP as $key => $constant) {
            if (defined($constant)) {
                $overrides[$key] = constant($constant);
            }
        }
        return $overrides;
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public function get_settings() {
        $settings = get_option(self::OPTION_KEY, []);

        // Merge with defaults to ensure all keys exist
        $settings = wp_parse_args($settings, $this->defaults);

        // Constants override database values
        return array_merge($settings, $this->get_constant_overrides());
    }

    /**
     * Update settings
     *
     * @param array $data Raw settings data
     * @return bool True on success, false on failure
     */
    public function update_settings($data) {
        // Inject constant values for validation, but strip them before saving
        $constant_overrides = $this->get_constant_overrides();
        $data_for_validation = array_merge($data, $constant_overrides);

        $sanitized = $this->sanitize_settings($data_for_validation);
        $validated = $this->validate_settings($sanitized);

        if (is_wp_error($validated)) {
            return $validated;
        }

        // Strip constant-defined fields — never store secrets in DB when constants provide them
        foreach (self::CONSTANT_MAP as $key => $constant) {
            if (defined($constant)) {
                unset($sanitized[$key]);
            }
        }

        return update_option(self::OPTION_KEY, $sanitized, false);
    }

    /**
     * Get a specific setting
     *
     * @param string $key Setting key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public function get($key, $default = null) {
        $settings = $this->get_settings();

        if (isset($settings[$key])) {
            return $settings[$key];
        }

        return $default !== null ? $default : (isset($this->defaults[$key]) ? $this->defaults[$key] : null);
    }

    /**
     * Check if plugin is enabled
     *
     * @return bool
     */
    public function is_enabled() {
        return (bool) $this->get('enabled', false);
    }

    /**
     * Check if plugin is properly configured
     *
     * @return bool
     */
    public function is_configured() {
        $settings = $this->get_settings();

        // Required fields
        $required = ['bucket', 'access_key', 'access_secret', 'region', 'endpoint'];

        foreach ($required as $field) {
            if (empty($settings[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate settings
     *
     * @param array $data Settings data
     * @return bool|\WP_Error True if valid, WP_Error if invalid
     */
    private function validate_settings($data) {
        $errors = new \WP_Error();

        // If enabled, validate required fields
        if (!empty($data['enabled'])) {
            if (empty($data['bucket'])) {
                $errors->add('bucket', 'Bucket name is required when plugin is enabled.');
            } elseif (!preg_match('/^[a-z0-9][a-z0-9-]*[a-z0-9]$/', $data['bucket'])) {
                $errors->add('bucket', 'Bucket name must contain only lowercase letters, numbers, and hyphens.');
            }

            if (empty($data['access_key'])) {
                $errors->add('access_key', 'Access key is required when plugin is enabled.');
            }

            if (empty($data['access_secret'])) {
                $errors->add('access_secret', 'Access secret is required when plugin is enabled.');
            }

            if (empty($data['region'])) {
                $errors->add('region', 'Region is required when plugin is enabled.');
            }

            if (empty($data['endpoint'])) {
                $errors->add('endpoint', 'Endpoint URL is required when plugin is enabled.');
            } elseif (!filter_var($data['endpoint'], FILTER_VALIDATE_URL)) {
                $errors->add('endpoint', 'Endpoint must be a valid URL.');
            }
        }

        // Validate path prefix format if provided
        if (!empty($data['path_prefix'])) {
            // Remove leading slash if present
            $data['path_prefix'] = ltrim($data['path_prefix'], '/');

            // Check for invalid characters
            if (preg_match('/[^a-zA-Z0-9\/_-]/', $data['path_prefix'])) {
                $errors->add('path_prefix', 'Path prefix contains invalid characters. Use only letters, numbers, hyphens, underscores, and forward slashes.');
            }
        }

        // Validate CDN settings
        if (!empty($data['use_cdn'])) {
            // CDN endpoint is optional - will be auto-generated if empty
            if (!empty($data['cdn_endpoint']) && !filter_var($data['cdn_endpoint'], FILTER_VALIDATE_URL)) {
                $errors->add('cdn_endpoint', 'CDN endpoint must be a valid URL if provided.');
            }
        }

        if ($errors->has_errors()) {
            return $errors;
        }

        return true;
    }

    /**
     * Sanitize settings
     *
     * @param array $data Raw settings data
     * @return array Sanitized settings
     */
    private function sanitize_settings($data) {
        return [
            'enabled' => !empty($data['enabled']),
            'bucket' => sanitize_text_field($data['bucket'] ?? ''),
            'access_key' => sanitize_text_field($data['access_key'] ?? ''),
            'access_secret' => sanitize_text_field($data['access_secret'] ?? ''),
            'region' => sanitize_text_field($data['region'] ?? 'us-east-1'),
            'endpoint' => esc_url_raw($data['endpoint'] ?? ''),
            'path_prefix' => trim(sanitize_text_field($data['path_prefix'] ?? ''), '/'),
            'keep_local' => !empty($data['keep_local']),
            'debug_mode' => !empty($data['debug_mode']),
            'use_cdn' => !empty($data['use_cdn']),
            'cdn_endpoint' => esc_url_raw($data['cdn_endpoint'] ?? ''),
        ];
    }
}
