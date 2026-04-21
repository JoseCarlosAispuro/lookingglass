<?php

namespace DOSpaces\Integration\Upload;

use DOSpaces\Integration\Settings\SettingsManager;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Path Manager
 * Handles URL and path management for Spaces integration
 */
class PathManager {

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
        add_filter('upload_dir', [$this, 'filter_upload_dir'], 10);
        add_filter('wp_get_attachment_url', [$this, 'filter_attachment_url'], 10, 2);
    }

    /**
     * Filter upload directory to use Spaces URLs
     *
     * @param array $uploads Upload directory data
     * @return array Modified upload directory data
     */
    public function filter_upload_dir($uploads) {
        // Only modify if plugin is enabled and configured
        if (!$this->settings_manager->is_enabled() || !$this->settings_manager->is_configured()) {
            return $uploads;
        }

        $spaces_url = $this->get_spaces_base_url();

        // Modify the URLs to point to Spaces
        $uploads['url'] = $spaces_url . $uploads['subdir'];
        $uploads['baseurl'] = $spaces_url;

        return $uploads;
    }

    /**
     * Filter individual attachment URLs to use Spaces
     *
     * @param string $url Attachment URL
     * @param int $post_id Attachment post ID
     * @return string Modified URL
     */
    public function filter_attachment_url($url, $post_id) {
        // Only modify if plugin is enabled and configured
        if (!$this->settings_manager->is_enabled() || !$this->settings_manager->is_configured()) {
            return $url;
        }

        // Get the attached file path (relative to uploads directory)
        $file = get_post_meta($post_id, '_wp_attached_file', true);

        if (!$file) {
            return $url;
        }

        // Build Spaces URL
        $spaces_url = $this->get_spaces_base_url() . '/' . $file;

        return $spaces_url;
    }

    /**
     * Get the base Spaces URL
     *
     * @return string Base URL for Spaces bucket
     */
    private function get_spaces_base_url() {
        $settings = $this->settings_manager->get_settings();

        // Extract hostname from endpoint
        $endpoint = rtrim($settings['endpoint'], '/');
        $endpoint_host = str_replace(['https://', 'http://'], '', $endpoint);

        // Build Spaces URL: https://bucket.region.digitaloceanspaces.com
        $url = 'https://' . $settings['bucket'] . '.' . $endpoint_host;

        // Add path prefix if configured
        if (!empty($settings['path_prefix'])) {
            $url .= '/' . trim($settings['path_prefix'], '/');
        }

        return $url;
    }

    /**
     * Get full Spaces URL for a file
     *
     * @param string $relative_path Relative file path
     * @return string Full Spaces URL
     */
    public function get_spaces_url($relative_path) {
        return $this->get_spaces_base_url() . '/' . ltrim($relative_path, '/');
    }
}
