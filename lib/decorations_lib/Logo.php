<?php

namespace nvsr;

class Logo {

    private static $post_type = 'nvsr_logo';
    private static $label = 'Logos';
    private static $singular_label = 'Logo';
    private static $args = array(
        'description' => 'Logo image with optional title and optional text.',
        'menu_position' => 40
    );
    private static $image_class = 'nvsr_logo_image';

    /**
     * Register the post type
     */
    public static function init() {
        Decorations::register_decoration_post_type(self::$post_type
                , self::$label, self::$singular_label, self::$args, true);
    }

    /**
     * Gets html for the logo. Looks for the Logo post with lowest order
     * or else most recent Logo post. Uses featured image from that post,
     * if there is one.
     * If a title is present with the post, that is used as the title
     * in the logo area, otherwise site title is used.
     * If content is present in the post, that is used as the description
     * that goes with the title, otherwise site description is used.
     * @return string
     */
    public static function display_logo() {
        //set up some vars for holding the parts of the output string
        $start = '<a href="'.site_url().'" class="' . self::$post_type . '">';
        $image = '';
        $title_start = '<div class="title">';
        $title = '';
        $title_end = '</div>';
        $content_start = '<div class="content">';
        $content = '';
        $content_end = '</div>';
        $clear_both = '<div style="clear:both;"></div>';
        $end = '</a>';
        //gather information from logo post
        $query = array(
            'post_type' => self::$post_type,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'posts_per_page' => 1
        );
        $logo = new \WP_Query($query);
        if ($logo->have_posts()) {
            $logo->the_post();
            $start = '<a href="'.site_url().'" class="' . implode(' ', get_post_class()) . '">';
            if (has_post_thumbnail()) {
                $image_id = get_post_thumbnail_id();
                $image_info = wp_get_attachment_image_src($image_id, 'full');
                $image_url = $image_info[0];
                $image = '<img src="' . $image_url . '" class="' . self::$image_class . '"/>';
            }
            $title = get_the_title();
            $content = apply_filters('the_content', get_the_content());
        }
        wp_reset_postdata();
        //gather title and description from site info if not in logo post
        if (empty($title)) {
            $title = get_bloginfo('name');
        }
        if (empty($content)) {
            $content = get_bloginfo('description');
        }
        $html = $start  . $image  . $title_start
                . $title . $title_end . $content_start . $content
                . $content_end  . $clear_both  . $end;
        echo $html;
    }

}

\add_action('init', '\\nvsr\\Logo::init');
?>
