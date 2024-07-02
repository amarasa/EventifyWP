<?php

// Shortcode to display upcoming events by category
function upcoming_events_by_category_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'category' => '',
        'count' => 5,
    ), $atts, 'upcoming_events_by_category');

    if (empty($atts['category'])) {
        return '<p>No category specified.</p>';
    }

    $query_args = array(
        'post_type' => 'event',
        'posts_per_page' => $atts['count'],
        'meta_key' => '_event_start_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'event_category',
                'field' => 'slug',
                'terms' => $atts['category'],
            ),
        ),
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
        $output = '<p>No upcoming events found for this category.</p>';
    }

    return $output;
}
add_shortcode('upcoming_events_by_category', 'upcoming_events_by_category_shortcode');
