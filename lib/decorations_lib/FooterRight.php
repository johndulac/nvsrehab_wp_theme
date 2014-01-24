<?php

namespace nvsr;

class FooterRight {

    private static $post_type = 'nvsr_footer_right';
    private static $label = 'Right Footers';
    private static $singular_label = 'Right Footer';
    private static $args = array(
        'description' => 'Entries appearing in the right footer.',
        'menu_position' => 60
    );

    /**
     * Register the post type
     */
    public static function init() {
        Decorations::register_decoration_post_type(self::$post_type
                , self::$label, self::$singular_label, self::$args);
    }

    public static function display_right_footer(){
        Footer::display_footer(self::$post_type);
    }
    
}
\add_action ('init', '\\nvsr\\FooterRight::init');

?>
