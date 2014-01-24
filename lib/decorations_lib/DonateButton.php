<?php

namespace nvsr;

class DonateButton {

    private static $post_type = 'nvsr_donate_button';
    private static $label = 'Donate Buttons';
    private static $singular_label = 'Donate Button';
    private static $args = array(
        'description' => 'Image used for donate button.',
        'menu_position' => 65,
        'supports' => array('title', 'page-attributes')
    );
    private static $image_attrs = array('class' => 'nvsr_donate_button_image');
    private static $default_donate_button_file = 'TransButton_DonateRounded-2.png';
    /**
     * Register the post type
     */
    public static function init() {
        Decorations::register_decoration_post_type(self::$post_type
                , self::$label, self::$singular_label, self::$args, true);
    }

/**
 * Fetches the url of the first (by order) donate button decoration. If none
 * is found, gives url of default button in theme's images folder
 * @return string url of image
 */
    public static function get_donate_button_src() {
        $image_url = '';
        $query = array(
            'post_type' => self::$post_type,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'posts_per_page' => 1
        );
        $button = new \WP_Query($query);
        if ($button->have_posts()){
            $button->the_post();
            if (has_post_thumbnail()){
                $id = get_post_thumbnail_id();
                $image_info = wp_get_attachment_image_src($id, 'full');
                $image_url = $image_info[0];
            }
        }
        \wp_reset_postdata();
        //if nothing found in post, get default image url
        if (empty($image_url) || false === $image_url){
            $base = get_template_directory_uri();
            $image_url = $base.'/images/'.self::$default_donate_button_file;
        }
        return $image_url;
    }

}
\add_action ('init', '\\nvsr\\DonateButton::init');

?>
