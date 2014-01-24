<?php

namespace nvsr;

class Slide {

    private static $post_type = 'nvsr_slide';
    private static $label = 'Slides';
    private static $singular_label = 'Slide';
    private static $args = array(
        'description' => 'Slide content.',
        'menu_position' => 25
    );
    private static $image_attrs = array('class' => 'nvsr_slide_image');
    private static $max_slides = 6;
    //slide markup stuff
    private static $transition_delay_seconds = 5; //seconds
    private static $transition_delay_input_class = 'transition_delay';
    private static $transition_duration_seconds = .5; //seconds
    private static $transition_duration_input_class = 'transition_duration';
    private static $screen_frame_class = 'screen_frame';
    private static $slide_image_class = 'slide_image';
    //slideshow js script info
    private static $slideshow_file = 'js/nvsr_slideshow.js';
    private static $slideshow_deps = array('jquery');
    private static $slideshow_handle = 'nvsr_slideshow';

    /**
     * Register the post type
     */
    public static function init() {
        Decorations::register_decoration_post_type(self::$post_type
                , self::$label, self::$singular_label, self::$args, true, true);
        add_action('wp_enqueue_scripts', '\\nvsr\\Slide::enqueue_nvsr_slideshow');
    }

    /**
     * Enqueues the javascript to run the slideshow
     */
    public static function enqueue_nvsr_slideshow() {
        if (is_front_page()) {
            $src = get_template_directory_uri() . '/' . self::$slideshow_file;
            $deps = self::$slideshow_deps;
            $handle = self::$slideshow_handle;
            wp_enqueue_script($handle, $src, $deps);
        }
    }

    /**
     * Sets the maximum number of slides.
     * @param int|string $number maximum number of slides
     */
    public static function set_max_slides($number) {
        if (is_numeric($number)) {
            self::$max_slides = $number;
        }
    }

    /**
     * Creates a slideshow from nvsr_slide posts.
     * @return string html of slideshow
     */
    public static function display_slides() {
        $xx = 1;
        //and slideshow data
        $transition_delay = '<input type="hidden" '
                . 'class="' . self::$transition_delay_input_class . '" '
                . 'name="transition_delay" '
                . 'value="' . self::$transition_delay_seconds . '"/>';
        $transition_duration = '<input type="hidden" '
                . 'class="' . self::$transition_duration_input_class . '" '
                . 'name="transition_duration" '
                . 'value="' . self::$transition_duration_seconds . '" />';
        //get slides from db
        $query = array(
            'post_type' => self::$post_type,
            'posts_per_page' => self::$max_slides,
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );
        $slides = new \WP_Query($query);
        //create markup for slides and screen
        $screen_frame = '';
        $html = '';
        $first = true;
        while ($slides->have_posts()) {
            $slides->the_post();
            //make sure image exists
            if (has_post_thumbnail()) {
                $post_id = get_the_ID();
                $target_id = get_post_meta($post_id, Decorations::$links_to_key, true);
                //make sure slide links to something
                if (!empty($target_id)) {
                    //anchor //first here gives 1st anchor class of .first
                    $anchor = self::get_slide_anchor($target_id, $first);
                    //image
                    $featured_id = get_post_thumbnail_id($post_id);
                    //$first here builds screen size
                    $featured_img = self::get_slide_img($featured_id, $first);
                    //first image is used as screen frame
                    //keeps screen propped up with correct dimensions
                    if ($first) {
                        $image_info = wp_get_attachment_image_src($featured_id, 'full');
                        $src = $image_info[0];
                        $screen_frame = '<img src="' . $src 
                                . '" class="' . self::$screen_frame_class . '"/>';
                        $first = false;
                    }
                    //title and content
                    $title = get_the_title();
                    $content = get_the_content();
                    $text = '';
                    //title/content area only if there is title or content
                    if (!(empty($title) && empty($content))) {
                        $bg_screen = '<div class="text_bg"></div>';
                        if (!empty($title)) {
                            $title = '<div class="title">' . $title . '</div>';
                        }
                        if (!empty($content)) {
                            $content = '<div class="content">' . $content . '</div>';
                        }
                        //put title and content together
                        $text = '<div class="text">' . $bg_screen . $title . $content . '</div>';
                    }
                    //add it all to other slides
                    $html .= $anchor . $featured_img . $text . '</a>';
                }
            }
        }
        wp_reset_postdata();
        $html = $transition_delay . $transition_duration
                . $screen_frame . $html;
        echo $html;
    }

    /**
     * Creates an html img with a style attribute to position it to fit
     * a particular height/width ratio.
     * @staticvar float $screen_h_over_w height/width ratio of image used to fit following images
     * @param int $featured_id ID of image attachment
     * @param bool $first if true, image is used to size images that follow
     * @return string html img tag
     */
    private static function get_slide_img($featured_id, $first) {
        static $screen_h_over_w = 1; //value not expected to be used
        $img = '';
        //grab image from db
        $img_info = wp_get_attachment_image_src($featured_id, 'full');
        //img must be present and height or width cannot be zero or negative
        if (false !== $img_info && $img_info[1] > 0 && $img_info[2] > 0) {
            //first image dimensions used to fit all images
            if ($first) {
                $screen_h_over_w = $img_info[2] / $img_info[1];
            }
            $css = array();
            $img_h_over_w = $img_info[2] / $img_info[1];
            //screen and image same height/width
            if ($img_h_over_w == $screen_h_over_w) {
                $css['top'] = '0';
                $css['left'] = '0';
                $css['width'] = '100%';
                $css['height'] = '100%';
            }
            //image is proportionately taller
            else if ($img_h_over_w > $screen_h_over_w) {
                $css['width'] = '100%';
                $css['left'] = '0';
                $overlap = ((($img_h_over_w / $screen_h_over_w) - 1) / 2) * 100;
                $css['top'] = '-' . $overlap . '%';
            }
            //screen proportionately taller
            else if ($screen_h_over_w > $img_h_over_w) {
                $css['height'] = '100%';
                $css['top'] = '0';
                $overlap = ((($screen_h_over_w / $img_h_over_w) - 1) / 2) * 100;
                $css['right'] = '-' . $overlap . '%';
            }
            //put together the pieces to make img
            $style = '';
            foreach ($css as $key => $val) {
                $style .= $key . ':' . $val . ';';
            }
            $class = self::$slide_image_class;
            $img = '<img class="' . $class . '" style="'
                    . $style . '" src="' . $img_info[0] . '"/>';
        }
        return $img;
    }

    /**
     * Retrieves the URL for given target
     * @param mixed $target_id ID of post that will be the link target
     * @param bool $first true adds class 'first' to position as visible
     * @return string
     */
    private static function get_slide_anchor($target_id, $first) {
        $href = get_permalink($target_id);
        $class = self::$post_type;
        $class .= ($first) ? ' first' : '';
        $anchor = '<a class="' . $class . '" href="' . $href . '">';
        return $anchor;
    }

}

add_action('init', '\\nvsr\\Slide::init');
?>
