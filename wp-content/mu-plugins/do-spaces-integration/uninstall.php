<?php
/**
 * DigitalOcean Spaces Integration - Uninstall
 * Cleans up plugin data when the plugin is deleted via WordPress admin.
 */

// Exit if not called by WordPress uninstall
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Delete plugin settings
delete_option('do_spaces_settings');

// Delete all migration-related postmeta entries
$meta_keys = [
    '_do_spaces_migrated',
    '_do_spaces_migration_date',
    '_do_spaces_migration_error',
];

$placeholders = implode(',', array_fill(0, count($meta_keys), '%s'));
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM $wpdb->postmeta WHERE meta_key IN ($placeholders)",
        ...$meta_keys
    )
);
