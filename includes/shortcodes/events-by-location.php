<?php

require plugin_dir_path(__FILE__) . '../../vendor/autoload.php';

// Shortcode to display events by location
function events_by_location_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'type' => '',
        'value' => '',
        'exact' => 'true',
        'radius' => '',
    ), $atts, 'events_by_location');

    if (empty($atts['type']) || empty($atts['value'])) {
        return '<p>Please specify a location type and value.</p>';
    }

    $query_args = array(
        'post_type' => 'event',
        'posts_per_page' => -1,
        'meta_query' => array(),
    );

    if ($atts['type'] === 'city') {
        if ($atts['exact'] === 'true') {
            $query_args['meta_query'][] = array(
                'key' => '_event_location',
                'value' => ', ' . $atts['value'] . ', ',
                'compare' => 'LIKE'
            );
        } else if (!empty($atts['radius'])) {
            $events = new WP_Query($query_args);
            $filtered_events = array();
            foreach ($events->posts as $event) {
                $location = get_post_meta($event->ID, '_event_location', true);
                $distance = get_distance($location, $atts['value']);
                if ($distance !== null && $distance <= $atts['radius']) {
                    $filtered_events[] = $event;
                }
            }
            return display_events($filtered_events);
        }
    } else if ($atts['type'] === 'state') {
        $query_args['meta_query'][] = array(
            'key' => '_event_location',
            'value' => ', ' . $atts['value'] . ',',
            'compare' => 'LIKE'
        );
    }

    $events = new WP_Query($query_args);
    return display_events($events->posts);
}
add_shortcode('events_by_location', 'events_by_location_shortcode');

function get_google_client()
{
    $api_key = get_option('events_plugin_google_api_key');
    $client = new Google_Client();
    $client->setDeveloperKey($api_key);
    return $client;
}

function get_coordinates($address)
{
    $api_key = get_option('events_plugin_google_api_key');
    $address = urlencode($address);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$api_key}";

    $response = wp_remote_get($url);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if (!empty($data->results)) {
        $location = $data->results[0]->geometry->location;
        return [$location->lat, $location->lng];
    }

    return null;
}

function calculate_distance($coords1, $coords2)
{
    $api_key = get_option('events_plugin_google_api_key');
    $origins = implode(',', $coords1);
    $destinations = implode(',', $coords2);
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$origins}&destinations={$destinations}&key={$api_key}";

    $response = wp_remote_get($url);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if (!empty($data->rows[0]->elements[0]->distance->value)) {
        return $data->rows[0]->elements[0]->distance->value / 1609.34; // Convert meters to miles
    }

    return null;
}

function get_distance($location, $city)
{
    $coords1 = get_coordinates($location);
    $coords2 = get_coordinates($city);

    if ($coords1 && $coords2) {
        return calculate_distance($coords1, $coords2);
    }

    return null;
}

function display_events($events)
{
    if (empty($events)) {
        return '<p>No events found for this location.</p>';
    }

    $output = '<ul class="events-by-location">';
    foreach ($events as $event) {
        $location = get_post_meta($event->ID, '_event_location', true);
        $start_date = get_post_meta($event->ID, '_event_start_date', true);
        $output .= '<li>';
        $output .= '<a href="' . get_permalink($event->ID) . '">' . get_the_title($event->ID) . '</a>';
        $output .= ' - ' . esc_html($location) . ' - ' . esc_html($start_date);
        $output .= '</li>';
    }
    $output .= '</ul>';
    return $output;
}
