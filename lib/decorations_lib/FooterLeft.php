<?php

namespace nvsr;

class FooterLeft {

    private static $post_type = 'nvsr_footer_left';
    private static $label = 'Left Footers';
    private static $singular_label = 'Left Footer';
    private static $args = array(
        'description' => 'Entries appearing in the left footer.',
        'menu_position' => 50
    );

    /**
     * Register the post type
     */
    public static function init() {
        Decorations::register_decoration_post_type(self::$post_type
                , self::$label, self::$singular_label, self::$args);
    }

    public static function display_left_footer(){
        Footer::display_footer(self::$post_type);
    }
    
}
\add_action ('init', '\\nvsr\\FooterLeft::init');

?>
