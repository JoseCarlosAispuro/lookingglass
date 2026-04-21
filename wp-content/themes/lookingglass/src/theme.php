<?php

const NAV_MENUS_MAIN = 'navigation';
const NAV_MENUS_FOOTER = 'footer';
const NAV_MENUS_SOCIAL = 'social';
const NAV_MENUS_PRIVACY = 'privacy';

const NAV_MENUS_SUPPORT = 'support';

add_theme_support('menus');
add_theme_support('post-thumbnails');
add_theme_support('wp-block-styles');

register_nav_menus([
    NAV_MENUS_MAIN => 'Main Navigation',
    NAV_MENUS_SUPPORT => 'Support',
    NAV_MENUS_FOOTER => 'Footer',
    NAV_MENUS_SOCIAL => 'Social Media',
    NAV_MENUS_PRIVACY => 'Privacy',
]);

/**
 * Add support for JSON files to be uploaded as media through the admin.
 */
function cc_mime_types($mimes): array
{
    $mimes['json'] = 'application/json';
    return $mimes;
}

add_filter('upload_mimes', 'cc_mime_types');

/**
 * Inject theme's custom styles to the gutenberg editor to style ACF blocks.
 */
function setup_gutenberg_preview_stylesheets(): void
{
    if (is_admin() || is_login()) {
        add_theme_support('wp-block-styles');
        // TODO: Fix this to be able to see the preview state in the editor
        //wp_enqueue_style('sp-styles', get_hashed_asset('/app.css'));
    }
}

add_action('after_setup_theme', 'setup_gutenberg_preview_stylesheets');

/**
 * Remove unnecessary WP scripts and styles.
 */
function remove_wp_global_styles(): void
{
    if (!is_admin()) {
        wp_enqueue_style('sp-styles', get_hashed_asset('/app.css'));
        wp_enqueue_script('sp-scripts', get_hashed_asset('/app.js'));
    }
}

add_action('wp_enqueue_scripts', 'remove_wp_global_styles', 100);

/**
 * Pass navigation gallery image data to the frontend as JSON
 * instead of rendering ~80 <img> tags in the DOM.
 */
function enqueue_navigation_gallery_data(): void
{
    $images = get_field('navigation_images', 'options');
    $gallery = [];

    if ($images) {
        foreach ($images as $image) {
            $gallery[] = [
                'url' => $image['url'],
                'alt' => $image['alt'] ?? '',
            ];
        }
    }

    wp_localize_script('sp-scripts', 'navigationGallery', $gallery);
}

add_action('wp_enqueue_scripts', 'enqueue_navigation_gallery_data', 101);

/**
 * Register ACF gutenberg blocks dynamically based on the src/blocks folder contents.
 * Supports both JSON files at the folder level and subfolder structures for backward compatibility.
 */
function init_gutenberg_blocks(): void
{
    if (function_exists('register_block_type')) {

        $source = get_theme_file_path("src/blocks");

        $blocks = array_filter(scandir($source) ?? [], function ($f) use ($source) {
            /* Ignore plain files and private folders */
            return is_dir("{$source}/{$f}") && !str_starts_with($f, '.');
        });

        foreach ($blocks as $block) {
            try {
                $file_path = path_join($source, $block);
                register_block_type($file_path);
            } catch (\Exception $e) {
                error_log("Failed to register block: {$block} - {$e->getMessage()}");
            }
        }
    }
}

add_action('init', 'init_gutenberg_blocks');

/**
 * Override the default block types allowed per post type / template.
 * For more information about available core blocks, visit https://wordpress.org/documentation/article/blocks-list/
 */
function set_allowed_block_types($allowed_block_types, $context): array
{
    $post = $context->post;
    $blocks = array_keys(WP_Block_Type_Registry::get_instance()->get_all_registered());

    // Block types enabled for all post types
    $whitelisted_blocks = [
        'core/shortcode',
    ];
    // Block types enabled for non-page post types
    $post_type_blocks = [
        'core/block',
        'core/embed',
        'core/quote',
        'core/columns',
        'core/gallery',
        'core/list',
        'core/image',
        'core/heading',
        'core/list-item',
        'core/paragraph',
        'core/separator',
    ];
    // Block types enabled for pages using the legal-page template
    $legal_page_blocks = [
        'core/list',
        'core/block',
        'core/columns',
        'core/heading',
        'core/list-item',
        'core/paragraph',
        'core/separator',
    ];

    $allowed_blocks = array_filter(
        $blocks,
        function ($block) use ($post, $whitelisted_blocks, $post_type_blocks, $legal_page_blocks) {
            $is_whitelisted = in_array($block, $whitelisted_blocks);
            $is_legal_page = get_page_template_slug($post->ID) === 'legal-page.php';

            $is_allowed_by_post_type = in_array($post->post_type, ['page', 'service', 'security', 'framework'])
                ? ($is_legal_page
                    ? in_array($block, $legal_page_blocks)
                    : str_starts_with($block, 'acf/'))
                : in_array($block, $post_type_blocks);

            return $is_whitelisted || $is_allowed_by_post_type;
        }
    );

    return array_values($allowed_blocks);
}

add_filter('allowed_block_types_all', 'set_allowed_block_types', 10, 2);

/**
 * Restrict access to the administration screens.
 *
 * Only administrators will be allowed to access the admin screens,
 * all other users will be automatically redirected to the homepage.
 */
function restrict_admin_with_redirect(): void
{
    if (!wp_doing_ajax() && !current_user_can('edit_posts')) {
        wp_safe_redirect(home_url());
        exit;
    }
}

add_action('admin_init', 'restrict_admin_with_redirect', 1);

/**
 * Add customizer fields for the login screen.
 */
function add_custom_login_screen_fields($wp_customize): void
{
    $wp_customize->add_setting(
        'login_screen_logo',
        [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ],
    );

    $wp_customize->add_control(new WP_Customize_Image_Control(
        $wp_customize,
        'login_screen_logo',
        [
            'priority' => 10,
            'section' => 'title_tagline',
            'settings' => 'login_screen_logo',
            'label' => __('Login Screen Logo', 'textdomain'),
            'description' => __('This logo will appear on the login and signup screens. \nThe logo should be rectangular and at least 320×20 pixels.', 'textdomain'),
        ],
    ));
}

add_action('customize_register', 'add_custom_login_screen_fields');

/**
 * Apply theme's customizer fields to the login screen.
 */
function set_login_screen_logo(): void
{
    $logo = get_theme_mod('login_screen_logo', null);

    if (!is_null($logo)) {
        echo "
        <style>
            #login h1 a, .login h1 a {
                width: 100%;
                background-size: contain;
                background-image: url({$logo});
            }
        </style>
    ";
    }
}

add_action('login_enqueue_scripts', 'set_login_screen_logo');

/**
 * Hide the admin toolbar for all users except administrators.
 */
function remove_admin_bar(): void
{
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

add_action('after_setup_theme', 'remove_admin_bar');

/**
 * Enqueue the jquery.ripples plugin only if the acf/ripple-banner block is present on the page.
 * This prevents unnecessary asset loading when the effect isn't needed.
 * Also sets window.$ = window.jQuery for plugin compatibility.
 */
function enqueue_ripple_banner_scripts()
{
    if (!has_block('acf/ripple-banner')) {
        return;
    }

    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'jquery-ripples',
        content_url('/themes/lookingglass/dist/jquery.ripples-min.js'),
        ['jquery'],
        null,
        true
    );

    wp_add_inline_script(
        'jquery',
        'window.$ = window.jQuery;'
    );
}

add_action('wp_enqueue_scripts', 'enqueue_ripple_banner_scripts');

function disable_gutenberg_on_plays( $use_block_editor, $post_type ) {
    if ( 'plays' === $post_type ) {
        return false;
    }
    return $use_block_editor;
}

add_filter( 'use_block_editor_for_post_type', 'disable_gutenberg_on_plays', 10, 2 );

function filter_blocks_for_legal($allowed_blocks, $editor_context) {

    if (!empty($editor_context->post)) {

        $template = get_page_template_slug($editor_context->post->ID);

        if ($template === 'legal.php') {
            return [
                'acf/legal-section',
            ];
        }
    }

    return $allowed_blocks;
}

add_filter('allowed_block_types_all', 'filter_blocks_for_legal', 10, 2);

add_action('wp_footer', function () { 
    wp_dequeue_style('core-block-supports'); 
});