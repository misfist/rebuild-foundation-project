<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Starter Theme
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function starter_theme_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'starter_theme_body_classes' );


/**
 * Media - set default image link location to 'None' 
 */

update_option('image_default_link_type','none');


/**
 * Always Show Kitchen Sink in WYSIWYG Editor
 */

function unhide_kitchensink( $args ) {
    $args['wordpress_adv_hidden'] = false;
    return $args;
}

add_filter( 'tiny_mce_before_init', 'unhide_kitchensink' );
