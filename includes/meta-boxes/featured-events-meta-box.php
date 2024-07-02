<?php
function add_featured_meta_box()
{
    add_meta_box(
        'featured_meta_box',           // Unique ID
        'Featured Event',              // Box title
        'featured_meta_box_html',      // Content callback, must be of type callable
        'event',                       // Post type
        'side',                        // Context (side, normal, advanced)
        'high'                         // Priority (high, core, default, low)
    );
}
add_action('add_meta_boxes', 'add_featured_meta_box');

function featured_meta_box_html($post)
{
    $value = get_post_meta($post->ID, '_featured_event', true);
?>
    <label for="featured_event_field"><?php _e('Mark as Featured Event', 'basic-events'); ?></label>
    <input type="checkbox" id="featured_event_field" name="featured_event_field" value="1" <?php checked($value, '1'); ?> />
<?php
}

function save_featured_meta_box_data($post_id)
{
    if (array_key_exists('featured_event_field', $_POST)) {
        update_post_meta(
            $post_id,
            '_featured_event',
            $_POST['featured_event_field']
        );
    } else {
        delete_post_meta($post_id, '_featured_event');
    }
}
add_action('save_post', 'save_featured_meta_box_data');
