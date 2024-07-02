<?php
// Enqueue Google Places API and custom scripts
add_action('admin_enqueue_scripts', 'enqueue_google_places_api');

function enqueue_google_places_api($hook_suffix)
{
    global $post;
    if ($hook_suffix == 'post-new.php' || $hook_suffix == 'post.php') {
        if ('event' === $post->post_type) {
            $api_key = get_option('events_plugin_google_api_key');
            if ($api_key) {
                // Google Places API
                wp_enqueue_script('google-places-api', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($api_key) . '&libraries=places', null, null, true);
            }

            // Custom script
            wp_enqueue_script('custom-google-places', plugin_dir_url(__FILE__) . '../js/custom-google-places.js', array('jquery'), null, true);
        }
    }
}

// Enqueue admin styles
add_action('admin_enqueue_scripts', 'enqueue_admin_styles');

function enqueue_admin_styles($hook_suffix)
{
    global $post;
    if ($hook_suffix == 'post-new.php' || $hook_suffix == 'post.php') {
        if ('event' === $post->post_type) {
            wp_enqueue_style('admin-styles', plugin_dir_url(__FILE__) . '../css/admin-style.css');
        }
    }
}

// Enqueue single event styles
add_action('wp_enqueue_scripts', 'enqueue_single_event_styles');

function enqueue_single_event_styles()
{
    if (is_singular('event')) {
        wp_enqueue_style('single-event-styles', plugin_dir_url(__FILE__) . '../css/single-event.css');
    }
}
