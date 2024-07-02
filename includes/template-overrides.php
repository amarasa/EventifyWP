<?php

// Override single event template
add_filter('template_include', 'override_event_templates');

function override_event_templates($template)
{
    if (is_singular('event')) {
        $plugin_template = plugin_dir_path(__FILE__) . '../templates/single-event.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    if (is_post_type_archive('event')) {
        $archive_template = plugin_dir_path(__FILE__) . '../templates/archive-event.php';
        if (file_exists($archive_template)) {
            return $archive_template;
        }
    }

    return $template;
}
