<?php
function event_countdown_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'id' => '',
        'style' => 'plain'
    ), $atts, 'event_countdown');

    $event_id = $atts['id'];
    $event = get_post($event_id);

    if (!$event) {
        return '<p>Event not found.</p>';
    }

    $end_date = get_post_meta($event_id, '_event_end_date', true);
    $end_time = get_post_meta($event_id, '_event_end_time', true);
    $end_date_time = $end_date . ' ' . $end_time;

    ob_start();
?>
    <div class="event-countdown <?php echo esc_attr($atts['style']); ?>" data-end-date="<?php echo esc_attr($end_date_time); ?>" data-style="<?php echo esc_attr($atts['style']); ?>">
        <!-- Countdown will be inserted here by JS -->
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('event_countdown', 'event_countdown_shortcode');
