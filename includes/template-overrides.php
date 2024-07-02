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

    if (is_tax('organizer')) {
        $organizer_template = plugin_dir_path(__FILE__) . '../templates/taxonomy-organizer.php';
        if (file_exists($organizer_template)) {
            return $organizer_template;
        }
    }

    if (is_tax('event_category')) {
        $event_category_template = plugin_dir_path(__FILE__) . '../templates/taxonomy-event_category.php';
        if (file_exists($event_category_template)) {
            return $event_category_template;
        }
    }

    if (is_tag()) {
        $tag_template = plugin_dir_path(__FILE__) . '../templates/taxonomy-post_tag.php';
        if (file_exists($tag_template)) {
            return $tag_template;
        }
    }

    return $template;
}
