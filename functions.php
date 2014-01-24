<?php
require_once 'lib/Decorations.php';
require_once 'lib/PageUtilities.php';
require_once 'lib/NewsUtilities.php';
require_once 'lib/LinkUtilities.php';
require_once 'lib/BlogUtilities.php';


add_theme_support('post-thumbnails');

/**
 * Changes the length of string returned by the_excerpt().
 * @return int the new excerpt length
 */
function nvsr_custom_excerpt_length() {
	return 25;
}
add_filter( 'excerpt_length', 'nvsr_custom_excerpt_length', 999 );

//turn on links
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

// Replaces the excerpt "more" text 
function nvsr_excerpt_more($more) {
       global $post;
	return '<div class="read_more"><a href="'. get_permalink($post->ID) . '">full article</a></div>';
}
add_filter('excerpt_more', 'nvsr_excerpt_more');

?>
