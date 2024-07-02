<?php

// Shortcode to display upcoming events
function upcoming_events_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'count' => 5, // Number of events to display
    ), $atts, 'upcoming_events');

    $query_args = array(
        'post_type' => 'event',
        'posts_per_page' => $atts['count'],
        'meta_key' => '_event_start_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_event_start_date',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE'
            )
        )
    );

    $events = new WP_Query($query_args);

    if ($events->have_posts()) {
        $output = '<ul class="upcoming-events">';
        while ($events->have_posts()) {
            $events->the_post();
            $start_date = get_post_meta(get_the_ID(), '_event_start_date', true);
            $output .= '<li>';
            $output .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            $output .= ' - ' . esc_html($start_date);
            $output .= '</li>';
        }
        $output .= '</ul>';
        wp_reset_postdata();
    } else {
        $output = '<p>No upcoming events found.</p>';
    }

    return $output;
}
add_shortcode('upcoming_events', 'upcoming_events_shortcode');
