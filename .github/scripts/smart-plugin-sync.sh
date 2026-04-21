#!/bin/bash

# Smart Plugin Sync Script
# Preserves untracked plugins and only updates when newer versions are available

DEPLOY_PATH="$1"
TEMP_PLUGINS_DIR="$2"

if [ -z "$DEPLOY_PATH" ] || [ -z "$TEMP_PLUGINS_DIR" ]; then
    echo "Usage: $0 <deploy_path> <temp_plugins_dir>"
    exit 1
fi

CURRENT_PLUGINS_DIR="$DEPLOY_PATH/wp-content/plugins"
NEW_PLUGINS_DIR="$TEMP_PLUGINS_DIR"

echo "🔄 Starting smart plugin synchronization..."
echo "   Current plugins: $CURRENT_PLUGINS_DIR"
echo "   New plugins: $NEW_PLUGINS_DIR"

# Function to extract version from plugin file
get_plugin_version() {
    local plugin_dir="$1"
    local version=""

    # Find the main plugin file (contains "Plugin Name:")
    local main_file=$(find "$plugin_dir" -maxdepth 1 -name "*.php" -exec grep -l "Plugin Name:" {} \; 2>/dev/null | head -n 1)

    if [ -n "$main_file" ]; then
        version=$(grep "Version:" "$main_file" | head -n 1 | sed 's/.*Version:[[:space:]]*//' | tr -d ' \r\n')
    fi

    echo "$version"
}

# Function to compare versions (returns 0 if first is newer, 1 otherwise)
version_is_newer() {
    local current="$1"
    local new="$2"

    if [ -z "$current" ] || [ -z "$new" ]; then
        return 1
    fi

    # Use sort -V for version comparison
    if [ "$(printf '%s\n' "$current" "$new" | sort -V | tail -n1)" = "$current" ] && [ "$current" != "$new" ]; then
        return 0
    else
        return 1
    fi
}

# Create plugins directory if it doesn't exist
mkdir -p "$CURRENT_PLUGINS_DIR"

# Process each plugin from the new deployment
if [ -d "$NEW_PLUGINS_DIR" ]; then
    echo "📦 Processing new plugins..."

    for new_plugin_path in "$NEW_PLUGINS_DIR"/*/; do
        if [ ! -d "$new_plugin_path" ]; then
            continue
        fi

        plugin_name=$(basename "$new_plugin_path")
        current_plugin_path="$CURRENT_PLUGINS_DIR/$plugin_name"

        # Skip system files
        if [ "$plugin_name" = "index.php" ] || [[ "$plugin_name" == .* ]]; then
            continue
        fi

        echo "   Checking plugin: $plugin_name"

        # If plugin doesn't exist on server, copy it
        if [ ! -d "$current_plugin_path" ]; then
            echo "     ✅ New plugin - copying $plugin_name"
            cp -R "$new_plugin_path" "$current_plugin_path"
            continue
        fi

        # Plugin exists, check versions
        current_version=$(get_plugin_version "$current_plugin_path")
        new_version=$(get_plugin_version "$new_plugin_path")

        echo "     Current version: ${current_version:-'unknown'}"
        echo "     New version: ${new_version:-'unknown'}"

        # If both versions exist, compare them
        if [ -n "$current_version" ] && [ -n "$new_version" ]; then
            if [ "$current_version" = "$new_version" ]; then
                echo "     ⚡ Same version - skipping $plugin_name"
            elif version_is_newer "$new_version" "$current_version"; then
                echo "     🔄 Updating $plugin_name from $current_version to $new_version"
                rm -rf "$current_plugin_path"
                cp -R "$new_plugin_path" "$current_plugin_path"
            else
                echo "     ⏸️  Server has newer version - preserving $plugin_name ($current_version)"
            fi
        else
            # If we can't determine versions, update anyway (safer for tracked plugins)
            echo "     🔄 Version unknown - updating $plugin_name"
            rm -rf "$current_plugin_path"
            cp -R "$new_plugin_path" "$current_plugin_path"
        fi
    done
fi

# Process existing plugins that aren't in the new deployment
echo "🔍 Checking for untracked plugins to preserve..."

if [ -d "$CURRENT_PLUGINS_DIR" ]; then
    for current_plugin_path in "$CURRENT_PLUGINS_DIR"/*/; do
        if [ ! -d "$current_plugin_path" ]; then
            continue
        fi

        plugin_name=$(basename "$current_plugin_path")
        new_plugin_path="$NEW_PLUGINS_DIR/$plugin_name"

        # Skip system files
        if [ "$plugin_name" = "index.php" ] || [[ "$plugin_name" == .* ]]; then
            continue
        fi

        # If plugin exists on server but not in new deployment, preserve it
        if [ ! -d "$new_plugin_path" ]; then
            current_version=$(get_plugin_version "$current_plugin_path")
            echo "   🔒 Preserving untracked plugin: $plugin_name (${current_version:-'unknown version'})"
        fi
    done
fi

echo "✅ Smart plugin synchronization completed!"