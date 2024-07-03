<?php
function single_event_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'id' => 0, // Event ID
    ), $atts, 'single_event');

    $event_id = $atts['id'];

    if (!$event_id) {
        return '<p>No event found.</p>';
    }

    $post = get_post($event_id);

    if (!$post || $post->post_type != 'event') {
        return '<p>No event found.</p>';
    }

    setup_postdata($post);

    ob_start();
    echo render_single_event_details($event_id);
    $output = ob_get_clean();

    wp_reset_postdata();

    return $output;
}
add_shortcode('single_event', 'single_event_shortcode');
