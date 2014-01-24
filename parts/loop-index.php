<?php

//display most recent news
while (have_posts()){
    the_post();
    echo '<div class="'.implode(' ', get_post_class()).'">';
    echo '<div class="title">'.get_the_title().'</div>';
    echo '<div class="date">Posted: '.get_the_date().'</div>';
    echo '<div class="content">'.apply_filters('the_content', get_the_content()).'</div>';
    echo '</div>';
}
        
?>
