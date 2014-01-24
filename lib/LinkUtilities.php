<?php

namespace nvsr;

class LinkUtilities {

    private static $header = "Useful Links";
    private static $args = array(
        'echo' => 0,
        'title_before' => '<div class="title">',
        'title_after' => '</div>',
        'class' => 'linkcat',
        'category_before' => '<div class="%class">',
        'category_after' => '</div>'
    );

    public static function display_links() {
        $bookmarks = \wp_list_bookmarks(self::$args);
        if (!empty($bookmarks)) {
            echo '<div class="nvsr_links heading">' . self::$header . '</div>';
            echo '<div class="nvsr_links">'.$bookmarks.'</div>';
        }
    }

}

?>
