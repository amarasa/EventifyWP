<?php

// Hook into the 'add_meta_boxes' action to add custom meta boxes
add_action('add_meta_boxes', 'event_meta_boxes');

function event_meta_boxes()
{
    add_meta_box(
        'event_details',
        __('Event Details', 'basic-events'),
        'render_event_details_meta_box',
        'event',
        'normal',
        'high'
    );

    add_meta_box(
        'venue_details',
        __('Venue Details', 'basic-events'),
        'render_venue_details_meta_box',
        'event',
        'normal',
        'high'
    );

    add_meta_box(
        'related_events_meta_box',
        __('Related Events', 'basic-events'),
        'render_related_events_meta_box',
        'event',
        'normal',
        'high'
    );
}

// Render the meta box for event details
function render_event_details_meta_box($post)
{
    // Add a nonce field for security
    wp_nonce_field('save_event_details', 'event_details_nonce');

    // Retrieve existing values from the database
    $start_date = get_post_meta($post->ID, '_event_start_date', true);
    $start_time = get_post_meta($post->ID, '_event_start_time', true);
    $end_date = get_post_meta($post->ID, '_event_end_date', true);
    $end_time = get_post_meta($post->ID, '_event_end_time', true);
    $contact_phone = get_post_meta($post->ID, '_event_contact_phone', true);
    $contact_email = get_post_meta($post->ID, '_event_contact_email', true);
    $cost_to_attend = get_post_meta($post->ID, '_event_cost_to_attend', true);

    // Display the form fields
?>
    <p>
        <label for="event_start_date"><?php _e('Start Date:', 'basic-events'); ?></label>
        <input type="date" id="event_start_date" name="event_start_date" value="<?php echo esc_attr($start_date); ?>" />
        <label for="event_start_time"><?php _e('Start Time:', 'basic-events'); ?></label>
        <input type="time" id="event_start_time" name="event_start_time" value="<?php echo esc_attr($start_time); ?>" />
    </p>
    <p>
        <label for="event_end_date"><?php _e('End Date:', 'basic-events'); ?></label>
        <input type="date" id="event_end_date" name="event_end_date" value="<?php echo esc_attr($end_date); ?>" />
        <label for="event_end_time"><?php _e('End Time:', 'basic-events'); ?></label>
        <input type="time" id="event_end_time" name="event_end_time" value="<?php echo esc_attr($end_time); ?>" />
    </p>
    <p>
        <label for="event_contact_phone"><?php _e('Contact Phone:', 'basic-events'); ?></label>
        <input type="tel" id="event_contact_phone" name="event_contact_phone" value="<?php echo esc_attr($contact_phone); ?>" size="25" pattern="\(\d{3}\) \d{3}-\d{4}" placeholder="(###) ###-####" />
    </p>
    <p>
        <label for="event_contact_email"><?php _e('Contact Email:', 'basic-events'); ?></label>
        <input type="email" id="event_contact_email" name="event_contact_email" value="<?php echo esc_attr($contact_email); ?>" size="25" />
    </p>
    <p>
        <label for="event_cost_to_attend"><?php _e('Cost to Attend:', 'basic-events'); ?></label>
        <input type="number" id="event_cost_to_attend" name="event_cost_to_attend" value="<?php echo esc_attr($cost_to_attend); ?>" step="0.01" min="0" />
        <label for="event_free">
            <input type="checkbox" id="event_free" name="event_free" value="1" <?php checked($cost_to_attend, '0'); ?>>
            <?php _e('Free Event', 'basic-events'); ?>
        </label>
    </p>
    <script>
        jQuery(document).ready(function($) {
            function toggleCostField() {
                if ($('#event_free').is(':checked')) {
                    $('#event_cost_to_attend').hide();
                } else {
                    $('#event_cost_to_attend').show();
                }
            }

            $('#event_free').change(function() {
                toggleCostField();
            });

            toggleCostField(); // Initial check
        });
    </script>
<?php
}

// Render the meta box for venue details
function render_venue_details_meta_box($post)
{
    // Add a nonce field for security
    wp_nonce_field('save_venue_details', 'venue_details_nonce');

    // Retrieve existing values from the database
    $location = get_post_meta($post->ID, '_event_location', true);
    $show_map = get_post_meta($post->ID, '_event_show_map', true);
    $venue_name = get_post_meta($post->ID, '_event_venue_name', true);
    $additional_info = get_post_meta($post->ID, '_event_additional_info', true);

    // Display the form fields
?>
    <p>
        <label for="event_venue_name"><?php _e('Venue Name:', 'basic-events'); ?></label>
        <input type="text" id="event_venue_name" name="event_venue_name" value="<?php echo esc_attr($venue_name); ?>" size="25" />
    </p>
    <p>
        <label for="event_location"><?php _e('Location:', 'basic-events'); ?></label>
        <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr($location); ?>" size="25" />
    </p>
    <p>
        <label for="event_show_map"><?php _e('Show Map', 'basic-events'); ?></label>
        <label class="switch">
            <input type="checkbox" id="event_show_map" name="event_show_map" value="1" <?php checked($show_map, '1'); ?>>
            <span class="slider"></span>
        </label>
    </p>
    <p>
    <p><label for="event_additional_info"><?php _e('Additional Venue Information:', 'basic-events'); ?></label></p>
    <textarea id="event_additional_info" name="event_additional_info" rows="4" cols="50"><?php echo esc_textarea($additional_info); ?></textarea>
    <p><small><?php _e('Provide additional information such as parking instructions, arrival time, what you can/can\'t bring, etc.', 'basic-events'); ?></small></p>
    </p>
<?php
}

// Render the meta box for related events
function render_related_events_meta_box($post)
{
    // Add a nonce field for security
    wp_nonce_field('save_related_events', 'related_events_nonce');

    // Retrieve existing value from the database
    $related_events = get_post_meta($post->ID, '_related_events', true);

    // Display the post selector
?>
    <div id="related-events-selector">
        <input type="text" id="related-events-search" placeholder="<?php _e('Search events...', 'basic-events'); ?>" />
        <div class="related-events-boxes">
            <div class="all-events">
                <h4><?php _e('All Events', 'basic-events'); ?></h4>
                <select id="all-events-list" size="10" style="width: 100%;">
                    <?php
                    $events = get_posts(array('post_type' => 'event', 'posts_per_page' => -1));
                    foreach ($events as $event) {
                        if ($event->ID != $post->ID && (!is_array($related_events) || !in_array($event->ID, $related_events))) {
                            echo '<option value="' . $event->ID . '">' . $event->post_title . '</option>';
                        }
                    }
                    ?>
                </select>
                <button type="button" id="add-related-event" class="button">+</button>
            </div>
            <div class="related-events">
                <h4><?php _e('Related Events', 'basic-events'); ?></h4>
                <select id="related-events-list" name="related_events[]" multiple size="10" style="width: 100%;">
                    <?php
                    if (is_array($related_events)) {
                        foreach ($related_events as $related_event_id) {
                            $related_event = get_post($related_event_id);
                            if ($related_event) {
                                echo '<option value="' . $related_event->ID . '">' . $related_event->post_title . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
                <button type="button" id="remove-related-event" class="button">-</button>
            </div>
        </div>
    </div>
<?php
}

// Save the event details meta box data
add_action('save_post', 'save_event_details');
function save_event_details($post_id)
{
    // Check if our nonce is set.
    if (!isset($_POST['event_details_nonce'])) {
        return $post_id;
    }

    $nonce = $_POST['event_details_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'save_event_details')) {
        return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'event' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    // Sanitize user input and update the meta fields
    $start_date = sanitize_text_field($_POST['event_start_date']);
    update_post_meta($post_id, '_event_start_date', $start_date);

    $start_time = sanitize_text_field($_POST['event_start_time']);
    update_post_meta($post_id, '_event_start_time', $start_time);

    $end_date = sanitize_text_field($_POST['event_end_date']);
    update_post_meta($post_id, '_event_end_date', $end_date);

    $end_time = sanitize_text_field($_POST['event_end_time']);
    update_post_meta($post_id, '_event_end_time', $end_time);

    $contact_phone = sanitize_text_field($_POST['event_contact_phone']);
    update_post_meta($post_id, '_event_contact_phone', $contact_phone);

    $contact_email = sanitize_email($_POST['event_contact_email']);
    update_post_meta($post_id, '_event_contact_email', $contact_email);

    $cost_to_attend = sanitize_text_field($_POST['event_cost_to_attend']);
    if (isset($_POST['event_free']) && $_POST['event_free'] == '1') {
        $cost_to_attend = '0';
    }
    update_post_meta($post_id, '_event_cost_to_attend', $cost_to_attend);
}

// Save the venue details meta box data
add_action('save_post', 'save_venue_details');
function save_venue_details($post_id)
{
    // Check if our nonce is set.
    if (!isset($_POST['venue_details_nonce'])) {
        return $post_id;
    }

    $nonce = $_POST['venue_details_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'save_venue_details')) {
        return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'event' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    // Sanitize user input and update the meta fields
    $location = sanitize_text_field($_POST['event_location']);
    update_post_meta($post_id, '_event_location', $location);

    $show_map = isset($_POST['event_show_map']) ? '1' : '0';
    update_post_meta($post_id, '_event_show_map', $show_map);

    $venue_name = sanitize_text_field($_POST['event_venue_name']);
    update_post_meta($post_id, '_event_venue_name', $venue_name);

    $additional_info = sanitize_textarea_field($_POST['event_additional_info']);
    update_post_meta($post_id, '_event_additional_info', $additional_info);
}

// Save the related events meta box data
add_action('save_post', 'save_related_events');
function save_related_events($post_id)
{
    // Check if our nonce is set.
    if (!isset($_POST['related_events_nonce'])) {
        return $post_id;
    }

    $nonce = $_POST['related_events_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'save_related_events')) {
        return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'event' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    // Sanitize user input and update the meta field
    $related_events = isset($_POST['related_events']) ? array_map('intval', $_POST['related_events']) : [];
    update_post_meta($post_id, '_related_events', $related_events);

    // Sync related events
    foreach ($related_events as $related_event_id) {
        $current_related = get_post_meta($related_event_id, '_related_events', true) ?: [];
        if (!in_array($post_id, $current_related)) {
            $current_related[] = $post_id;
            update_post_meta($related_event_id, '_related_events', $current_related);
        }
    }

    // Remove current post from other related events
    $all_events = get_posts(array('post_type' => 'event', 'posts_per_page' => -1, 'post__not_in' => [$post_id]));
    foreach ($all_events as $event) {
        $current_related = get_post_meta($event->ID, '_related_events', true) ?: [];
        if (in_array($post_id, $current_related) && !in_array($event->ID, $related_events)) {
            $current_related = array_diff($current_related, [$post_id]);
            update_post_meta($event->ID, '_related_events', $current_related);
        }
    }
}
