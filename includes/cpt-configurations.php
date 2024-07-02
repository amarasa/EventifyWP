<?php
// Add columns to the event post type list table
add_filter('manage_event_posts_columns', 'add_event_columns');
function add_event_columns($columns)
{
    $new_columns = array();
    foreach ($columns as $key => $title) {
        $new_columns[$key] = $title;
        if ('title' === $key) {
            $new_columns['recurring_series'] = __('Recurring Series', 'basic-events');
        }
    }
    $new_columns['featured_event'] = __('Featured', 'basic-events');
    $new_columns['tags'] = __('Tags', 'basic-events'); // Add tags column
    return $new_columns;
}

// Populate the event columns
add_action('manage_event_posts_custom_column', 'populate_event_columns', 10, 2);
function populate_event_columns($column, $post_id)
{
    if ($column === 'recurring_series') {
        $recurrence_dates = get_post_meta($post_id, '_event_recurrence_dates', true);
        if ($recurrence_dates && is_array($recurrence_dates)) {
            $upcoming_dates = array_filter($recurrence_dates, function ($date) {
                return strtotime($date) >= strtotime(date('Y-m-d'));
            });
            echo count($upcoming_dates) . ' Events Remaining';
        } else {
            echo 'Single Event';
        }
    }

    if ($column === 'featured_event') {
        $is_featured = get_post_meta($post_id, '_featured_event', true);
        $star_class = $is_featured ? 'dashicons-star-filled' : 'dashicons-star-empty';
        echo '<a href="#" class="toggle-featured-event" data-post-id="' . $post_id . '"><span class="dashicons ' . $star_class . '"></span></a>';
    }

    if ($column === 'tags') {
        $post_tags = get_the_terms($post_id, 'post_tag');
        if ($post_tags && !is_wp_error($post_tags)) {
            $tags = array();
            foreach ($post_tags as $tag) {
                $tags[] = $tag->name;
            }
            echo implode(', ', $tags);
        } else {
            echo '—';
        }
    }
}

// Handle the AJAX request to toggle featured event
add_action('wp_ajax_toggle_featured_event', 'toggle_featured_event');
function toggle_featured_event()
{
    check_ajax_referer('toggle_featured_event', 'nonce');

    $post_id = intval($_POST['post_id']);
    $is_featured = get_post_meta($post_id, '_featured_event', true) === '1' ? '0' : '1';

    update_post_meta($post_id, '_featured_event', $is_featured);

    wp_send_json_success(array('new_status' => $is_featured));
}

// Register tags for the event post type
function add_tags_to_events()
{
    register_taxonomy_for_object_type('post_tag', 'event');
}
add_action('init', 'add_tags_to_events');
