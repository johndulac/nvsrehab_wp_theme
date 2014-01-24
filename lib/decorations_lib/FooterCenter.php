<?php

namespace nvsr;

class FooterCenter {

    private static $post_type = 'nvsr_footer_center';
    private static $label = 'Center Footers';
    private static $singular_label = 'Center Footer';
    private static $args = array(
        'description' => 'Entries appearing in the center footer.',
        'menu_position' => 55
    );

    /**
     * Register the post type
     */
    public static function init() {
        Decorations::register_decoration_post_type(self::$post_type
                , self::$label, self::$singular_label, self::$args);
    }

    public static function display_center_footer(){
        Footer::display_footer(self::$post_type);
    }
    
}
\add_action ('init', '\\nvsr\\FooterCenter::init');

?>
