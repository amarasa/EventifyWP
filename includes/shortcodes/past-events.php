<?php

// Shortcode to display past events
function past_events_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'start' => '', // Start date
        'end' => '',   // End date
    ), $atts, 'past_events');

    $query_args = array(
        'post_type' => 'event',
        'posts_per_page' => -1,
        'meta_key' => '_event_start_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_event_start_date',
                'value' => date('Y-m-d'),
                'compare' => '<',
                'type' => 'DATE'
            )
        )
    );

    if (!empty($atts['start'])) {
        $query_args['meta_query'][] = array(
            'key' => '_event_start_date',
            'value' => $atts['start'],
            'compare' => '<=',
            'type' => 'DATE'
        );
    }

    if (!empty($atts['end'])) {
        $query_args['meta_query'][] = array(
            'key' => '_event_start_date',
            'value' => $atts['end'],
            'compare' => '>=',
            'type' => 'DATE'
        );
    }

    $events = new WP_Query($query_args);

    if ($events->have_posts()) {
        $output = '<ul class="past-events">';
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
        $output = '<p>No past events found.</p>';
    }

    return $output;
}
add_shortcode('past_events', 'past_events_shortcode');
