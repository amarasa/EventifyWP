<?php
/*
Plugin Name: EventifyWP
Description: A simple plugin to manage events.
Plugin URI: https://github.com/amarasa/EventifyWP
Version: 1.0
Author: Angelo Marasa
*/
// Include the Composer autoloader
require plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/updater.php';
require_once plugin_dir_path(__FILE__) . 'includes/scripts.php';
// Ensure this is loaded first
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/template-overrides.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-configurations.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/recurrence-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/event-functions.php';

// Automatically include all shortcode files
foreach (glob(plugin_dir_path(__FILE__) . 'includes/shortcodes/*.php') as $shortcode_file) {
    require_once $shortcode_file;
}

// Enqueue scripts and styles for the front-end
function enqueue_scripts()
{
    // Enqueue FullCalendar core and CSS
    wp_enqueue_style('fullcalendar-core', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.css', [], '6.1.14');
    wp_enqueue_script('fullcalendar-core', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js', [], '6.1.14', true);

    // Enqueue custom calendar scripts and styles
    wp_enqueue_style('events-calendar-styles', plugin_dir_url(__FILE__) . 'css/events-calendar.css');
    wp_enqueue_style('single-events-styles', plugin_dir_url(__FILE__) . 'css/single-event.css');
    wp_enqueue_style('events-rollup-styles', plugin_dir_url(__FILE__) . 'css/events-rollup.css');
    wp_enqueue_script('events-calendar', plugin_dir_url(__FILE__) . 'js/events-calendar.js', ['fullcalendar-core'], null, true);


    // Localize script to pass AJAX URL
    wp_localize_script('events-calendar', 'eventsCalendar', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));

    // Enqueue the countdown script and styles
    wp_enqueue_script('event-countdown', plugin_dir_url(__FILE__) . 'js/countdown.js', ['jquery'], null, true);
    wp_enqueue_style('event-countdown-styles', plugin_dir_url(__FILE__) . 'css/event-countdown.css');
}
add_action('wp_enqueue_scripts', 'enqueue_scripts');


function enqueue_slick_scripts()
{
    // Check if Slick is already enqueued
    if (!wp_script_is('slick-js', 'enqueued')) {
        wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
        wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
        wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);
    }

    // Enqueue the custom initialization script
    wp_enqueue_script('slick-init-js', plugin_dir_url(__FILE__) . 'js/slick-init.js', array('jquery', 'slick-js'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_slick_scripts');


// Enqueue the featured event toggle script for the admin page
function enqueue_admin_scripts($hook)
{
    global $post_type;

    if ($post_type == 'event' && ($hook == 'edit.php' || $hook == 'post.php' || $hook == 'post-new.php')) {
        // Enqueue the featured event toggle script
        wp_enqueue_script('featured-event-toggle', plugin_dir_url(__FILE__) . 'js/featured-event-toggle.js', array('jquery'), null, true);
        wp_localize_script('featured-event-toggle', 'featuredEventToggle', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('toggle_featured_event')
        ));

        // Enqueue the custom script for related events
        wp_enqueue_script('related-events-selector', plugin_dir_url(__FILE__) . 'js/related-events-selector.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');




// Flush rewrite rules on activation
function events_plugin_activate()
{
    if (function_exists('register_event_post_type')) {
        register_event_post_type();
    }
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'events_plugin_activate');

// Flush rewrite rules on deactivation
function events_plugin_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'events_plugin_deactivate');

function add_query_vars_filter($vars)
{
    $vars[] = 'paged';
    return $vars;
}
add_filter('query_vars', 'add_query_vars_filter');

function custom_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'custom_excerpt_more');

// Customize the label text for the organizer taxonomy input field
function custom_organizer_taxonomy_labels($args, $taxonomy)
{
    if ('organizer' === $taxonomy) {
        $args['labels']['separate_items_with_commas'] = __('Separate organizers with commas', 'basic-events');
    }
    return $args;
}
add_filter('register_taxonomy_args', 'custom_organizer_taxonomy_labels', 10, 2);


// Remove Date column from the event post type list table
add_filter('manage_event_posts_columns', 'remove_unwanted_event_columns');
function remove_unwanted_event_columns($columns)
{
    unset($columns['date']);
    return $columns;
}

// Remove Yoast SEO columns
add_filter('wpseo_always_register_metaboxes_on', 'remove_yoast_columns_for_events');
function remove_yoast_columns_for_events($post_types)
{
    if (($key = array_search('event', $post_types)) !== false) {
        unset($post_types[$key]);
    }
    return $post_types;
}

// Remove Yoast SEO columns from the event post type list table
add_filter('manage_edit-event_columns', 'remove_yoast_event_columns');
function remove_yoast_event_columns($columns)
{
    unset($columns['wpseo-score']);
    unset($columns['wpseo-score-readability']);
    unset($columns['wpseo-title']);
    unset($columns['wpseo-metadesc']);
    unset($columns['wpseo-links']); // Remove "linked to"
    unset($columns['wpseo-linked']); // Remove "linked from"
    return $columns;
}

// Remove Yoast SEO columns from other custom post type list tables if needed
add_filter('manage_edit-event_sortable_columns', 'remove_yoast_event_sortable_columns');
function remove_yoast_event_sortable_columns($columns)
{
    unset($columns['wpseo-score']);
    unset($columns['wpseo-score-readability']);
    unset($columns['wpseo-title']);
    unset($columns['wpseo-metadesc']);
    unset($columns['wpseo-links']); // Remove "linked to"
    unset($columns['wpseo-linked']); // Remove "linked from"
    return $columns;
}
