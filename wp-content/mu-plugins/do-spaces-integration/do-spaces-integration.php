<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DO_SPACES_VERSION', '1.1.0');
define('DO_SPACES_PATH', __DIR__ . '/');
define('DO_SPACES_URL', plugin_dir_url(__FILE__));
define('DO_SPACES_FILE', __FILE__);

// Check PHP version
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>Digital Ocean Spaces Integration:</strong> This plugin requires PHP 7.4 or higher. ';
        echo 'You are running PHP ' . PHP_VERSION . '.';
        echo '</p></div>';
    });
    return;
}

// Check if Composer autoloader exists
$autoloader = DO_SPACES_PATH . 'vendor/autoload.php';
if (!file_exists($autoloader)) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>Digital Ocean Spaces Integration:</strong> Composer dependencies are not installed. ';
        echo 'Please run <code>composer install</code> in the plugin directory: ';
        echo '<code>' . esc_html(DO_SPACES_PATH) . '</code>';
        echo '</p></div>';
    });
    return;
}

// Load Composer autoloader
require_once $autoloader;

// Initialize the plugin
add_action('plugins_loaded', function() {
    try {
        \DOSpaces\Integration\Plugin::instance()->init();
    } catch (\Exception $e) {
        add_action('admin_notices', function() use ($e) {
            echo '<div class="notice notice-error"><p>';
            echo '<strong>Digital Ocean Spaces Integration Error:</strong> ';
            echo esc_html($e->getMessage());
            echo '</p></div>';
        });
        error_log('DO Spaces Integration Error: ' . $e->getMessage());
    }
}, 20);
