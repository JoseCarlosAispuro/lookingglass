<?php

namespace DOSpaces\Integration\CDN;

use DOSpaces\Integration\Settings\SettingsManager;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * URL Rewriter
 * Handles CDN URL replacement for uploaded files
 */
class URLRewriter {

    /**
     * Settings Manager instance
     *
     * @var SettingsManager
     */
    private $settings_manager;

    /**
     * Spaces base URL (API endpoint)
     *
     * @var string|null
     */
    private $spaces_base_url = null;

    /**
     * CDN base URL
     *
     * @var string|null
     */
    private $cdn_base_url = null;

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
        // Only hook if CDN is enabled
        if (!$this->is_cdn_enabled()) {
            return;
        }

        // Build URLs once
        $this->build_urls();

        // WordPress attachment URLs
        add_filter('wp_get_attachment_url', [$this, 'rewrite_url'], 10, 1);
        add_filter('wp_get_attachment_image_src', [$this, 'rewrite_image_src'], 10, 1);

        // WordPress content (images in post content)
        add_filter('the_content', [$this, 'rewrite_content_urls'], 10, 1);
        add_filter('post_thumbnail_html', [$this, 'rewrite_content_urls'], 10, 1);

        // REST API
        add_filter('rest_prepare_attachment', [$this, 'rewrite_rest_response'], 10, 1);

        // WPGraphQL (if installed) — use 9 args to get field info for targeted rewriting
        if (class_exists('WPGraphQL')) {
            add_filter('graphql_resolve_field', [$this, 'rewrite_graphql_field'], 10, 9);
        }
    }

    /**
     * Check if CDN is enabled
     *
     * @return bool
     */
    private function is_cdn_enabled() {
        return $this->settings_manager->is_enabled()
            && $this->settings_manager->get('use_cdn', false);
    }

    /**
     * Build Spaces and CDN base URLs
     */
    private function build_urls() {
        $settings = $this->settings_manager->get_settings();

        // Build Spaces base URL (what's stored in DB)
        $endpoint = rtrim($settings['endpoint'], '/');
        $endpoint_host = str_replace(['https://', 'http://'], '', $endpoint);
        $this->spaces_base_url = 'https://' . $settings['bucket'] . '.' . $endpoint_host;

        if (!empty($settings['path_prefix'])) {
            $this->spaces_base_url .= '/' . trim($settings['path_prefix'], '/');
        }

        // Build CDN base URL (what we want to serve)
        $cdn_endpoint = $settings['cdn_endpoint'];

        // If CDN endpoint is empty, auto-generate from API endpoint
        if (empty($cdn_endpoint)) {
            $cdn_endpoint = str_replace('.digitaloceanspaces.com', '.cdn.digitaloceanspaces.com', $endpoint);
        }

        $cdn_endpoint = rtrim($cdn_endpoint, '/');
        $cdn_endpoint_host = str_replace(['https://', 'http://'], '', $cdn_endpoint);
        $this->cdn_base_url = 'https://' . $settings['bucket'] . '.' . $cdn_endpoint_host;

        if (!empty($settings['path_prefix'])) {
            $this->cdn_base_url .= '/' . trim($settings['path_prefix'], '/');
        }
    }

    /**
     * Rewrite a single URL
     *
     * @param string $url
     * @return string
     */
    public function rewrite_url($url) {
        if (empty($url) || !is_string($url)) {
            return $url;
        }

        // Only rewrite if URL contains our Spaces base URL
        if (strpos($url, $this->spaces_base_url) === 0) {
            return str_replace($this->spaces_base_url, $this->cdn_base_url, $url);
        }

        return $url;
    }

    /**
     * Rewrite image src array
     *
     * @param array|false $image
     * @return array|false
     */
    public function rewrite_image_src($image) {
        if (is_array($image) && isset($image[0])) {
            $image[0] = $this->rewrite_url($image[0]);
        }

        return $image;
    }

    /**
     * Rewrite URLs in content
     *
     * @param string $content
     * @return string
     */
    public function rewrite_content_urls($content) {
        if (empty($content) || !is_string($content)) {
            return $content;
        }

        return str_replace($this->spaces_base_url, $this->cdn_base_url, $content);
    }

    /**
     * Rewrite REST API response
     *
     * @param \WP_REST_Response $response
     * @return \WP_REST_Response
     */
    public function rewrite_rest_response($response) {
        $data = $response->get_data();

        // Rewrite source_url
        if (isset($data['source_url'])) {
            $data['source_url'] = $this->rewrite_url($data['source_url']);
        }

        // Rewrite media_details sizes
        if (isset($data['media_details']['sizes']) && is_array($data['media_details']['sizes'])) {
            foreach ($data['media_details']['sizes'] as $size => $size_data) {
                if (isset($size_data['source_url'])) {
                    $data['media_details']['sizes'][$size]['source_url'] =
                        $this->rewrite_url($size_data['source_url']);
                }
            }
        }

        $response->set_data($data);
        return $response;
    }

    /**
     * Field names known to contain media URLs in WPGraphQL
     */
    private static $media_url_fields = [
        'sourceUrl', 'mediaItemUrl', 'link', 'url', 'href',
        'src', 'guid', 'featuredImageUrl',
    ];

    /**
     * Type names known to contain media URLs in WPGraphQL
     */
    private static $media_type_names = [
        'MediaItem', 'MediaDetails', 'MediaSize',
    ];

    /**
     * Rewrite GraphQL field values — only for media-related fields/types
     *
     * @param mixed $value
     * @param mixed $source
     * @param mixed $args
     * @param mixed $context
     * @param mixed $info
     * @param mixed $type_name
     * @param mixed $field_key
     * @param mixed $field
     * @param mixed $field_resolver
     * @return mixed
     */
    public function rewrite_graphql_field($value, $source = null, $args = null, $context = null, $info = null, $type_name = null, $field_key = null, $field = null, $field_resolver = null) {
        // Skip non-string/non-array values immediately (integers, booleans, nulls)
        if (!is_string($value) && !is_array($value)) {
            return $value;
        }

        // Only process fields on media-related types or known URL field names
        $is_media_type = $type_name && in_array($type_name, self::$media_type_names, true);
        $is_url_field = $field_key && in_array($field_key, self::$media_url_fields, true);

        if (!$is_media_type && !$is_url_field) {
            return $value;
        }

        if (is_string($value)) {
            return $this->rewrite_url($value);
        }

        if (is_array($value)) {
            array_walk_recursive($value, function(&$item) {
                if (is_string($item)) {
                    $item = $this->rewrite_url($item);
                }
            });
        }

        return $value;
    }
}
