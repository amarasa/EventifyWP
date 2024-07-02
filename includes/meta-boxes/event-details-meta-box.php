<?php

// Hook into the 'add_meta_boxes' action to add custom meta boxes
add_action('add_meta_boxes', 'event_details_meta_boxes');

function event_details_meta_boxes()
{
    add_meta_box(
        'event_details',
        __('Event Details', 'basic-events'),
        'render_event_details_meta_box',
        'event',
        'normal',
        'high'
    );
}

function render_event_details_meta_box($post)
{
    wp_nonce_field('save_event_details', 'event_details_nonce');

    $start_date = get_post_meta($post->ID, '_event_start_date', true);
    $start_time = get_post_meta($post->ID, '_event_start_time', true);
    $end_date = get_post_meta($post->ID, '_event_end_date', true);
    $end_time = get_post_meta($post->ID, '_event_end_time', true);
    $contact_phone = get_post_meta($post->ID, '_event_contact_phone', true);
    $contact_email = get_post_meta($post->ID, '_event_contact_email', true);
    $cost_to_attend = get_post_meta($post->ID, '_event_cost_to_attend', true);
    $recurrence_type = get_post_meta($post->ID, '_event_recurrence_type', true);
    $recurrence_interval = get_post_meta($post->ID, '_event_recurrence_interval', true);
    $recurrence_end_date = get_post_meta($post->ID, '_event_recurrence_end_date', true);
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
    <hr>
    <h3><?php _e('Recurrence Settings', 'basic-events'); ?></h3>
    <p>
        <label for="event_recurrence_type"><?php _e('Recurrence Type:', 'basic-events'); ?></label>
        <select id="event_recurrence_type" name="event_recurrence_type">
            <option value="none" <?php selected($recurrence_type, 'none'); ?>><?php _e('None', 'basic-events'); ?></option>
            <option value="daily" <?php selected($recurrence_type, 'daily'); ?>><?php _e('Daily', 'basic-events'); ?></option>
            <option value="weekly" <?php selected($recurrence_type, 'weekly'); ?>><?php _e('Weekly', 'basic-events'); ?></option>
            <option value="monthly" <?php selected($recurrence_type, 'monthly'); ?>><?php _e('Monthly', 'basic-events'); ?></option>
        </select>
    </p>
    <p>
        <label for="event_recurrence_interval"><?php _e('Recurrence Interval:', 'basic-events'); ?></label>
        <input type="number" id="event_recurrence_interval" name="event_recurrence_interval" value="<?php echo esc_attr($recurrence_interval); ?>" min="1" />
    </p>
    <p>
        <label for="event_recurrence_end_date"><?php _e('Recurrence End Date:', 'basic-events'); ?></label>
        <input type="date" id="event_recurrence_end_date" name="event_recurrence_end_date" value="<?php echo esc_attr($recurrence_end_date); ?>" />
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


function save_event_details($post_id)
{
    if (!isset($_POST['event_details_nonce']) || !wp_verify_nonce($_POST['event_details_nonce'], 'save_event_details')) {
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

    // Save recurrence fields
    $recurrence_type = sanitize_text_field($_POST['event_recurrence_type']);
    update_post_meta($post_id, '_event_recurrence_type', $recurrence_type);

    $recurrence_interval = sanitize_text_field($_POST['event_recurrence_interval']);
    update_post_meta($post_id, '_event_recurrence_interval', $recurrence_interval);

    $recurrence_end_date = sanitize_text_field($_POST['event_recurrence_end_date']);
    update_post_meta($post_id, '_event_recurrence_end_date', $recurrence_end_date);

    // Generate and save recurrence dates
    if ($recurrence_type && $start_date && $recurrence_interval && $recurrence_end_date) {
        $recurrence_dates = generate_recurrence_dates($start_date, $recurrence_type, $recurrence_interval, $recurrence_end_date);
        update_post_meta($post_id, '_event_recurrence_dates', $recurrence_dates);
    }
}

add_action('save_post', 'save_event_details');
