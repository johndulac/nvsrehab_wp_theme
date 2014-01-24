<?php

namespace nvsr;

class NewsUtilities {

    private static $num_front_page_excerpts = 2;
    private static $front_page_news_header = "Latest News";
    private static $front_page_news_footer = "all news";
    private static $featured_image_class = "featured_image";
    private static $news_class = "nvsr_front_page_news";

    public static function display_front_page_news() {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => self::$num_front_page_excerpts,
            'orderby' => 'post_date',
            'order' => 'DESC'
        );
        $news_posts = new \WP_Query($args);
        $first = true;
        while ($news_posts->have_posts()) {
            if ($first){
                echo '<div class="nvsr_front_page_news heading">'
                .self::$front_page_news_header.'</div>';
                $first = false;
            }
            $news_posts->the_post();
            $img = '';
            if (has_post_thumbnail()) {
                $featured_id = get_post_thumbnail_id();
                $img_info = wp_get_attachment_image_src($featured_id, 'full');
                if (false !== $img_info) {
                    $src = $img_info[0];
                    $img_class = self::$featured_image_class;
                    $img = '<img class="' . $img_class . '" src="' . $src . '"/>';
                }
            }
            $start = '<div class="' . implode(' ' ,get_post_class(self::$news_class)) . '">';
            $title = '<div class="title">' . get_the_title() . '</div>';
            $content = '<div class="content">' . get_the_excerpt() . '</div>';
            $end = '<div style="clear:both;"></div></div>';
            echo $start . $img . $title . $content . $end;
        }
        wp_reset_postdata();
    }
}

?>
