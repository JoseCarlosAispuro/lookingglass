# Digital Ocean Spaces Integration for WordPress

A must-use WordPress plugin that integrates Digital Ocean Spaces (S3-compatible storage) for media uploads with an admin configuration interface.

## Features

- **Seamless Integration**: Automatically uploads media files to Digital Ocean Spaces
- **Sideload Support**: Handles sideloaded files (e.g., imports from external URLs) in addition to standard uploads
- **Media Migration**: Batch-migrate existing media library files to Spaces via an admin UI with progress tracking
- **CDN URL Rewriting**: Optionally serve media through DigitalOcean's CDN or a custom CDN endpoint
- **Admin Configuration**: Easy-to-use settings page in WordPress admin
- **Enable/Disable Toggle**: Turn integration on/off without losing configuration
- **Custom Path Prefix**: Organize files within your bucket with custom folder structures
- **Local Backup Option**: Keep local copies of files in addition to uploading to Spaces
- **Connection Testing**: Built-in connection test to verify credentials
- **Error Handling**: Graceful fallback to local storage if Spaces is unreachable
- **Thumbnail Support**: Automatically uploads all WordPress thumbnail sizes
- **Deletion Sync**: Removes files from Spaces when deleted from Media Library
- **GraphQL Compatible**: Works seamlessly with WPGraphQL (attachment URLs, image src, post content, and REST API responses)
- **Debug Logging**: Built-in logger for troubleshooting upload and migration issues
- **Clean Uninstall**: Removes all plugin data (settings and migration metadata) on deletion

## Installation

1. **Upload the plugin files** to `wp-content/mu-plugins/`:
   - `do-spaces-integration.php` (main loader file)
   - `do-spaces-integration/` (plugin directory)

2. **Install dependencies** via Composer:
   ```bash
   cd wp-content/mu-plugins/do-spaces-integration/
   composer install --no-dev --optimize-autoloader
   ```

3. **Configure the plugin** in WordPress admin:
   - Navigate to **Settings > DO Spaces**
   - Enter your Digital Ocean Spaces credentials
   - Test the connection
   - Save settings

## Configuration

### Required Settings

- **Bucket Name**: Your Digital Ocean Spaces bucket name (e.g., `my-wordpress-media`)
- **Access Key**: Your Spaces access key
- **Access Secret**: Your Spaces secret key
- **Region**: Region identifier (e.g., `nyc3`, `sfo3`, `ams3`, `sgp1`)
- **Endpoint URL**: Spaces endpoint URL (e.g., `https://nyc3.digitaloceanspaces.com`)

### Optional Settings

- **Enable/Disable**: Toggle integration without losing configuration
- **Path Prefix**: Custom folder structure in bucket (e.g., `wp-uploads/` or `sites/example/`)
- **Keep Local Backup**: Maintain local copies in addition to uploading to Spaces
- **Enable CDN**: Serve media files through DigitalOcean's built-in CDN
- **CDN Endpoint**: Custom CDN endpoint URL (leave blank to auto-generate from your region, e.g., `https://nyc3.cdn.digitaloceanspaces.com`)

## Getting Digital Ocean Spaces Credentials

1. Log in to your Digital Ocean account
2. Navigate to **Spaces** in the main menu
3. Create a new Space or select an existing one
4. Go to **API** in the main menu
5. Generate a new **Spaces access key** under the "Spaces Keys" section
6. Copy the **Access Key** and **Secret Key**
7. Note your Space's **Region** (e.g., nyc3, sfo3)
8. Note your **Endpoint URL** based on region:
   - NYC3: `https://nyc3.digitaloceanspaces.com`
   - SFO3: `https://sfo3.digitaloceanspaces.com`
   - AMS3: `https://ams3.digitaloceanspaces.com`
   - SGP1: `https://sgp1.digitaloceanspaces.com`

## Usage

### Upload Flow

**With Integration Enabled (No Local Backup):**
1. Upload media file via WordPress Media Library
2. File is temporarily saved locally
3. Plugin intercepts upload and sends to Spaces
4. Local file is deleted after successful upload
5. WordPress stores Spaces URL in database
6. File is accessible at: `https://bucket.region.digitaloceanspaces.com/[prefix]/YYYY/MM/filename.jpg`

**With Local Backup Enabled:**
- Same as above, but local file is kept
- Files exist in both locations
- URLs still point to Spaces (primary location)

**On Upload Error:**
- Upload to Spaces fails
- Local file is kept
- Error is logged
- Admin notice displayed
- Falls back to normal WordPress behavior

### Media Migration

Migrate existing media library files to Spaces without re-uploading:

1. Go to **Settings > DO Spaces**
2. Scroll to the **Media Migration** section
3. Click **Start Migration** to begin batch processing
4. Progress is shown in real-time (percentage, files processed, errors)
5. Migration processes files in batches of 50 to avoid timeouts

Files uploaded after enabling the plugin are automatically marked as migrated and will be skipped during migration. You can reset migration status and re-run it at any time.

### CDN URL Rewriting

When CDN is enabled, the plugin rewrites media URLs across:
- Attachment URLs (`wp_get_attachment_url`)
- Image src attributes (`wp_get_attachment_image_src`)
- Post content and featured image HTML
- REST API responses
- WPGraphQL queries (if WPGraphQL is installed)

If no custom CDN endpoint is provided, the plugin auto-generates one by replacing `.digitaloceanspaces.com` with `.cdn.digitaloceanspaces.com` in your endpoint URL.

### Disabling the Plugin

To temporarily disable without losing configuration:
1. Go to **Settings > DO Spaces**
2. Uncheck **Enable Integration**
3. Save settings
4. Uploads will now use local WordPress storage

To permanently remove:
1. Delete `/wp-content/mu-plugins/do-spaces-integration.php`
2. Delete `/wp-content/mu-plugins/do-spaces-integration/` directory
3. Note: Files remain in Spaces after removal; all plugin settings and migration metadata are cleaned up automatically

## File Structure

```
wp-content/mu-plugins/
├── do-spaces-integration.php          # Main loader file
└── do-spaces-integration/
    ├── src/
    │   ├── Plugin.php                 # Main plugin class
    │   ├── Admin/
    │   │   ├── SettingsPage.php       # Admin UI
    │   │   ├── ConnectionTest.php     # Connection testing
    │   │   └── MediaMigration.php     # Batch migration handler
    │   ├── CDN/
    │   │   └── URLRewriter.php        # CDN URL rewriting
    │   ├── Logger/
    │   │   └── Logger.php             # Debug logging
    │   ├── Upload/
    │   │   ├── UploadHandler.php      # Upload logic
    │   │   ├── S3ClientFactory.php    # S3 client and file operations
    │   │   └── PathManager.php        # URL management
    │   └── Settings/
    │       └── SettingsManager.php     # Settings storage
    ├── assets/
    │   ├── css/
    │   │   └── admin.css              # Admin styling
    │   └── js/
    │       ├── admin.js               # Admin JavaScript
    │       └── migration.js           # Migration UI
    ├── uninstall.php                   # Clean uninstall handler
    ├── vendor/                         # Composer dependencies
    ├── composer.json
    └── README.md                       # This file
```

## Requirements

- **PHP**: 7.4 or higher
- **WordPress**: 6.0 or higher
- **Composer**: For dependency management
- **Digital Ocean Spaces**: Active account with a Space created

## Dependencies

- **AWS SDK for PHP** (^3.300): S3-compatible client for Digital Ocean Spaces
- Installed automatically via Composer

## Troubleshooting

### Plugin Not Showing Up
- This is a must-use plugin and doesn't appear in the standard Plugins page
- Check if the main loader file exists: `wp-content/mu-plugins/do-spaces-integration.php`
- Verify Composer dependencies are installed

### Connection Test Fails
- Verify all credentials are correct
- Check bucket name matches exactly (case-sensitive)
- Ensure endpoint URL matches your region
- Verify your Spaces key has proper permissions
- Check for firewall/network issues

### Files Not Uploading to Spaces
- Ensure integration is enabled in settings
- Check all required fields are filled
- Test connection using the "Test Connection" button
- Check WordPress error logs for specific errors
- Verify bucket has proper permissions (ACL: public-read)

### Files Still Local After Upload
- Check if "Keep Local Backup" is enabled
- If not enabled, verify PHP has permission to delete files
- Check file permissions on `wp-content/uploads/`

### URLs Not Pointing to Spaces
- Verify integration is enabled
- Check settings are saved correctly
- Clear any caching plugins
- Regenerate attachment URLs if needed

### Thumbnails Missing
- Plugin automatically handles thumbnails after generation
- Check error logs for thumbnail-specific errors
- Verify bucket has space for multiple files

### Migration Issues
- Ensure the plugin is configured and can connect to Spaces (test connection first)
- Check that local files still exist — migration reads from `wp-content/uploads/`
- If migration stalls, refresh the page and restart — it picks up where it left off
- Use **Reset Migration** to clear all migration metadata and start fresh

## GraphQL Compatibility

This plugin is fully compatible with WPGraphQL. Media URLs in GraphQL queries automatically point to Spaces (or CDN if enabled):

```graphql
query GetPost {
  post(id: 1, idType: DATABASE_ID) {
    featuredImage {
      node {
        sourceUrl      # Returns Spaces/CDN URL
        mediaItemUrl   # Returns Spaces/CDN URL
      }
    }
  }
}
```

ACF image fields also work automatically:

```graphql
query GetPage {
  page(id: 1, idType: DATABASE_ID) {
    acfFields {
      heroImage {
        sourceUrl      # Returns Spaces/CDN URL
      }
    }
  }
}
```

## Security

- Settings require `manage_options` capability
- All forms and AJAX requests use WordPress nonces
- Input is sanitized and validated
- Supports `wp-config.php` constants so credentials never touch the database

### Using wp-config.php Constants (Recommended)

For best security, define your credentials as constants in `wp-config.php` instead of entering them through the admin UI. This keeps secrets out of the database, where they could be exposed by SQL injection, backup leaks, or other plugins.

Add the following to `wp-config.php` (before the `/* That's all, stop editing! */` line):

```php
define('DO_SPACES_ACCESS_KEY', 'your-key-here');
define('DO_SPACES_ACCESS_SECRET', 'your-secret-here');
```

When constants are defined:
- The corresponding admin fields become read-only and display a masked value
- Credentials are read directly from `wp-config.php` at runtime — they are never stored in the database
- You can define one or both constants — any field without a constant remains editable in the UI
- The "Test Connection" button works normally with constant-provided credentials

This approach is especially useful on managed WordPress hosts (e.g., WP Engine, Cloudways) that provide a portal for setting `wp-config.php` constants.

## Credits

[StrangePixels](https://strangepixels.co/)

## License

GPL-2.0-or-later

## Version

1.1.0
