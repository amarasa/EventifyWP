<?php
// Hook into the 'add_meta_boxes' action to add custom meta boxes
add_action('add_meta_boxes', 'event_images_meta_box');

function event_images_meta_box()
{
    add_meta_box(
        'event_images',
        __('Event Images', 'basic-events'),
        'render_event_images_meta_box',
        'event',
        'normal',
        'high'
    );
}

function render_event_images_meta_box($post)
{
    wp_nonce_field('save_event_images', 'event_images_nonce');
    $event_images = get_post_meta($post->ID, '_event_images', true);
?>
    <div id="event-images-container">
        <ul id="event-images-list">
            <?php
            if ($event_images) {
                foreach ($event_images as $image) {
                    echo '<li><img src="' . wp_get_attachment_url($image) . '" style="max-width:150px;" /><input type="hidden" name="event_images[]" value="' . esc_attr($image) . '"><button type="button" class="remove-image-button button">Remove</button></li>';
                }
            }
            ?>
        </ul>
        <button type="button" id="add-event-image-button" class="button">Add Image</button>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('#add-event-image-button').click(function(e) {
                e.preventDefault();
                var imageFrame;
                if (imageFrame) {
                    imageFrame.open();
                    return;
                }
                imageFrame = wp.media({
                    title: 'Select Event Images',
                    button: {
                        text: 'Add to Event'
                    },
                    multiple: true
                });

                imageFrame.on('select', function() {
                    var attachments = imageFrame.state().get('selection').toJSON();
                    attachments.forEach(function(attachment) {
                        $('#event-images-list').append('<li><img src="' + attachment.url + '" style="max-width:150px;" /><input type="hidden" name="event_images[]" value="' + attachment.id + '"><button type="button" class="remove-image-button button">Remove</button></li>');
                    });
                });

                imageFrame.open();
            });

            $(document).on('click', '.remove-image-button', function(e) {
                e.preventDefault();
                $(this).closest('li').remove();
            });
        });
    </script>
<?php
}

function save_event_images($post_id)
{
    if (!isset($_POST['event_images_nonce']) || !wp_verify_nonce($_POST['event_images_nonce'], 'save_event_images')) {
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

    $event_images = isset($_POST['event_images']) ? array_map('sanitize_text_field', $_POST['event_images']) : [];
    update_post_meta($post_id, '_event_images', $event_images);
}

add_action('save_post', 'save_event_images');
