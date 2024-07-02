<?php

// Hook into the 'init' action to register the custom post type and taxonomy
add_action('init', 'register_event_post_type_and_taxonomy');

function register_event_post_type_and_taxonomy()
{
    $labels = array(
        'name'               => _x('Events', 'post type general name', 'basic-events'),
        'singular_name'      => _x('Event', 'post type singular name', 'basic-events'),
        'menu_name'          => _x('Events', 'admin menu', 'basic-events'),
        'name_admin_bar'     => _x('Event', 'add new on admin bar', 'basic-events'),
        'add_new'            => _x('Add New', 'event', 'basic-events'),
        'add_new_item'       => __('Add New Event', 'basic-events'),
        'new_item'           => __('New Event', 'basic-events'),
        'edit_item'          => __('Edit Event', 'basic-events'),
        'view_item'          => __('View Event', 'basic-events'),
        'all_items'          => __('All Events', 'basic-events'),
        'search_items'       => __('Search Events', 'basic-events'),
        'parent_item_colon'  => __('Parent Events:', 'basic-events'),
        'not_found'          => __('No events found.', 'basic-events'),
        'not_found_in_trash' => __('No events found in Trash.', 'basic-events')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'events'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-calendar',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies'         => array('post_tag'), // Add post_tag here
    );

    register_post_type('event', $args);

    // Register custom taxonomy for events
    $taxonomy_labels = array(
        'name'              => _x('Event Categories', 'taxonomy general name', 'basic-events'),
        'singular_name'     => _x('Event Category', 'taxonomy singular name', 'basic-events'),
        'search_items'      => __('Search Event Categories', 'basic-events'),
        'all_items'         => __('All Event Categories', 'basic-events'),
        'parent_item'       => __('Parent Event Category', 'basic-events'),
        'parent_item_colon' => __('Parent Event Category:', 'basic-events'),
        'edit_item'         => __('Edit Event Category', 'basic-events'),
        'update_item'       => __('Update Event Category', 'basic-events'),
        'add_new_item'      => __('Add New Event Category', 'basic-events'),
        'new_item_name'     => __('New Event Category Name', 'basic-events'),
        'menu_name'         => __('Event Categories', 'basic-events'),
    );

    $taxonomy_args = array(
        'hierarchical'      => true,
        'labels'            => $taxonomy_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'event-category'),
    );

    register_taxonomy('event_category', array('event'), $taxonomy_args);

    // Register tags for the event post type
    register_taxonomy_for_object_type('post_tag', 'event');
}

add_action('init', 'register_event_post_type_and_taxonomy');


// Register the Organizer taxonomy for events
function register_organizer_taxonomy()
{
    $labels = array(
        'name'              => _x('Organizers', 'taxonomy general name', 'basic-events'),
        'singular_name'     => _x('Organizer', 'taxonomy singular name', 'basic-events'),
        'search_items'      => __('Search Organizers', 'basic-events'),
        'all_items'         => __('All Organizers', 'basic-events'),
        'parent_item'       => __('Parent Organizer', 'basic-events'),
        'parent_item_colon' => __('Parent Organizer:', 'basic-events'),
        'edit_item'         => __('Edit Organizer', 'basic-events'),
        'update_item'       => __('Update Organizer', 'basic-events'),
        'add_new_item'      => __('Add New Organizer', 'basic-events'),
        'new_item_name'     => __('New Organizer Name', 'basic-events'),
        'menu_name'         => __('Organizers', 'basic-events'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'organizer'),
    );

    register_taxonomy('organizer', array('event'), $args);
}
add_action('init', 'register_organizer_taxonomy');
