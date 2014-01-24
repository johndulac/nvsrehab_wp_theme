<?php

namespace nvsr;

class TriPanel {

    private static $post_type = 'nvsr_tri_panel';
    private static $label = 'Tri-panels';
    private static $singular_label = 'Tri-panel';
    private static $args = array(
        'description' => 'Tri-panel content.',
        'menu_position' => 30
    );
    //storage
    private static $tri_panels;
    private static $panels_prepared = false;
    //panel html classes
    private static $container_class = 'container';
    private static $frame_class = 'frame';
    private static $display_class = 'display';
    private static $title_class = 'title';
    private static $content_class = 'content';
    private static $image_class = 'image';

    /**
     * Register the post type
     */
    public static function init() {
        Decorations::register_decoration_post_type(self::$post_type
                , self::$label, self::$singular_label, self::$args, true, true);
    }

    public static function display_tri_panel($index) {
        $return = '';
        if (!self::$panels_prepared) {
            self::prepare_tri_panels();
        }
        if (isset(self::$tri_panels[$index])) {
            $return = self::$tri_panels[$index];
        }
        return $return;
    }

    private static function prepare_tri_panels() {
        self::$tri_panels = array();
        //pull panels out of db
        $query = array(
            'post_type' => self::$post_type,
            'posts_per_page' => 3,
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );
        $panels = new \WP_Query($query);
        //collect arrays with post info
        //img_info, title, content, link
        $trio = array();
        while ($panels->have_posts()) {
            $panel = array();
            $panels->the_post();
            $post_id = get_the_ID();
            //link
            $links_to = get_post_meta($post_id, Decorations::$links_to_key, true);
            $href = '';
            if (!empty($links_to)) {
                $href = get_permalink($links_to);
            }
            $panel['link'] = $href;
            //title
            $panel['title'] = get_the_title();
            //featured image
            $panel['image_info'] = false;
            if (has_post_thumbnail()) {
                $featured_id = get_post_thumbnail_id($post_id);
                $panel['image_info'] = wp_get_attachment_image_src($featured_id, 'full');
            }
            //content
            $panel['content'] = get_the_content();
            $trio[] = $panel;
        }
        wp_reset_postdata();
        //find longest title and content
        //get h/w ratio for 1st image
        $longest_title = '';
        $longest_content = '';
        $frame_img_h_over_w = 1;
        $frame_img_src = '';
        $has_frame = false;
        foreach ($trio as $single) {
            if (mb_strlen($single['title']) > mb_strlen($longest_title)) {
                $longest_title = $single['title'];
            }
            if (mb_strlen($single['content']) > mb_strlen($longest_content)) {
                $longest_content = $single['content'];
            }
            if (false !== $single['image_info'] && !$has_frame) {
                $height = $single['image_info'][2];
                $width = $single['image_info'][1];
                if ($height > 0 && $width > 0) {
                    $frame_img_h_over_w = $height / $width;
                    $frame_img_src = $single['image_info'][0];
                    $has_frame = true;
                }
            }
        }
        //prepare repeated $html
        //container pieces
        $title_container_start = '<div class="'
                . self::$title_class . ' '
                . self::$container_class . '">';
        $content_container_start = '<div class="'
                . self::$content_class . ' '
                . self::$container_class . '">';
        $image_container_start = '<div class="'
                . self::$image_class . ' '
                . self::$container_class . '">';
        $container_end = '</div>';
        //frames create flow in container 
        $title_frame = '<div class="'
                . self::$title_class . ' '
                . self::$frame_class . '">'
                . $longest_title . '</div>';
        $frame_image = '<img class="'
                . self::$image_class . ' '
                . self::$frame_class . '" src="'
                . $frame_img_src . '" />';
        $content_frame = '<div class="'
                . self::$content_class . ' '
                . self::$frame_class . '">'
                . $longest_content . '</div>';
        //compose panels
        foreach ($trio as $single) {
            $link_start = '<a class="' . self::$post_type
                    . '" href="' . $single['link'] . '">';
            $link_end = '</a>';
            //title
            $title = '<div class="'
                    . self::$title_class . ' '
                    . self::$display_class
                    . '">' . $single['title'] . '</div>';
            $image = self::get_panel_image($single['image_info'], $frame_img_h_over_w);
            $content = '<div class="'
                    . self::$content_class . ' '
                    . self::$display_class
                    . '">' . $single['content'] . '</div>';
            $html = $link_start
                    . $title_container_start . $title_frame . $title
                    . $container_end
                    . $image_container_start . $frame_image . $image
                    . $container_end
                    . $content_container_start . $content_frame . $content
                    . $container_end
                    . $link_end;
            self::$tri_panels[] = $html;
        }
        self::$panels_prepared = true;
    }

    private static function get_panel_image($img_info, $frame_img_h_over_w) {
        $img = '';
        //make sure image exists and no opportunity for  division by zero problems
        if (false != $img_info && $img_info[1] > 0 && $img_info[2] > 0) {
            $img_h_over_w = $img_info[2] / $img_info[1];
            $css = array();
            //image and frame same size
            if ($img_h_over_w == $frame_img_h_over_w) {
                $css['top'] = '0';
                $css['left'] = '0';
                $css['width'] = '100%';
                $css['height'] = '100%';
            }
            //image proportionately taller
            else if ($img_h_over_w > $frame_img_h_over_w) {
                $css['width'] = '100%';
                $css['left'] = '0';
                $overlap = ((($img_h_over_w / $frame_img_h_over_w) - 1) / 2) * 100;
                $css['top'] = '-' . $overlap . '%';
            }
            //frame proportionately taller
            else if ($frame_img_h_over_w > $img_h_over_w) {
                $css['height'] = '100%';
                $css['top'] = '0';
                $overlap = ((($frame_img_h_over_w / $img_h_over_w) - 1) / 2) * 100;
                $css['right'] = '-' . $overlap . '%';
            }
            //put together the pieces to make img
            $style = '';
            foreach ($css as $key => $val) {
                $style .= $key . ':' . $val . ';';
            }
            $class = self::$image_class . ' ' . self::$display_class;
            $img = '<img class="' . $class . '" style="'
                    . $style . '" src="' . $img_info[0] . '"/>';
        }
        return $img;
    }

}

add_action('init', '\\nvsr\\TriPanel::init');
?>
