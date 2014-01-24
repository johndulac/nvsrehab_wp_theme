<?php

namespace nvsr;

class Accreditation {

    private static $post_type = 'nvsr_accreditation';
    private static $heading = 'Accreditation';
    private static $label = 'Accreditation Images';
    private static $singular_label = 'Accreditation Image';
    private static $args = array(
        'description' => 'Entries appearing in the area for accreditation images.',
        'menu_position' => 45
    );
    private static $image_attrs = array('class' => 'nvsr_accreditation_image');

    /**
     * Register the post type
     */
    public static function init() {
        Decorations::register_decoration_post_type(self::$post_type
                , self::$label, self::$singular_label, self::$args, true);
    }

    public static function display_accreditations() {
        $html = '';
        $query = array(
            'post_type' => self::$post_type,
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );
        $accreditations = new \WP_Query($query);
        while ($accreditations->have_posts()) {
            $accreditations->the_post();
            if (has_post_thumbnail()) {
                $image_id = get_post_thumbnail_id();
                $src = wp_get_attachment_image_src($image_id, 'full');
                $image = '<img src="'.$src[0].'"/>';
                $start = '<div class="'.self::$post_type.'">';
                $end = '</div>';
                $html .= $start.$image.$end;
            }
        }
        wp_reset_postdata();
        if (!empty($html)){
        echo '<div class="nvsr_accreditations heading">'.self::$heading.'</div>';
        }
        echo $html;
    }

}

\add_action('init', '\\nvsr\\Accreditation::init');
?>
