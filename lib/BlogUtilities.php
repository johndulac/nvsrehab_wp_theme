<?php
namespace nvsr;

class BlogUtilities {

    public function displayBlogNav(){
        $args = array(
            'posts_per_page' => -1
        );
        $blog_nav = new \WP_Query($args);
        while ($blog_nav->have_posts()){
            $blog_nav->the_post();
            $href = get_permalink();
            $start = '<a href="'.$href.'">';
            $title = get_the_title();
            $date = get_the_date();
            $date = '<span class="date">'.$date.'</span>';
            $end = '</a>';
            echo $start.$title.'<br />'.$date.$end;
        }
        wp_reset_postdata();
    }
    
}

?>
