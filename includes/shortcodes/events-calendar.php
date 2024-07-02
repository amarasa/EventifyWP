<?php
function events_calendar_shortcode()
{
    ob_start();
?>
    <div id="events-calendar"></div>
<?php
    return ob_get_clean();
}
add_shortcode('events_calendar', 'events_calendar_shortcode');
