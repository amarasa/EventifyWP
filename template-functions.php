<?php
function render_event_list($query)
{
    if ($query->have_posts()) :
        $current_year_month = '';

        echo '<div class="container events-rollup">';
        echo '<div class="event-list">';

        while ($query->have_posts()) : $query->the_post();
            $recurrence_dates = get_post_meta(get_the_ID(), '_event_recurrence_dates', true);
            $all_dates = $recurrence_dates ? $recurrence_dates : [get_post_meta(get_the_ID(), '_event_start_date', true)];
            $is_featured = get_post_meta(get_the_ID(), '_featured_event', true);

            foreach ($all_dates as $date) :
                $start_date = $date;
                $start_time = get_post_meta(get_the_ID(), '_event_start_time', true);
                $end_date = get_post_meta(get_the_ID(), '_event_end_date', true);
                $end_time = get_post_meta(get_the_ID(), '_event_end_time', true);
                $event_year_month = date('F Y', strtotime($start_date));

                if ($current_year_month != $event_year_month) {
                    if ($current_year_month != '') {
                        echo '</div>'; // close previous month group
                    }
                    $current_year_month = $event_year_month;
                    echo '<div class="event-month-group">';
                    echo '<h2 class="event-month-title">' . $current_year_month . '</h2>';
                }
?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('event-archive-item' . ($is_featured ? ' featured-event' : '')); ?>>
                    <div class="event-date">
                        <div class="event-day"><?php echo date('D', strtotime($start_date)); ?></div>
                        <div class="event-date-number"><?php echo date('j', strtotime($start_date)); ?></div>
                    </div>
                    <div class="event-content">
                        <div class="entry-meta">
                            <p class="event-date-time"><?php echo date('F j, Y', strtotime($start_date)); ?> @ <?php echo date('g:i A', strtotime($start_time)); ?> - <?php echo date('g:i A', strtotime($end_time)); ?></p>
                            <p class="event-location">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                                    <path d="M384 476.1L192 421.2V35.9L384 90.8V476.1zm32-1.2V88.4L543.1 37.5c15.8-6.3 32.9 5.3 32.9 22.3V394.6c0 9.8-6 18.6-15.1 22.3L416 474.8zM15.1 95.1L160 37.2V423.6L32.9 474.5C17.1 480.8 0 469.2 0 452.2V117.4c0-9.8 6-18.6 15.1-22.3z" />
                                </svg>
                                <?php echo esc_html(get_post_meta(get_the_ID(), '_event_location', true)); ?>
                            </p>
                        </div>
                        <header class="entry-header">
                            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        </header>
                        <div class="entry-content">
                            <?php the_excerpt(); ?>
                            <p class="see-event"><a href="<?php the_permalink(); ?>">See Event</a></p>
                        </div>
                    </div>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="event-thumbnail">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php
        endwhile;

        echo '</div>'; // close event list
        echo '</div>'; // close container

    else :
        ?>
        <article id="post-0" class="post no-results not-found">
            <header class="entry-header">
                <h1 class="entry-title"><?php _e('No Events Found', 'basic-events'); ?></h1>
            </header>
            <div class="entry-content">
                <p><?php _e('Sorry, no events matched your criteria.', 'basic-events'); ?></p>
            </div>
        </article>
<?php
    endif;
    wp_reset_postdata();
}
?>