<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php
    require_once plugin_dir_path(__FILE__) . '../template-functions.php';

    $current_year_month = '';
    $term = get_queried_object();
    $today = date('Y-m-d');

    $args = array(
        'post_type' => 'event',
        'posts_per_page' => -1,
        'meta_key' => '_event_start_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'event_category',
                'field'    => 'slug',
                'terms'    => $term->slug,
            ),
        ),
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_event_start_date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'DATE'
            ),
            array(
                'key' => '_event_recurrence_dates',
                'value' => $today,
                'compare' => 'LIKE'
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) : ?>

        <header class="page-header">
            <h1 class="page-title"><?php echo sprintf(__('Events in category %s', 'basic-events'), $term->name); ?></h1>
        </header>

        <?php render_event_list($query); ?>

    <?php else : ?>

        <article id="post-0" class="post no-results not-found">
            <header class="entry-header">
                <h1 class="entry-title"><?php _e('No Events Found', 'basic-events'); ?></h1>
            </header>
            <div class="entry-content">
                <p><?php _e('Sorry, no events matched your criteria.', 'basic-events'); ?></p>
            </div>
        </article>
    <?php endif; ?>

    <?php wp_footer(); ?>
</body>

</html>