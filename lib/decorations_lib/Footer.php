<?php

namespace nvsr;

/**
 * A class for handling tasks common to FooterLeft, FooterCenter, 
 * and FooterRight
 */
class Footer {

    /**
     * Retrieves footer posts. To be called from FooterLeft, FooterCenter,
     * and FooterRight classes to ensure uniform look.
     * @param string $post_type the post type to retrieve
     */
    public static function display_footer($post_type) {
        $html = '';
        $query = array(
            'post_type' => $post_type,
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );
        $footer = new \WP_Query($query);
        while ($footer->have_posts()) {
            $footer->the_post();
            $html .= '<div class="' . implode(' ',get_post_class('nvsr_footer')) . '">';
            $html .= '<div class="title">' . get_the_title() . '</div>';
            $content = apply_filters('the_content', get_the_content());
            $html .= '<div class="content">' . $content . '</div>';
            $html .= '</div>';
        }
        wp_reset_postdata();
        echo $html;
    }

}

?>
