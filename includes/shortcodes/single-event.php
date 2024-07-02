<?php

// Shortcode to display a single event
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

    $location = get_post_meta($event_id, '_event_location', true);
    $start_date = get_post_meta($event_id, '_event_start_date', true);
    $start_time = get_post_meta($event_id, '_event_start_time', true);
    $end_date = get_post_meta($event_id, '_event_end_date', true);
    $end_time = get_post_meta($event_id, '_event_end_time', true);
    $show_map = get_post_meta($event_id, '_event_show_map', true);
    $featured_image = get_the_post_thumbnail_url($event_id, 'full');

    ob_start();
?>
    <div class="single-event-details">
        <h1 class="event-title"><?php echo get_the_title($event_id); ?></h1>

        <?php if ($featured_image) : ?>
            <div class="event-featured-image">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo get_the_title($event_id); ?>">
            </div>
        <?php endif; ?>

        <div class="event-meta">
            <?php if ($location) : ?>
                <p><strong><?php _e('Location:', 'basic-events'); ?></strong> <?php echo esc_html($location); ?></p>
            <?php endif; ?>
            <?php if ($start_date && $start_time) : ?>
                <p><strong><?php _e('Start:', 'basic-events'); ?></strong> <?php echo esc_html($start_date . ' ' . $start_time); ?></p>
            <?php elseif ($start_date) : ?>
                <p><strong><?php _e('Start Date:', 'basic-events'); ?></strong> <?php echo esc_html($start_date); ?></p>
            <?php endif; ?>
            <?php if ($end_date && $end_time) : ?>
                <p><strong><?php _e('End:', 'basic-events'); ?></strong> <?php echo esc_html($end_date . ' ' . $end_time); ?></p>
            <?php elseif ($end_date) : ?>
                <p><strong><?php _e('End Date:', 'basic-events'); ?></strong> <?php echo esc_html($end_date); ?></p>
            <?php endif; ?>
        </div>

        <div class="event-content">
            <?php echo apply_filters('the_content', $post->post_content); ?>
        </div>

        <?php if ($show_map && $location) : ?>
            <div id="event_map_<?php echo $event_id; ?>" style="width: 100%; height: 400px;"></div>
            <script>
                function initMap_<?php echo $event_id; ?>() {
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        'address': '<?php echo esc_js($location); ?>'
                    }, function(results, status) {
                        if (status === 'OK') {
                            var mapOptions = {
                                zoom: 15,
                                center: results[0].geometry.location
                            };
                            var map = new google.maps.Map(document.getElementById('event_map_<?php echo $event_id; ?>'), mapOptions);
                            var marker = new google.maps.Marker({
                                position: results[0].geometry.location,
                                map: map
                            });
                        }
                    });
                }
                google.maps.event.addDomListener(window, 'load', initMap_<?php echo $event_id; ?>);
            </script>
            <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr(get_option('events_plugin_google_api_key')); ?>&callback=initMap_<?php echo $event_id; ?>" async defer></script>
        <?php endif; ?>
    </div>
<?php

    $output = ob_get_clean();

    wp_reset_postdata();

    return $output;
}
add_shortcode('single_event', 'single_event_shortcode');
