<?php
// Hook into the 'add_meta_boxes' action to add custom meta boxes
add_action('add_meta_boxes', 'event_shortcode_meta_box');

function event_shortcode_meta_box()
{
    add_meta_box(
        'event_shortcode',
        __('Event Shortcode', 'basic-events'),
        'render_event_shortcode_meta_box',
        'event',
        'side',
        'default'
    );
}

function render_event_shortcode_meta_box($post)
{
    $event_id = $post->ID;
    echo '<p>' . __('Use the shortcode below to display this event:', 'basic-events') . '</p>';
    echo '<code>[single_event id="' . $event_id . '"]</code>';
}
