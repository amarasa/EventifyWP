<?php

// Hook into the 'add_meta_boxes' action to add custom meta boxes
add_action('add_meta_boxes', 'related_events_meta_boxes');

function related_events_meta_boxes()
{
    add_meta_box(
        'related_events_meta_box',
        __('Related Events', 'basic-events'),
        'render_related_events_meta_box',
        'event',
        'normal',
        'high'
    );
}

function render_related_events_meta_box($post)
{
    wp_nonce_field('save_related_events', 'related_events_nonce');

    $related_events = get_post_meta($post->ID, '_related_events', true);
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

function save_related_events($post_id)
{
    if (!isset($_POST['related_events_nonce']) || !wp_verify_nonce($_POST['related_events_nonce'], 'save_related_events')) {
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

add_action('save_post', 'save_related_events');
?>