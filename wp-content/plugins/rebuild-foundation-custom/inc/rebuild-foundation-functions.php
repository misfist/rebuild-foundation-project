<?php 

/**
 * Rebuild Foundation Custom Functions
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom
 *
 */


/* 
 * Add markdown support for custom post types
 */

if(! function_exists( 'rebuild_markdown_support' )  ) {

    function rebuild_markdown_support() {
        add_post_type_support( 'wp-help', 'wpcom-markdown' );
    }

    add_action( 'init', 'rebuild_markdown_support' );

}

/* 
 * Add home page link menu to admin
 */

if(! function_exists( 'rebuild_home_page_admin_menu' )  ) {

    function rebuild_home_page_admin_menu() {

      $homepage_id = get_option( 'page_on_front' );
      
      add_menu_page(
        __('Home Page', 'rebuild-custom'),
        __('Home Page', 'rebuild-custom'), //
        'edit_pages',
        'post.php?post=' . $homepage_id . '&action=edit',
        false,
        'dashicons-admin-home',
        4
      );

    }

    add_action( 'admin_menu', 'rebuild_home_page_admin_menu' );

}


?>