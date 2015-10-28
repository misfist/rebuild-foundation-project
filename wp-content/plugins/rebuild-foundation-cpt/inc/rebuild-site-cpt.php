<?php

/**
 * Rebuild Foundation Site Custom Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */


/*
 * Site Custom Post Type
 *
 */

if ( ! function_exists( 'rebuild_site_cpt' ) ) {

    // Register Custom Post Type
    function rebuild_site_cpt() {

        $labels = array(
            'name'                => _x( 'Sites', 'Post Type General Name', 'rebuild_cpt' ),
            'singular_name'       => _x( 'Site', 'Post Type Singular Name', 'rebuild_cpt' ),
            'menu_name'           => __( 'Sites', 'rebuild_cpt' ),
            'name_admin_bar'      => __( 'Site', 'rebuild_cpt' ),
            'parent_item_colon'   => __( 'Parent Site:', 'rebuild_cpt' ),
            'all_items'           => __( 'All Sites', 'rebuild_cpt' ),
            'add_new_item'        => __( 'Add New Site', 'rebuild_cpt' ),
            'add_new'             => __( 'Add New Site', 'rebuild_cpt' ),
            'new_item'            => __( 'New Site', 'rebuild_cpt' ),
            'edit_item'           => __( 'Edit Site', 'rebuild_cpt' ),
            'update_item'         => __( 'Update Site', 'rebuild_cpt' ),
            'view_item'           => __( 'View Site', 'rebuild_cpt' ),
            'search_items'        => __( 'Search Item', 'rebuild_cpt' ),
            'not_found'           => __( 'Not found', 'rebuild_cpt' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                => 'site',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
        );
        $args = array(
            'label'               => __( 'Site', 'rebuild_cpt' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', ),
            'taxonomies'          => array( 'site_category' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-building',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => 'sites',
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'query_var'           => 'site',
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
        );
        register_post_type( 'site', $args );

    }
    add_action( 'init', 'rebuild_site_cpt', 0 );

}


/*
 * Site Category
 *
 */

if ( ! function_exists( 'rebuild_site_category' ) ) {

    // Register Custom Taxonomy
    function rebuild_site_category() {

        $labels = array(
            'name'                       => _x( 'Site Categories', 'Taxonomy General Name', 'rebuild_cpt' ),
            'singular_name'              => _x( 'Site Category', 'Taxonomy Singular Name', 'rebuild_cpt' ),
            'menu_name'                  => __( 'Site Categories', 'rebuild_cpt' ),
            'all_items'                  => __( 'All Site Categories', 'rebuild_cpt' ),
            'parent_item'                => __( 'Parent Site Category', 'rebuild_cpt' ),
            'parent_item_colon'          => __( 'Parent Site Category:', 'rebuild_cpt' ),
            'new_item_name'              => __( 'New Site Category Name', 'rebuild_cpt' ),
            'add_new_item'               => __( 'Add New Site Category', 'rebuild_cpt' ),
            'edit_item'                  => __( 'Edit Site Category', 'rebuild_cpt' ),
            'update_item'                => __( 'Update Site Category', 'rebuild_cpt' ),
            'view_item'                  => __( 'View Site Category', 'rebuild_cpt' ),
            'separate_items_with_commas' => __( 'Separate site categories with commas', 'rebuild_cpt' ),
            'add_or_remove_items'        => __( 'Add or remove site categories', 'rebuild_cpt' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'rebuild_cpt' ),
            'popular_items'              => __( 'Popular Site Categories', 'rebuild_cpt' ),
            'search_items'               => __( 'Search Site Categories', 'rebuild_cpt' ),
            'not_found'                  => __( 'Not Found', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                       => 'site-category',
            'with_front'                 => true,
            'hierarchical'               => true,
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'query_var'                  => 'site_category',
            'rewrite'                    => $rewrite,
        );
        register_taxonomy( 'site_category', array( 
            'post',
            'site', 
            'location', 
            'event', 
            'exhibition' ), $args );

    }
    add_action( 'init', 'rebuild_site_category', 0 );

}

/**
 * Hide site_category metabox from content edit screen
 * We're using ACF to select site_category so we can limit to 1 selection
 */

if( ( ! function_exists( 'rebuild_foundation_remove_custom_taxonomy' ) ) && is_admin() ) {

  function rebuild_foundation_remove_custom_taxonomy() {

    remove_meta_box( 'site_categorydiv', 'post', 'side' );
    remove_meta_box( 'site_categorydiv', 'site', 'side' );
    remove_meta_box( 'site_categorydiv', 'event', 'side' );
    remove_meta_box( 'site_categorydiv', 'exhibition', 'side' );
          
  }

  add_action( 'admin_menu', 'rebuild_foundation_remove_custom_taxonomy' );

}


?>