<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn]
function dd(...$args): void
{
    var_dump($args);
    die();
}

function optional($value)
{
    return $value ? $value : (object)[];
}

#[NoReturn]
function redirect_404(): void
{
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part('404');
    exit();
}

function get_asset_path($fileName): string | bool
{
    $_path = path_join('/dist', $fileName);

    return file_exists(get_theme_file_path($_path))
        ? get_theme_file_uri($_path)
        : false;
}

function get_hashed_asset($fileName): string
{
    $manifestFile = get_template_directory() . '/dist/.vite/manifest.json';

    if (!file_exists($manifestFile)) {
        return '';
    }

    $manifest = json_decode(file_get_contents($manifestFile), true);

    // Vite manifest structure is different - look for assets/typescript/main.ts entry
    $entry = 'assets/typescript/main.ts';
    if (!isset($manifest[$entry])) {
        return '';
    }

    $asset = $manifest[$entry];

    // Return CSS file if requesting .css
    if (str_ends_with($fileName, '.css') && isset($asset['css'])) {
        return get_template_directory_uri() . '/dist/' . $asset['css'][0];
    }

    // Return JS file if requesting .js
    if (str_ends_with($fileName, '.js') && isset($asset['file'])) {
        return get_template_directory_uri() . '/dist/' . $asset['file'];
    }

    return '';
}

function get_nav_menu_object_by_location($location): object | false | null
{
    // Get all locations
    $locations = get_nav_menu_locations();
    // Get object id by location if exists
    if (!isset($locations[$location])) return false;
    return wp_get_nav_menu_object($locations[$location]);
}

function get_nav_menu_by_location($location, $args = []): string | false | null
{
    $object = get_nav_menu_object_by_location($location);
    return $object ? wp_nav_menu(array_merge($args, ['menu' => $object->name])) : $object;
}

function get_nav_menu_items_by_location($location, $args = []): array | false | null
{
    // Get all locations
    $locations = get_nav_menu_locations();
    // Get object id by location if exists
    if (!isset($locations[$location])) return false;
    return wp_get_nav_menu_items($locations[$location]);
}

function get_plays($postsPerPage, $offset, $isPastPlay, $category, $tag) {
    $pastPlaysArray = [];

    $baseQuery = [
        'post_type' => 'plays',
        'posts_per_page' => $postsPerPage,
        'offset' => $offset,
        'post_status' => 'publish',
    ];

    if($isPastPlay) {
        $todayDate = date( 'Y-m-d' );

        $pastPlaysArr = [
            'order' => 'DESC',
            'orderby' => 'meta_value_num',
            'meta_key' => 'end_play_date',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'end_play_date',
                    'compare' => 'EXISTS',
                ],
                [
                    'key' => 'end_play_date',
                    'compare' => '!=',
                    'value' => ''
                ],
                [
                    'key' => 'end_play_date',
                    'compare' => '<',
                    'value' => $todayDate,
                    'type' => 'DATE'
                ],
            ]
        ];

        $pastPlaysArray = array_merge($baseQuery, $pastPlaysArr);
    }

    if($category) {
        $categoryArray = [
            'cat' => '-' . $category
        ];

        $pastPlaysArray = array_merge($pastPlaysArray, $categoryArray);
    }

    if($tag) {
        $tagArray = [
            'tag_id' => $tag
        ];

        $pastPlaysArray = array_merge($pastPlaysArray, $tagArray);
    }

    $wpQuery = new WP_Query($pastPlaysArray);

    return ['posts' => $wpQuery->posts, 'maxPosts' => $wpQuery->found_posts];
}

function get_active_plays($plays) {
    if(!$plays) {
        return null;
    }

    return array_filter($plays, function($play){
        return check_play_display($play);
    });
}

function get_past_plays($plays) {
    if(!$plays) {
        return null;
    }

    return array_filter($plays, function($play){
        return past_play_display($play);
    });
}

function past_play_display($play) {
    $ID = $play->ID ?? $play['play'][0];
    $endDate = get_field('end_play_date', $ID);
    $endDateFormat = $endDate ? new DateTime($endDate) : null;
    $today = new DateTime();

    return $today > $endDateFormat;
}

function get_plays_grid($plays, $reversed = false) {
    $result = [];
    $index = 0;

    while ($index < count($plays)) {
        // Determine batch size: even batches get 4, odd batches get 3
        if($reversed){
            $size = (count($result) % 2 == 0) ? 3 : 4;
        } else {
            $size = (count($result) % 2 == 0) ? 4 : 3;
        }

        $result[] = array_slice($plays, $index, $size);
        $index += $size;
    }

    return $result;
}

function get_play_dates($ID, $longFormat = false) {
    $startDate = get_field('start_play_date', $ID);
    $endDate = get_field('end_play_date', $ID);
    $startDateFormat = $startDate ? new DateTime($startDate) : null;
    $endDateFormat = $endDate ? new DateTime($endDate) : null;

    if($startDateFormat && $endDateFormat) {
        return $longFormat ? date_format($startDateFormat, 'F j') . ' - ' . date_format($endDateFormat, 'F j, Y') : date_format($startDateFormat, 'M j') . ' - ' . date_format($endDateFormat, 'M j, Y');
    }

    if($endDateFormat) {
        return $longFormat ? date_format($endDateFormat, 'F j, Y') : date_format($endDateFormat, 'M j, Y');
    }

    if($startDateFormat) {
        return $longFormat ? date_format($startDateFormat, 'F j, Y') : date_format($startDateFormat, 'M j, Y');
    }

    return '';
}

function check_play_display($play) {
    $playId = $play['play'][0];
    $startDate = get_field('start_play_date', $playId);
    $endDate = get_field('end_play_date', $playId);
    $startDateFormat = $startDate ? new DateTime($startDate) : null;
    $endDateFormat = $endDate ? new DateTime($endDate) : null;
    $today = new DateTime();

    if($endDateFormat) {
        return $startDateFormat < $today && $endDateFormat > $today;
    }

    return $startDateFormat > $today;
}

function get_palette_color_by_slug( $slug = null ) {
    if(!$slug) {
        return null;
    }

    $settings = wp_get_global_settings();
    $palette  = $settings['color']['palette']['theme'] ?? [];

    $slugs = array_column($palette, 'slug');
    $index = array_search($slug, $slugs);

    if ($index !== false && isset($palette[$index]['color'])) {
        return $palette[$index]['color'];
    }

    return null;
}

function get_plays_by_ID($postsPerPage = 10, $offset = 0, $IDs = []) {
    $pastPlaysArray = [];

    if (is_int($IDs) || (is_string($IDs) && is_numeric($IDs))) {
        $IDs = [$IDs];
    }

    $baseQuery = [
        'post_type' => 'plays',
        'posts_per_page' => $postsPerPage,
        'offset' => $offset,
        'post_status' => 'publish',
        'post__in' => $IDs,
        'order' => 'DESC',
        'orderby' => 'meta_value_num',
        'meta_key' => 'end_play_date'
    ];

    $wpQuery = new WP_Query($baseQuery);

    return ['posts' => $wpQuery->posts, 'maxPosts' => $wpQuery->found_posts];
}

function get_member_id_by_slug($slug = null) {
    if(!$slug) return null;

    $post_object = get_page_by_path($slug, OBJECT, 'team-member');

    if ($post_object) {
        return $post_object->ID;
    }

    return null;
}

function get_plays_by_member($memberID = null) {
    if($memberID) {
        return get_field('lookingglass_productions', $memberID) ?? [];
    }

    return null;
}

function get_default_time_url($times) {
    $defaultTime = array_filter($times, function($time)  {
        return $time['default_selected'];
    });

    return $defaultTime ? array_values($defaultTime)[0]['external_url'] : $times[0]['external_url'];
}

function get_members_by_taxonomy($taxonomy = '') {
    if (!$taxonomy) {
        return [];
    }

    $args = [
        'post_type'      => 'team-member',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'meta_value',
        'meta_key'       => 'last_name',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => 'member-role',
                'field'    => 'slug',
                'terms'    => $taxonomy,
            ]
        ],
    ];

    $wpQuery = new WP_Query($args);

    return $wpQuery->posts;
}

function check_default_time_selected($times): array {
    return array_filter($times, function($time)  {
        return $time['default_selected'];
    });
}

function get_status_label($status): string {
    return $status === 'sold_out' ? 'Sold out' : 'Cancelled';
}
