<?php
function fetch_events()
{
    $events = [];
    $query = new WP_Query(array(
        'post_type' => 'event',
        'posts_per_page' => -1,
    ));

    while ($query->have_posts()) : $query->the_post();
        $event = array(
            'title' => get_the_title(),
            'start' => get_post_meta(get_the_ID(), '_event_start_date', true) . 'T' . get_post_meta(get_the_ID(), '_event_start_time', true),
            'end' => get_post_meta(get_the_ID(), '_event_end_date', true) . 'T' . get_post_meta(get_the_ID(), '_event_end_time', true),
            'url' => get_permalink()
        );
        $events[] = $event;
    endwhile;

    wp_reset_postdata();
    wp_send_json($events);
}
add_action('wp_ajax_fetch_events', 'fetch_events');
add_action('wp_ajax_nopriv_fetch_events', 'fetch_events');
