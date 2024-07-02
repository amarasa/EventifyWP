<?php

// Hook into the 'add_meta_boxes' action to add custom meta boxes
add_action('add_meta_boxes', 'venue_details_meta_boxes');

function venue_details_meta_boxes()
{
    add_meta_box(
        'venue_details',
        __('Venue Details', 'basic-events'),
        'render_venue_details_meta_box',
        'event',
        'normal',
        'high'
    );
}

function render_venue_details_meta_box($post)
{
    wp_nonce_field('save_venue_details', 'venue_details_nonce');

    $location = get_post_meta($post->ID, '_event_location', true);
    $show_map = get_post_meta($post->ID, '_event_show_map', true);
    $venue_name = get_post_meta($post->ID, '_event_venue_name', true);
    $additional_info = get_post_meta($post->ID, '_event_additional_info', true);
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
        <label for="event_additional_info"><?php _e('Additional Venue Information:', 'basic-events'); ?></label>
    </p>
    <textarea id="event_additional_info" name="event_additional_info" rows="4" cols="50"><?php echo esc_textarea($additional_info); ?></textarea>
    <p><small><?php _e('Provide additional information such as parking instructions, arrival time, what you can/can\'t bring, etc.', 'basic-events'); ?></small></p>
<?php
}

function save_venue_details($post_id)
{
    if (!isset($_POST['venue_details_nonce']) || !wp_verify_nonce($_POST['venue_details_nonce'], 'save_venue_details')) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if (isset($_POST['post_type']) && 'event' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    $location = sanitize_text_field($_POST['event_location']);
    update_post_meta($post_id, '_event_location', $location);

    $show_map = isset($_POST['event_show_map']) ? '1' : '0';
    update_post_meta($post_id, '_event_show_map', $show_map);

    $venue_name = sanitize_text_field($_POST['event_venue_name']);
    update_post_meta($post_id, '_event_venue_name', $venue_name);

    $additional_info = sanitize_textarea_field($_POST['event_additional_info']);
    update_post_meta($post_id, '_event_additional_info', $additional_info);
}

add_action('save_post', 'save_venue_details');
?>