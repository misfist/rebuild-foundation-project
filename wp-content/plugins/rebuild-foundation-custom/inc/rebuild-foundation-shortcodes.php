<?php 

/**
 * Rebuild Foundation Custom Shortcodes
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom
 *
 */

// Enable shortcodes in widget
add_filter( 'widget_text', 'do_shortcode' );

/**
 * Current year shortcode
 */

if(! function_exists( 'rebuild_current_year' ) ) {

  function rebuild_current_year() {

    return date( 'Y' );

  }

  add_shortcode( 'year', 'rebuild_current_year' );

}

/**
 * Site name shortcode
 */

if(! function_exists( 'rebuild_site_name' ) ) {

  function rebuild_site_name() {

    return get_bloginfo( 'name' );

  }

  add_shortcode( 'sitename', 'rebuild_site_name' );

}

/**
 * Site link shortcode
 */

if(! function_exists( 'rebuild_site_link' ) ) {

  function rebuild_site_link() {

    return '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . get_bloginfo( 'name' ) . '</a>';

  }

  add_shortcode( 'sitelink', 'rebuild_site_link' );

}






?>