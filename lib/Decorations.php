<?php

namespace nvsr;

class Decorations {

    //admin menu 
    private static $page_title = 'NVSR Decorations';
    private static $menu_title = 'NVSR Decorations';
    private static $capability = 'publish_pages';
    private static $menu_slug = 'nvsr_decorations';
    private static $display_function = '\\nvsr\\Decorations::display_page';
    private static $icon_url = '';
    private static $position = 100;
    //thumbnails in admin menus
    private static $admin_thumb_size = 'nvsr_admin_thumb';
    private static $admin_thumb_width = 75;
    private static $admin_thumb_height = 75;
    //links_to meta box
    private static $links_to_nonce_action = 'decoration_links_to_action';
    private static $links_to_nonce_name = 'decoration_links_to_name';
    private static $links_to_meta_box_id = 'nvsr_links_to_meta_box';
    private static $links_to_meta_box_title = 'Links to';
    public static $links_to_key = 'nvsr_decoration_links_to';
    //standard args for custom post types
    private static $standard_args = array(
        'public ' => 'false',
        'show_ui' => true,
        'show_in_admin_bar' => false,
        'rewrite' => false,
        'supports' => array('title', 'editor', 'page-attributes'),
        'register_meta_box_cb' => '\\nvsr\\Decorations::register_meta_box_cb'
    );

    /**
     * Creates the admin menu item.
     */
    public static function init() {
        add_image_size(self::$admin_thumb_size
                , self::$admin_thumb_width
                , self::$admin_thumb_height);
        add_menu_page(self::$page_title, self::$menu_title
                , self::$capability, self::$menu_slug
                , self::$display_function, self::$icon_url, self::$position);
    }

    /**
     * Displays the Decorations page
     */
    public static function display_page() {
        echo 'This is the Decorations page';
    }

    /**
     * A utility function for creating the labels for custom post types.
     * @param string $label
     * @param string $singular_label
     * @return array the labels for a custom post type
     */
    private static function get_labels($label, $singular_label) {
        $label_lower = strtolower($label);
        $labels = array(
            'singular_name' => $singular_label,
            'menu_name' => $label,
            'all_items' => $label,
            'add_new_item' => 'Add New ' . $singular_label,
            'edit_item' => 'Edit ' . $singular_label,
            'new_item' => 'New ' . $singular_label,
            'view_item' => 'View ' . $singular_label,
            'search_items' => 'Search ' . $label,
            'not_found' => 'No ' . $label_lower . ' found',
            'not_found_in_trash' => 'No ' . $label_lower . ' found in trash'
        );
        return $labels;
    }

    /**
     * The callback function registered with nvsr decoration post types.
     * Removes the 'pasteword' button from mce editor and
     * removes the 'add media' button from the editor page.
     */
    public static function register_meta_box_cb() {
        //remove pasteword from mce toolbar
        add_filter('mce_buttons_2', function($tools) {
                    $index = array_search('pasteword', $tools);
                    if (false !== $index) {
                        array_splice($tools, $index, 1);
                    }
                    return $tools;
                });
        //remove add media button
        remove_action('media_buttons', 'media_buttons');
    }

    public static function register_links_to_meta_box() {
        add_meta_box(self::$links_to_meta_box_id, self::$links_to_meta_box_title
                , '\\nvsr\\Decorations::add_links_to_meta_box_content'
                , null, 'side');
    }

    /**
     * Creates html for links_to meta box.
     * @param object $post post object supplied by WP
     */
    public static function add_links_to_meta_box_content($post) {
        //get the current meta value
        $current_value = get_post_meta($post->ID, self::$links_to_key, true);
        //fetch all the top level pages
        $args = array(
            'parent' => 0
        );
        $pages = get_pages($args);
        //add the nonce field
        wp_nonce_field(self::$links_to_nonce_action, self::$links_to_nonce_name);
        //build the select
        echo '<select name="' . self::$links_to_key . '">';
        echo '<option value="">(No link)</option>';
        $has_selected = (empty($current_value)) ? true : false;
        foreach ($pages as $page) {
            $title = $page->post_title;
            $id = $page->ID;
            $selected = '';
            if (!$has_selected && 0 == strcmp($id, $current_value)) {
                $has_selected = true;
                $selected = ' selected="selected"';
            }
            echo '<option value="' . $id . '"'
            . $selected . '>' . $title . '</option>';
        }
        echo '</select>';
    }

    /**
     * Saves links_to meta data for post
     * @param int $post_id passed in by WP
     * @return none return only used to exit function early
     */
    public static function save_links_to_meta_data($post_id) {
        $nonce = (isset($_POST[self::$links_to_nonce_name])) ? $_POST[self::$links_to_nonce_name] : '';
        if (!wp_verify_nonce($nonce, self::$links_to_nonce_action)) {
            return;// $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;// $post_id;
        }
        if (!current_user_can(self::$capability)) {
            return;// $post_id;
        }
        if (isset($_POST[self::$links_to_key]) && !empty($_POST[self::$links_to_key])) {
            $value = $_POST[self::$links_to_key];
            if (null != get_page($value)) {
                update_post_meta($post_id, self::$links_to_key, $value);
            }
        }
    }

    /**
     * A utility function used by all nvsr decoration post types.
     * Registers the post type with arguments common to all post types.
     * Sets up filter and hook needed for 'order' column in admin page
     * for post type.
     * @param string $post_type slug for post type
     * @param string $label label for post type (plural)
     * @param string $singular_label label for post type (singular)
     * @param array $args arguments passed in to register_post_type
     * @param bool $thumbnail_support true adds thumbnail support to $args
     * @param bool $links_to_meta_box true if post type has a links_to box
     */
    public static function register_decoration_post_type(
    $post_type, $label, $singular_label, $args
    , $thumbnail_support = false, $links_to_meta_box = false) {
        //prepare args for registration of post type
        $args['label'] = $label;
        $args['labels'] = self::get_labels($label, $singular_label);
        $args['show_in_menu'] = self::$menu_slug;
        $args = array_merge(self::$standard_args, $args);
        if ($thumbnail_support) {
            $args['supports'][] = 'thumbnail';
        }
        //register post type
        \register_post_type($post_type, $args);
        //add actions and filters for order column in admin list
        add_filter('manage_edit-' . $post_type . '_columns'
                , '\\nvsr\\Decorations::add_admin_order_column');
        add_action('manage_' . $post_type . '_posts_custom_column'
                , '\\nvsr\\Decorations::add_admin_column_value');
        add_filter('manage_edit-' . $post_type . '_sortable_columns'
                , '\\nvsr\\Decorations::add_admin_sortable_order_column');
        //column for featured image in admin list
        if ($thumbnail_support) {
            add_filter('manage_edit-' . $post_type . '_columns'
                    , '\\nvsr\\Decorations::add_admin_featured_column');
        }
        //links_to meta box
        if ($links_to_meta_box) {
            add_action('add_meta_boxes_' . $post_type
                    , '\\nvsr\\Decorations::register_links_to_meta_box');
            add_action('save_post'
                    , '\\nvsr\\Decorations::save_links_to_meta_data');
        }
    }

    /**
     * Adds a column for the order (menu_order) to the admin page
     * for the post type. Filter hook.
     */
    public static function add_admin_order_column($columns) {
        $columns['order'] = 'Order';
        return $columns;
    }

    /**
     * Adds a column for the featured image to the admin page
     * for the post type. Filter hook.
     */
    public static function add_admin_featured_column($columns) {
        $columns['featured'] = 'Featured';
        return $columns;
    }

    /**
     * Adds the value for the order columnin the admin menu. Action hook.
     * @global type $post
     * @param string $column the key for the column
     */
    public static function add_admin_column_value($column, $post_id) {
        global $post;
        switch ($column) {
            case ('order'):
                echo $post->menu_order;
                break;
            case('featured'):
                if (has_post_thumbnail($post_id)) {
                    echo \get_the_post_thumbnail($post_id, self::$admin_thumb_size);
                }
                break;
            default:
                break;
        }
    }

    /**
     * Adds order and author as sortable columns for admin list
     * @param array $columns
     * @return array as required by filter
     */
    public static function add_admin_sortable_order_column($columns) {
        $columns['order'] = 'menu_order';
        $columns['author'] = 'author';
        return $columns;
    }

}

//init
add_action('admin_menu', '\\nvsr\\Decorations::init');

//classes
require_once 'decorations_lib/Logo.php';
require_once 'decorations_lib/Slide.php';
require_once 'decorations_lib/TriPanel.php';
require_once 'decorations_lib/Footer.php';
require_once 'decorations_lib/FooterLeft.php';
require_once 'decorations_lib/FooterCenter.php';
require_once 'decorations_lib/FooterRight.php';
require_once 'decorations_lib/Accreditation.php';
require_once 'decorations_lib/DonateButton.php';
?>