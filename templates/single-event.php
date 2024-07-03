<?php

get_header();

while (have_posts()) : the_post();
    echo render_single_event_details(get_the_ID());
endwhile;

get_footer();
