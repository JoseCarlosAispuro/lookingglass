<?php

namespace DOSpaces\Integration\Admin;

use DOSpaces\Integration\Settings\SettingsManager;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings Page
 * Handles the admin settings page UI and form submission
 */
class SettingsPage {

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
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'process_form']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Register settings page in admin menu
     */
    public function register_menu() {
        add_options_page(
            'Digital Ocean Spaces',
            'DO Spaces',
            'manage_options',
            'do-spaces-settings',
            [$this, 'render_page']
        );
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_assets($hook) {
        // Only load on our settings page
        if ($hook !== 'settings_page_do-spaces-settings') {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'do-spaces-admin',
            DO_SPACES_URL . 'assets/css/admin.css',
            [],
            DO_SPACES_VERSION
        );

        // Enqueue JS
        wp_enqueue_script(
            'do-spaces-admin',
            DO_SPACES_URL . 'assets/js/admin.js',
            ['jquery'],
            DO_SPACES_VERSION,
            true
        );

        // Localize script with AJAX data
        wp_localize_script('do-spaces-admin', 'doSpacesAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('do_spaces_admin'),
            'strings' => [
                'testing' => __('Testing connection...', 'do-spaces'),
                'success' => __('Connection successful!', 'do-spaces'),
                'error' => __('Connection failed: ', 'do-spaces'),
            ],
        ]);

        // Enqueue migration JS
        wp_enqueue_script(
            'do-spaces-migration',
            DO_SPACES_URL . 'assets/js/migration.js',
            ['jquery', 'do-spaces-admin'],
            DO_SPACES_VERSION,
            true
        );

        // Localize migration script
        wp_localize_script('do-spaces-migration', 'doSpacesMigration', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('do_spaces_admin'),
        ]);

        // Enqueue repair JS
        wp_enqueue_script(
            'do-spaces-repair',
            DO_SPACES_URL . 'assets/js/repair.js',
            ['jquery', 'do-spaces-admin'],
            DO_SPACES_VERSION,
            true
        );

        // Localize repair script
        wp_localize_script('do-spaces-repair', 'doSpacesRepair', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('do_spaces_admin'),
        ]);
    }

    /**
     * Process form submission
     */
    public function process_form() {
        // Check if form was submitted
        if (!isset($_POST['do_spaces_submit'])) {
            return;
        }

        // Verify nonce
        if (!isset($_POST['do_spaces_nonce']) || !wp_verify_nonce($_POST['do_spaces_nonce'], 'do_spaces_save_settings')) {
            add_settings_error('do_spaces', 'nonce_failed', 'Security check failed. Please try again.');
            return;
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            add_settings_error('do_spaces', 'unauthorized', 'You do not have permission to modify these settings.');
            return;
        }

        // Prepare settings data, skipping constant-defined fields
        $data = [
            'enabled' => isset($_POST['enabled']) ? 1 : 0,
            'bucket' => $_POST['bucket'] ?? '',
            'region' => $_POST['region'] ?? 'us-east-1',
            'endpoint' => $_POST['endpoint'] ?? '',
            'path_prefix' => $_POST['path_prefix'] ?? '',
            'keep_local' => isset($_POST['keep_local']) ? 1 : 0,
            'debug_mode' => isset($_POST['debug_mode']) ? 1 : 0,
            'use_cdn' => isset($_POST['use_cdn']) ? 1 : 0,
            'cdn_endpoint' => $_POST['cdn_endpoint'] ?? '',
        ];

        if (!$this->settings_manager->is_constant_defined('access_key')) {
            $data['access_key'] = $_POST['access_key'] ?? '';
        }
        if (!$this->settings_manager->is_constant_defined('access_secret')) {
            $data['access_secret'] = $_POST['access_secret'] ?? '';
        }

        // Update settings
        $result = $this->settings_manager->update_settings($data);

        if (is_wp_error($result)) {
            foreach ($result->get_error_messages() as $message) {
                add_settings_error('do_spaces', 'validation_error', $message);
            }
        } else {
            add_settings_error('do_spaces', 'settings_saved', 'Settings saved successfully.', 'success');
        }
    }

    /**
     * Render settings page
     */
    public function render_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $settings = $this->settings_manager->get_settings();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <?php settings_errors('do_spaces'); ?>

            <form method="post" action="">
                <?php wp_nonce_field('do_spaces_save_settings', 'do_spaces_nonce'); ?>

                <table class="form-table" role="presentation">
                    <!-- Enable/Disable Toggle -->
                    <tr>
                        <th scope="row">
                            <label for="enabled">Enable Integration</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="enabled" id="enabled" value="1" <?php checked($settings['enabled'], true); ?>>
                                Enable Digital Ocean Spaces integration
                            </label>
                            <p class="description">
                                When disabled, uploads will use local WordPress storage. Settings are preserved when disabled.
                            </p>
                        </td>
                    </tr>

                    <!-- Connection Settings Section -->
                    <tr>
                        <th colspan="2">
                            <h2>Connection Settings</h2>
                        </th>
                    </tr>

                    <!-- Bucket Name -->
                    <tr>
                        <th scope="row">
                            <label for="bucket">Bucket Name <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" name="bucket" id="bucket" value="<?php echo esc_attr($settings['bucket']); ?>" class="regular-text" required>
                            <p class="description">
                                Your Digital Ocean Spaces bucket name (e.g., "my-wordpress-media")
                            </p>
                        </td>
                    </tr>

                    <!-- Access Key -->
                    <tr>
                        <th scope="row">
                            <label for="access_key">Access Key <span class="required">*</span></label>
                        </th>
                        <td>
                            <?php if ($this->settings_manager->is_constant_defined('access_key')): ?>
                                <input type="text" id="access_key" value="<?php echo esc_attr(str_repeat('•', 12)); ?>" class="regular-text" readonly>
                                <p class="description">
                                    <span class="dashicons dashicons-lock" style="font-size: 14px; width: 14px; height: 14px; vertical-align: text-top;"></span>
                                    Defined in <code>wp-config.php</code> via <code>DO_SPACES_ACCESS_KEY</code>
                                </p>
                            <?php else: ?>
                                <input type="text" name="access_key" id="access_key" value="<?php echo esc_attr($settings['access_key']); ?>" class="regular-text" required>
                                <p class="description">
                                    Your Digital Ocean Spaces access key
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Access Secret -->
                    <tr>
                        <th scope="row">
                            <label for="access_secret">Access Secret <span class="required">*</span></label>
                        </th>
                        <td>
                            <?php if ($this->settings_manager->is_constant_defined('access_secret')): ?>
                                <input type="text" id="access_secret" value="<?php echo esc_attr(str_repeat('•', 12)); ?>" class="regular-text" readonly>
                                <p class="description">
                                    <span class="dashicons dashicons-lock" style="font-size: 14px; width: 14px; height: 14px; vertical-align: text-top;"></span>
                                    Defined in <code>wp-config.php</code> via <code>DO_SPACES_ACCESS_SECRET</code>
                                </p>
                            <?php else: ?>
                                <input type="password" name="access_secret" id="access_secret" value="<?php echo esc_attr($settings['access_secret']); ?>" class="regular-text" required>
                                <p class="description">
                                    Your Digital Ocean Spaces secret key
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Region -->
                    <tr>
                        <th scope="row">
                            <label for="region">Region <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" name="region" id="region" value="<?php echo esc_attr($settings['region']); ?>" class="regular-text" required>
                            <p class="description">
                                Region identifier (e.g., "nyc3", "sfo3", "ams3", "sgp1")
                            </p>
                        </td>
                    </tr>

                    <!-- Endpoint URL -->
                    <tr>
                        <th scope="row">
                            <label for="endpoint">Endpoint URL <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="url" name="endpoint" id="endpoint" value="<?php echo esc_attr($settings['endpoint']); ?>" class="regular-text" required>
                            <p class="description">
                                Digital Ocean Spaces endpoint URL (e.g., "https://nyc3.digitaloceanspaces.com")
                            </p>
                        </td>
                    </tr>

                    <!-- Test Connection Button -->
                    <tr>
                        <th scope="row"></th>
                        <td>
                            <button type="button" id="test-connection-btn" class="button button-secondary">
                                Test Connection
                            </button>
                            <span id="connection-status" class="connection-status"></span>
                        </td>
                    </tr>

                    <!-- Upload Settings Section -->
                    <tr>
                        <th colspan="2">
                            <h2>Upload Settings</h2>
                        </th>
                    </tr>

                    <!-- Path Prefix -->
                    <tr>
                        <th scope="row">
                            <label for="path_prefix">Path Prefix</label>
                        </th>
                        <td>
                            <input type="text" name="path_prefix" id="path_prefix" value="<?php echo esc_attr($settings['path_prefix']); ?>" class="regular-text">
                            <p class="description">
                                Optional path prefix for organizing files in your bucket (e.g., "wp-uploads/" or "sites/example/")
                            </p>
                            <p class="description" style="color: #d63638;">
                                <strong>Warning:</strong> Changing this after files have been uploaded will break existing media URLs.
                                If you need to change the prefix, first rename the folder in your Digital Ocean Spaces bucket to match the new prefix.
                            </p>
                        </td>
                    </tr>

                    <!-- Keep Local Backup -->
                    <tr>
                        <th scope="row">
                            <label for="keep_local">Local Backup</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="keep_local" id="keep_local" value="1" <?php checked($settings['keep_local'], true); ?>>
                                Keep local copies of uploaded files
                            </label>
                            <p class="description">
                                When enabled, files are uploaded to Spaces AND kept locally in wp-content/uploads/
                            </p>
                        </td>
                    </tr>

                    <!-- Debug Mode -->
                    <tr>
                        <th scope="row">
                            <label for="debug_mode">Debug Logging</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="debug_mode" id="debug_mode" value="1" <?php checked($settings['debug_mode'], true); ?>>
                                Enable debug logging
                            </label>
                            <p class="description">
                                Log detailed information about uploads for troubleshooting. Check your PHP error log for details (usually wp-content/debug.log).
                            </p>
                        </td>
                    </tr>

                    <!-- CDN Settings Section -->
                    <tr>
                        <th colspan="2">
                            <h2>CDN Settings (Optional)</h2>
                        </th>
                    </tr>

                    <!-- Use CDN -->
                    <tr>
                        <th scope="row">
                            <label for="use_cdn">Enable CDN</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="use_cdn" id="use_cdn" value="1" <?php checked($settings['use_cdn'], true); ?>>
                                Serve files via CDN endpoint
                            </label>
                            <p class="description">
                                When enabled, file URLs will use the CDN endpoint for faster delivery. Files are still uploaded via the API endpoint.
                            </p>
                        </td>
                    </tr>

                    <!-- CDN Endpoint -->
                    <tr id="cdn-endpoint-row">
                        <th scope="row">
                            <label for="cdn_endpoint">CDN Endpoint (Optional)</label>
                        </th>
                        <td>
                            <?php
                            // Auto-generate CDN endpoint from API endpoint
                            $auto_cdn_endpoint = '';
                            if (!empty($settings['endpoint'])) {
                                $endpoint = $settings['endpoint'];
                                // Transform: https://sfo3.digitaloceanspaces.com → https://sfo3.cdn.digitaloceanspaces.com
                                $auto_cdn_endpoint = str_replace('.digitaloceanspaces.com', '.cdn.digitaloceanspaces.com', $endpoint);
                            }
                            ?>

                            <input
                                type="url"
                                name="cdn_endpoint"
                                id="cdn_endpoint"
                                value="<?php echo esc_attr($settings['cdn_endpoint']); ?>"
                                class="regular-text"
                                placeholder="<?php echo esc_attr($auto_cdn_endpoint); ?>">

                            <p class="description">
                                <strong>Default (auto-generated):</strong> <code id="auto-cdn-value"><?php echo esc_html($auto_cdn_endpoint); ?></code><br>
                                Leave empty to use the default Spaces CDN endpoint. Only fill this in if you've configured a custom CDN domain (e.g., "https://cdn.yourdomain.com").
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button('Save Settings', 'primary', 'do_spaces_submit'); ?>
            </form>

            <!-- Status Information -->
            <div class="do-spaces-status-box">
                <h3>Current Status</h3>
                <ul>
                    <li>
                        <strong>Integration:</strong>
                        <?php if ($settings['enabled']): ?>
                            <span class="status-enabled">Enabled</span>
                        <?php else: ?>
                            <span class="status-disabled">Disabled</span>
                        <?php endif; ?>
                    </li>
                    <li>
                        <strong>Configuration:</strong>
                        <?php if ($this->settings_manager->is_configured()): ?>
                            <span class="status-configured">Complete</span>
                        <?php else: ?>
                            <span class="status-incomplete">Incomplete</span>
                        <?php endif; ?>
                    </li>
                    <?php if ($settings['enabled'] && $this->settings_manager->is_configured()): ?>
                    <li>
                        <strong>Upload URL:</strong>
                        <code><?php echo esc_html($this->get_spaces_url($settings)); ?></code>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Migration Tool -->
            <?php if ($settings['enabled'] && $this->settings_manager->is_configured()): ?>
            <div class="do-spaces-migration-box">
                <h2>Migrate Existing Media</h2>
                <p>Upload all existing WordPress media files to Digital Ocean Spaces. This is a one-time operation needed when first enabling the plugin.</p>

                <div class="migration-stats">
                    <p>
                        <strong>Total Files:</strong> <span id="migration-total">-</span> |
                        <strong>Migrated:</strong> <span id="migration-migrated">-</span> |
                        <strong>Remaining:</strong> <span id="migration-remaining">-</span>
                    </p>
                    <div class="migration-progress">
                        <div class="migration-progress-bar-container">
                            <div id="migration-progress-bar" class="migration-progress-bar" style="width: 0%"></div>
                        </div>
                        <span id="migration-progress-text" class="migration-progress-text">0%</span>
                    </div>
                </div>

                <p>
                    <button type="button" id="start-migration-btn" class="button button-primary">
                        Start Migration
                    </button>
                    <button type="button" id="cancel-migration-btn" class="button button-secondary" style="display:none;" disabled>
                        Cancel
                    </button>
                    <button type="button" id="reset-migration-btn" class="button button-link-delete" style="margin-left: 10px;">
                        Reset Migration Status
                    </button>
                </p>

                <div id="migration-status" class="migration-status"></div>

                <p class="description">
                    <strong>Note:</strong> Migration will process all media files and their thumbnails.
                    This may take several minutes depending on the number of files.
                    Please keep this page open until migration completes.
                </p>
            </div>
            <?php endif; ?>

            <!-- Metadata Repair Tool -->
            <?php if ($settings['enabled'] && $this->settings_manager->is_configured()): ?>
            <div class="do-spaces-repair-box">
                <h2>Repair Image Metadata</h2>
                <p>Fix images that have broken dimensions (width="1" height="1") or missing thumbnails. This downloads each image from Spaces, regenerates metadata and thumbnails, then re-uploads them.</p>

                <div class="repair-stats">
                    <p>
                        <strong>Broken:</strong> <span id="repair-broken">-</span> |
                        <strong>Repaired:</strong> <span id="repair-repaired">-</span>
                    </p>
                    <div class="migration-progress">
                        <div class="migration-progress-bar-container">
                            <div id="repair-progress-bar" class="migration-progress-bar" style="width: 0%"></div>
                        </div>
                        <span id="repair-progress-text" class="migration-progress-text">0%</span>
                    </div>
                </div>

                <p>
                    <button type="button" id="start-repair-btn" class="button button-primary">
                        Start Repair
                    </button>
                    <button type="button" id="cancel-repair-btn" class="button button-secondary" style="display:none;" disabled>
                        Cancel
                    </button>
                    <button type="button" id="reset-repair-btn" class="button button-link-delete" style="margin-left: 10px;">
                        Reset Repair Status
                    </button>
                </p>

                <div id="repair-status" class="migration-status"></div>

                <p class="description">
                    <strong>Note:</strong> Each image is downloaded from Spaces, processed locally, then thumbnails are uploaded back.
                    This can be slow for large images. Keep this page open until repair completes.
                </p>
            </div>
            <?php endif; ?>
        </div>

        <style>
            .required { color: #d63638; }
            .do-spaces-status-box {
                background: #fff;
                border: 1px solid #c3c4c7;
                border-left: 4px solid #2271b1;
                padding: 15px;
                margin-top: 20px;
            }
            .do-spaces-status-box h3 {
                margin-top: 0;
            }
            .status-enabled,
            .status-configured {
                color: #008a00;
                font-weight: 600;
            }
            .status-disabled,
            .status-incomplete {
                color: #d63638;
                font-weight: 600;
            }
            .do-spaces-migration-box {
                background: #fff;
                border: 1px solid #c3c4c7;
                border-left: 4px solid #2271b1;
                padding: 15px;
                margin-top: 20px;
            }
            .do-spaces-migration-box h2 {
                margin-top: 0;
            }
            .migration-stats {
                margin: 15px 0;
            }
            .migration-progress {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-top: 10px;
            }
            .migration-progress-bar-container {
                flex: 1;
                height: 24px;
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                overflow: hidden;
            }
            .migration-progress-bar {
                height: 100%;
                background: #2271b1;
                transition: width 0.3s ease;
            }
            .migration-progress-text {
                min-width: 45px;
                font-weight: 600;
            }
            .migration-status {
                margin-top: 10px;
                padding: 8px 12px;
                border-radius: 3px;
            }
            .migration-status.processing {
                background: #f0f6fc;
                border: 1px solid #2271b1;
                color: #2271b1;
            }
            .migration-status.success {
                background: #d5f5e3;
                border: 1px solid #008a00;
                color: #008a00;
            }
            .migration-status.error {
                background: #fef0f0;
                border: 1px solid #d63638;
                color: #d63638;
            }
            .do-spaces-repair-box {
                background: #fff;
                border: 1px solid #c3c4c7;
                border-left: 4px solid #dba617;
                padding: 15px;
                margin-top: 20px;
            }
            .do-spaces-repair-box h2 {
                margin-top: 0;
            }
            .repair-stats {
                margin: 15px 0;
            }
        </style>
        <?php
    }

    /**
     * Get the Spaces URL based on current settings
     *
     * @param array $settings
     * @return string
     */
    private function get_spaces_url($settings) {
        $url = rtrim($settings['endpoint'], '/');
        $url = str_replace(['https://', 'http://'], '', $url);
        $url = 'https://' . $settings['bucket'] . '.' . $url;

        if (!empty($settings['path_prefix'])) {
            $url .= '/' . trim($settings['path_prefix'], '/');
        }

        return $url;
    }
}
