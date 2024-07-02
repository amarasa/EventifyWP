<?php
function featured_events_shortcode($atts)
{
    $default_per_page = 2; // default number of events per page
    $atts = shortcode_atts(array(
        'count' => $default_per_page,
    ), $atts, 'featured_events');

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // Determine the number of posts per page
    if (($atts['count'] === 'all') || (intval($atts['count'] >  $default_per_page))) {
        $posts_per_page = $default_per_page;
        $show_pagination = true;
    } else {
        $posts_per_page = intval($atts['count']);
        $show_pagination = $posts_per_page > $default_per_page;
    }

    // Adjust the query arguments based on 'count'
    $query_args = array(
        'post_type' => 'event',
        'posts_per_page' => ($atts['count'] === 'all' ? $default_per_page : $posts_per_page),
        'paged' => $paged,
        'meta_query' => array(
            array(
                'key' => '_featured_event',
                'value' => '1',
                'compare' => '='
            )
        )
    );

    $events = new WP_Query($query_args);

    if ($events->have_posts()) {
        ob_start();
        echo '<div class="featured-events">';
        while ($events->have_posts()) {
            $events->the_post();
            echo '<div class="featured-event">';
            echo '<h2>' . get_the_title() . '</h2>';
            echo '<p>' . get_the_excerpt() . '</p>';
            echo '<a href="' . get_permalink() . '">' . __('Read More', 'basic-events') . '</a>';
            echo '</div>';
        }
        echo '</div>';

        // Pagination
        if ($atts['count'] === 'all' || $show_pagination) {
            echo '<div class="pagination">';
            echo paginate_links(array(
                'total' => $events->max_num_pages,
                'current' => $paged,
            ));
            echo '</div>';
        }

        wp_reset_postdata();
        return ob_get_clean();
    } else {
        return '<p>' . __('No featured events found.', 'basic-events') . '</p>';
    }
}
add_shortcode('featured_events', 'featured_events_shortcode');
