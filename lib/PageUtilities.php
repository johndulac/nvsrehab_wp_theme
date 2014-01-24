<?php

namespace nvsr;

class PageUtilities {

    private static $nav_sub_pages_div_class = 'sub_pages';
    private static $level_class_prefix = 'level_';

    /**
     * Prints the navigation bar.
     */
    public static function display_nav_bar() {
        echo '<div class="line"></div>';
        //home and news links
        //self::add_extra_nav_link ('Home', get_site_url());
        //the pages
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'menu_order',
            'parent' => 0,
            'hierarchical' => 0
        );
        $pages = \get_pages($args);
        $page_for_posts = get_option('page_for_posts');
        $level = self::$level_class_prefix . '0';
        if (false !== $pages) {
            foreach ($pages as $page) {
                $permalink = get_permalink($page->ID);
                $title = htmlspecialchars($page->post_title);
                $class = $level;
                if (is_page($page->ID) || (is_home() && $page->ID == $page_for_posts)) {
                    $class .= ' current_page';
                }
                echo '<div class="' . $class . '"><div class="membrane">';
                echo '<a href="' . $permalink . '">' . $title . '</a>';
                echo '<div class="' . self::$nav_sub_pages_div_class . '">';
                self::display_nav_bar_subpages($page->ID, $permalink);
                echo '</div></div></div>'; //subpages, membrane, level_0
            }
        }
    }

    /**
     * Helper function for get_nav_bar(). Recursively prints the sub-page
     * links.
     * @param int $parent ID of the parent
     * @param string $permalink permalink of the top-level page, minus trailing '/'
     * @param int $indent
     */
    private static function display_nav_bar_subpages($parent_id, $permalink, $level = 1) {
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'menu_order',
            'parent' => $parent_id,
            'hierarchical' => 0
        );
        $pages = \get_pages($args);
        if (false !== $pages) {
            foreach ($pages as $page) {
                $sublink = $permalink . '#' . $page->post_name;
                $title = htmlspecialchars($page->post_title);
                $class = self::get_level_class($level);
                echo '<a href="' . $sublink . '" class="' . $class . '">'
                . $title . '</a>';
                self::display_nav_bar_subpages($page->ID, $permalink, $level + 1);
            }
        }
    }

    private static function get_level_class($level) {
        $class = array();
        while ($level > 0) {
            $class[] = self::$level_class_prefix . $level;
            $level--;
        }
        return implode(' ', array_reverse($class));
    }

    public static function display_page() {
        global $post;
        if (have_posts()) {
            the_post();
            $level = self::$level_class_prefix . '0';
            echo '<div class="' . implode(' ', get_post_class()) . '">';
            echo '<div class="title ' . $level . '">' . get_the_title() . '</div>';
            $content = apply_filters('the_content', get_the_content());
            echo '<div class="content">' . $content . '</div>';
            //get the subpages
            $post_id = $post->ID;
            self::display_subpages($post_id);
            echo '</div>';
        }
        rewind_posts();
    }

    private static function display_subpages($parent_id, $level = 1) {
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'menu_order',
            'parent' => $parent_id,
            'hierarchical' => 0
        );
        $pages = \get_pages($args);
        if (false !== $pages) {
            foreach ($pages as $page) {
                $class = self::get_level_class($level);
                $title = get_the_title($page->ID);
                $content = apply_filters('the_content', $page->post_content);
                echo '<div id="' . $page->post_name . '" class="title ' . $class . '">'
                . $title . '</div>';
                echo '<div class="content">' . $content . '</div>';
                self::display_subpages($page->ID, $level + 1);
            }
        }
    }

    public static function display_page_nav() {
        global $post;
        if (have_posts()) {
            the_post();
            $page_id = $post->ID;
            self::display_page_nav_subpages($page_id);
        }
        rewind_posts();
    }

    public static function display_page_nav_subpages($parent_id, $level = 1) {
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'menu_order',
            'parent' => $parent_id,
            'hierarchical' => 0
        );
        $pages = \get_pages($args);
        if (false !== $pages) {
            foreach ($pages as $page) {
                $sublink = '#' . $page->post_name;
                $title = htmlspecialchars($page->post_title);
                $class = self::get_level_class($level);
                echo '<a href="' . $sublink . '" class="' . $class . '">'
                . $title . '</a>';
                self::display_page_nav_subpages($page->ID, $level + 1);
            }
        }
    }

}

?>
