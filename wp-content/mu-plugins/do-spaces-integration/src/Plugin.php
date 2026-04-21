<?php

namespace DOSpaces\Integration;

use DOSpaces\Integration\Settings\SettingsManager;
use DOSpaces\Integration\Admin\SettingsPage;
use DOSpaces\Integration\Admin\ConnectionTest;
use DOSpaces\Integration\Admin\MediaMigration;
use DOSpaces\Integration\Admin\MetadataRepair;
use DOSpaces\Integration\Upload\S3ClientFactory;
use DOSpaces\Integration\Upload\UploadHandler;
use DOSpaces\Integration\Upload\PathManager;
use DOSpaces\Integration\Logger\Logger;
use DOSpaces\Integration\CDN\URLRewriter;
use DOSpaces\Integration\Integrations\EnableMediaReplace;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Plugin Class
 * Handles plugin initialization and component management
 */
class Plugin {

    /**
     * Plugin instance
     *
     * @var Plugin|null
     */
    private static $instance = null;

    /**
     * Settings Manager instance
     *
     * @var SettingsManager
     */
    private $settings_manager;

    /**
     * Logger instance
     *
     * @var Logger
     */
    private $logger;

    /**
     * Settings Page instance
     *
     * @var SettingsPage
     */
    private $settings_page;

    /**
     * Connection Test instance
     *
     * @var ConnectionTest
     */
    private $connection_test;

    /**
     * S3 Client Factory instance
     *
     * @var S3ClientFactory
     */
    private $s3_factory;

    /**
     * Upload Handler instance
     *
     * @var UploadHandler
     */
    private $upload_handler;

    /**
     * Path Manager instance
     *
     * @var PathManager
     */
    private $path_manager;

    /**
     * URL Rewriter instance
     *
     * @var URLRewriter
     */
    private $url_rewriter;

    /**
     * Media Migration instance
     *
     * @var MediaMigration
     */
    private $media_migration;

    /**
     * Metadata Repair instance
     *
     * @var MetadataRepair
     */
    private $metadata_repair;

    /**
     * Get plugin instance (Singleton pattern)
     *
     * @return Plugin
     */
    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        // Constructor is private for Singleton pattern
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Check dependencies
        if (!$this->check_dependencies()) {
            return;
        }

        // Initialize components
        $this->load_components();

        // Register hooks
        $this->register_hooks();
    }

    /**
     * Check plugin dependencies
     *
     * @return bool True if all dependencies are met
     */
    private function check_dependencies() {
        // Check if AWS SDK is loaded
        if (!class_exists('Aws\S3\S3Client')) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>';
                echo '<strong>Digital Ocean Spaces Integration:</strong> AWS SDK not found. ';
                echo 'Please run <code>composer install</code> in the plugin directory.';
                echo '</p></div>';
            });
            return false;
        }

        return true;
    }

    /**
     * Load and initialize all components
     */
    private function load_components() {
        // Initialize Settings Manager
        $this->settings_manager = new SettingsManager();

        // Initialize Logger
        $this->logger = new Logger($this->settings_manager);

        // Initialize shared S3 client factory
        $this->s3_factory = new S3ClientFactory($this->settings_manager, $this->logger);

        // Initialize Admin components
        $this->settings_page = new SettingsPage($this->settings_manager);
        $this->connection_test = new ConnectionTest($this->settings_manager);
        $this->media_migration = new MediaMigration($this->settings_manager, $this->logger, $this->s3_factory);
        $this->metadata_repair = new MetadataRepair($this->settings_manager, $this->logger, $this->s3_factory);

        // Initialize Upload components
        $this->upload_handler = new UploadHandler($this->settings_manager, $this->logger, $this->s3_factory);
        $this->path_manager = new PathManager($this->settings_manager);

        // Initialize CDN URL rewriter
        $this->url_rewriter = new URLRewriter($this->settings_manager);

        // Initialize third-party integrations
        $emr = new EnableMediaReplace($this->settings_manager, $this->logger, $this->s3_factory);
        if ($emr->is_active()) {
            $emr->init();
        }
    }

    /**
     * Register WordPress hooks
     */
    private function register_hooks() {
        // Initialize admin components
        if (is_admin()) {
            $this->settings_page->init();
            $this->connection_test->init();
            $this->media_migration->init();
            $this->metadata_repair->init();
        }

        // Initialize upload components (frontend and admin)
        $this->upload_handler->init();
        $this->path_manager->init();

        // Initialize URL rewriter (frontend and admin)
        $this->url_rewriter->init();

        // Add plugin action links
        add_filter('plugin_action_links', [$this, 'add_action_links'], 10, 2);
    }

    /**
     * Add action links to plugins page
     *
     * @param array $links Existing links
     * @param string $file Plugin file
     * @return array Modified links
     */
    public function add_action_links($links, $file) {
        // For mu-plugins, this won't show up in the standard plugins page
        // but we include it for potential future use
        if ($file === plugin_basename(DO_SPACES_FILE)) {
            $settings_link = '<a href="' . admin_url('options-general.php?page=do-spaces-settings') . '">Settings</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * Get Settings Manager instance
     *
     * @return SettingsManager
     */
    public function get_settings_manager() {
        return $this->settings_manager;
    }

    /**
     * Get Upload Handler instance
     *
     * @return UploadHandler
     */
    public function get_upload_handler() {
        return $this->upload_handler;
    }

    /**
     * Get Path Manager instance
     *
     * @return PathManager
     */
    public function get_path_manager() {
        return $this->path_manager;
    }
}
