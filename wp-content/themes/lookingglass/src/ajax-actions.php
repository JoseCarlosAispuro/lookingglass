<?php

function set_wp_nonce_for_fetch_actions()
{
    wp_localize_script('sp-scripts', 'wpApi', [
        'url' => admin_url('admin-ajax.php'),
        'actions' => [
            'fetch_past_plays' => [
                'action' => 'fetch_past_plays',
                'nonce'  => wp_create_nonce('fetch_past_plays'),
            ],
            'fetch_plays_by_member' => [
                'action' => 'fetch_plays_by_member',
                'nonce'  => wp_create_nonce('fetch_plays_by_member'),
            ],
            'fetch_play_events' => [
                'action' => 'fetch_play_events',
                'nonce'  => wp_create_nonce('fetch_play_events'),
            ],
            'fetch_play_event_times' => [
                'action' => 'fetch_play_event_times',
                'nonce'  => wp_create_nonce('fetch_play_event_times'),
            ],
        ],
    ]);
}

add_action('wp_enqueue_scripts', 'set_wp_nonce_for_fetch_actions', 100);


function fetch_past_plays(): void
{
    $output = '';
    $page = $_POST['offset'] ?? 10;
    $current_page = $_POST['currentPage'] ?? 1;
    $posts_per_page = $_POST['perPage'] ?? -1;
    $tag = $_POST['tag'] ?? null;
    $exceptionCategory = $_POST['exceptionCategory'] ?? null;
    $nonce = sanitize_text_field($_POST['nonce']);

    if (!wp_verify_nonce($nonce, 'fetch_past_plays')) {
        wp_send_json_error();
    }

    $plays = get_plays($posts_per_page, $page, true, $exceptionCategory, $tag);
    $gridPlays = get_plays_grid($plays['posts'], $current_page % 2 === 0);

    foreach ($gridPlays as $index => $rowPlays) {
        $output .= view('components.plays-grid-row', [
            'rowIndex' => $index,
            'plays' => $rowPlays,
            'reversed' => $current_page % 2 === 0
        ]);
    }

    wp_send_json_success([
        'html' => $output,
        'page' => intval($page),
        'max_num_posts' => $plays['maxPosts'],
    ]);
}

add_action('wp_ajax_fetch_past_plays', 'fetch_past_plays');
add_action('wp_ajax_nopriv_fetch_past_plays', 'fetch_past_plays');


function fetch_plays_by_member(): void
{
    $output = '';
    $page = intval($_POST['offset'] ?? 1);
    $posts_per_page = intval($_POST['perPage'] ?? -1);
    $team_member = sanitize_text_field($_POST['teamMember'] ?? null);
    $nonce = sanitize_text_field($_POST['nonce']);

    if (!wp_verify_nonce($nonce, 'fetch_plays_by_member')) {
        wp_send_json_error();
    }

    $member_id = get_member_id_by_slug($team_member) ?? null;
    $playIDs = get_plays_by_member($member_id) ?? [];

    $plays = get_plays_by_ID($posts_per_page, $page, $playIDs);

    if(!isset($plays['posts'])) {
        wp_send_json_error();
    }

    foreach ($plays['posts'] as $play) {
        $output .= view('components.team-members.play-list-item', compact('play'));
    }

    wp_send_json_success([
        'html' => $output,
        'page' => intval($page),
        'max_num_posts' => $plays['maxPosts'],
    ]);
}

add_action('wp_ajax_fetch_plays_by_member', 'fetch_plays_by_member');
add_action('wp_ajax_nopriv_fetch_plays_by_member', 'fetch_plays_by_member');


function fetch_play_events(): void
{
    $playId = $_POST['playId'] ?? null;
    $nonce = sanitize_text_field($_POST['nonce']);

    if (!wp_verify_nonce($nonce, 'fetch_play_events')) {
        wp_send_json_error();
    }

    $playEvents = get_field('events_schedule', $playId ? (int)$playId : null);


    wp_send_json_success([
        'events' => $playEvents,
    ]);
}

add_action('wp_ajax_fetch_play_events', 'fetch_play_events');
add_action('wp_ajax_nopriv_fetch_play_events', 'fetch_play_events');

function fetch_play_event_times(): void
{
    $output = '';
    $events = $_POST['events'] ?? null;
    $activeDate = $_POST['date'] ?? null;
    $nonce = sanitize_text_field($_POST['nonce']);
    $newDate = new DateTime($activeDate);

    if (!wp_verify_nonce($nonce, 'fetch_play_event_times')) {
        wp_send_json_error();
    }

    try {
        $jsonEvents = json_decode(stripslashes($events), true);

        $filteredEvents = array_filter($jsonEvents, function($event) use ($newDate)  {
            return $event['date'] === $newDate->format('Y-m-d');
        });

        if($filteredEvents) {
            foreach ($filteredEvents as $index => $event) {
                $output .= view('components.calendar-events', [
                    'eventIndex' => $index,
                    'times' => $event['times'],
                    'defaultUrl' => get_default_time_url($event['times'])
                ]);
            }
        } else {
            $output .= view('components.calendar-events-no-results');
        }

        wp_send_json_success([
            'html' => $output,
        ]);
    } catch(error){
        wp_send_json_error();
    }

}

add_action('wp_ajax_fetch_play_event_times', 'fetch_play_event_times');
add_action('wp_ajax_nopriv_fetch_play_event_times', 'fetch_play_event_times');
