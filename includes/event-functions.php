<?php
function render_single_event_details($event_id)
{
    $location = get_post_meta($event_id, '_event_location', true);
    $start_date = get_post_meta($event_id, '_event_start_date', true);
    $start_time = get_post_meta($event_id, '_event_start_time', true);
    $end_date = get_post_meta($event_id, '_event_end_date', true);
    $end_time = get_post_meta($event_id, '_event_end_time', true);
    $show_map = get_post_meta($event_id, '_event_show_map', true);
    $featured_image = get_the_post_thumbnail_url($event_id, 'full');
    $categories = get_the_terms($event_id, 'event_category');
    $tags = get_the_terms($event_id, 'post_tag');
    $contact_phone = get_post_meta($event_id, '_event_contact_phone', true);
    $contact_email = get_post_meta($event_id, '_event_contact_email', true);
    $cost_to_attend = get_post_meta($event_id, '_event_cost_to_attend', true);
    $related_events = get_post_meta($event_id, '_related_events', true);
    $venue_name = get_post_meta($event_id, '_event_venue_name', true);
    $additional_info = get_post_meta($event_id, '_event_additional_info', true);
    $is_featured = get_post_meta($event_id, '_featured_event', true);
    $recurrence_dates = get_post_meta($event_id, '_event_recurrence_dates', true);
    $event_images = get_post_meta($event_id, '_event_images', true);

    ob_start();
?>
    <?php if ($is_featured) : ?>
        <div class="featured-event-banner">
            <p><?php _e('***Featured Event***', 'basic-events'); ?></p>
        </div>
    <?php endif; ?>

    <div class="single-event event-details<?php echo $is_featured ? ' featured-event' : ''; ?>">
        <?php if ($featured_image) : ?>
            <div class="event-featured-image">
                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo get_the_title($event_id); ?>">
            </div>
        <?php endif; ?>

        <h1 class="event-title"><?php echo get_the_title($event_id); ?></h1>

        <div class="event-content">
            <?php echo apply_filters('the_content', get_post_field('post_content', $event_id)); ?>
        </div>

        <div class="event-details-section">
            <h2>Details</h2>
            <?php if ($start_date) : ?>
                <p><strong><?php _e('Date:', 'basic-events'); ?></strong>
                    <?php
                    echo date('l, F j, Y', strtotime($start_date));
                    if ($end_date && $start_date != $end_date) {
                        echo ' - ' . date('l, F j, Y', strtotime($end_date));
                    }
                    ?>
                </p>
            <?php endif; ?>
            <?php if ($start_time || $end_time) : ?>
                <p><strong><?php _e('Time:', 'basic-events'); ?></strong>
                    <?php
                    if ($start_time) {
                        echo date('g:i A', strtotime($start_time));
                    }
                    if ($end_time) {
                        echo ' - ' . date('g:i A', strtotime($end_time));
                    }
                    ?>
                </p>
            <?php endif; ?>

            <?php if ($categories && !is_wp_error($categories)) : ?>
                <p><strong><?php _e('Event Category:', 'basic-events'); ?></strong>
                    <?php
                    $category_list = array();
                    foreach ($categories as $category) {
                        $category_list[] = '<a href="' . get_term_link($category) . '">' . $category->name . '</a>';
                    }
                    echo implode(', ', $category_list);
                    ?>
                </p>
            <?php endif; ?>
            <?php if ($tags && !is_wp_error($tags)) : ?>
                <p><strong><?php _e('Tags:', 'basic-events'); ?></strong>
                    <?php
                    $tag_list = array();
                    foreach ($tags as $tag) {
                        $tag_list[] = ucwords($tag->name);
                    }
                    echo implode(', ', $tag_list);
                    ?>
                </p>
            <?php endif; ?>
            <?php
            $organizers = get_the_terms($event_id, 'organizer');
            if ($organizers && !is_wp_error($organizers)) : ?>
                <p><strong><?php _e('Organizers:', 'basic-events'); ?></strong>
                    <?php
                    $organizer_list = array();
                    foreach ($organizers as $organizer) {
                        $organizer_list[] = '<a href="' . get_term_link($organizer) . '">' . $organizer->name . '</a>';
                    }
                    echo implode(', ', $organizer_list);
                    ?>
                </p>
            <?php endif; ?>
            <?php if ($contact_phone) : ?>
                <p><strong><?php _e('Contact Phone:', 'basic-events'); ?></strong> <?php echo esc_html($contact_phone); ?></p>
            <?php endif; ?>
            <?php if ($contact_email) : ?>
                <p><strong><?php _e('Contact Email:', 'basic-events'); ?></strong> <?php echo esc_html($contact_email); ?></p>
            <?php endif; ?>
            <p><strong><?php _e('Cost:', 'basic-events'); ?></strong>
                <?php echo ($cost_to_attend == '0' || $cost_to_attend == '') ? __('Free', 'basic-events') : '$' . esc_html($cost_to_attend); ?>
            </p>
        </div>

        <?php if ($recurrence_dates && is_array($recurrence_dates)) : ?>
            <div class="recurrence-dates-section">
                <h2><?php _e('Recurring Dates', 'basic-events'); ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'basic-events'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recurrence_dates as $date) : ?>
                            <tr>
                                <td><?php echo date('l, F j, Y', strtotime($date)); ?> at <?php echo date('g:i A', strtotime($start_time)); ?> - <?php echo date('g:i A', strtotime($end_time)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <?php if ($event_images && is_array($event_images)) : ?>
            <div class="event-images-slider">
                <?php foreach ($event_images as $image_id) : ?>
                    <div>
                        <img src="<?php echo wp_get_attachment_url($image_id); ?>" alt="Event Image">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="venue-details-section">
            <h2>Venue Details</h2>
            <?php if ($venue_name) : ?>
                <p><strong><?php _e('Venue Name:', 'basic-events'); ?></strong> <?php echo esc_html($venue_name); ?></p>
            <?php endif; ?>
            <?php if ($location) : ?>
                <p class="event-location">
                    <strong><?php _e('Address:', 'basic-events'); ?></strong>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                        <path d="M384 476.1L192 421.2V35.9L384 90.8V476.1zm32-1.2V88.4L543.1 37.5c15.8-6.3 32.9 5.3 32.9 22.3V394.6c0 9.8-6 18.6-15.1 22.3L416 474.8zM15.1 95.1L160 37.2V423.6L32.9 474.5C17.1 480.8 0 469.2 0 452.2V117.4c0-9.8 6-18.6 15.1-22.3z" />
                    </svg>
                    <?php echo esc_html($location); ?>
                </p>
            <?php endif; ?>
            <?php if ($additional_info) : ?>
                <p><strong><?php _e('Additional Venue Information:', 'basic-events'); ?></strong> <?php echo esc_html($additional_info); ?></p>
            <?php endif; ?>
            <?php if ($show_map && $location) : ?>
                <div id="event_map" style="width: 100%; height: 400px;"></div>
                <script>
                    function initMap() {
                        var geocoder = new google.maps.Geocoder();
                        geocoder.geocode({
                            'address': '<?php echo esc_js($location); ?>'
                        }, function(results, status) {
                            if (status === 'OK') {
                                var mapOptions = {
                                    zoom: 15,
                                    center: results[0].geometry.location
                                };
                                var map = new google.maps.Map(document.getElementById('event_map'), mapOptions);
                                var marker = new google.maps.Marker({
                                    position: results[0].geometry.location,
                                    map: map
                                });
                            }
                        });
                    }
                    google.maps.event.addDomListener(window, 'load', initMap);
                </script>
                <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr(get_option('events_plugin_google_api_key')); ?>&callback=initMap" async defer></script>
            <?php endif; ?>
        </div>

        <?php var_dump($related_events); ?>
        <?php if ($related_events && is_array($related_events) && count($related_events) > 0) : ?>
            <div class="related-events-section">
                <h2><?php _e('Related Events', 'basic-events'); ?></h2>
                <div class="related-events-grid">
                    <?php
                    foreach ($related_events as $related_event_id) :
                        $related_event = get_post($related_event_id);
                        if ($related_event) :
                            $related_event_image = get_the_post_thumbnail_url($related_event->ID, 'thumbnail');
                            $related_event_start_date = get_post_meta($related_event->ID, '_event_start_date', true);
                            $related_event_start_time = get_post_meta($related_event->ID, '_event_start_time', true);
                            $related_event_end_date = get_post_meta($related_event->ID, '_event_end_date', true);
                            $related_event_end_time = get_post_meta($related_event->ID, '_event_end_time', true);
                    ?>
                            <div class="related-event-item">
                                <?php if ($related_event_image) : ?>
                                    <div class="related-event-image">
                                        <img src="<?php echo esc_url($related_event_image); ?>" alt="<?php echo esc_attr($related_event->post_title); ?>">
                                    </div>
                                <?php endif; ?>
                                <div class="related-event-details">
                                    <h3 class="related-event-title"><a href="<?php echo get_permalink($related_event->ID); ?>"><?php echo esc_html($related_event->post_title); ?></a></h3>
                                    <p class="related-event-date">
                                        <?php
                                        echo date('F j', strtotime($related_event_start_date));
                                        if ($related_event_end_date && $related_event_start_date != $related_event_end_date) {
                                            echo ' - ' . date('F j', strtotime($related_event_end_date));
                                        }
                                        echo ' @ ' . date('g:i A', strtotime($related_event_start_time));
                                        if ($related_event_end_time) {
                                            echo ' - ' . date('g:i A', strtotime($related_event_end_time));
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endif; ?>

    </div>

<?php
    $output = ob_get_clean();
    return $output;
}
?>