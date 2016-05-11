<?php

/**
 * Rebuild Foundation Residencies Custom Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.1
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

/**
 * Residencies Custom Post Type
 *
 */

if ( ! function_exists( 'rebuild_residency_cpt' ) ) {

    // Register Custom Post Type
    function rebuild_residency_cpt() {

        $labels = array(
            'name'                => _x( 'Residencies', 'Post Type General Name', 'rebuild_cpt' ),
            'singular_name'       => _x( 'Residency', 'Post Type Singular Name', 'rebuild_cpt' ),
            'menu_name'           => __( 'Residencies', 'rebuild_cpt' ),
            'name_admin_bar'      => __( 'Residency', 'rebuild_cpt' ),
            'parent_item_colon'   => __( 'Parent Residency:', 'rebuild_cpt' ),
            'all_items'           => __( 'All Residencies', 'rebuild_cpt' ),
            'add_new_item'        => __( 'Add New Residency', 'rebuild_cpt' ),
            'add_new'             => __( 'Add New Residency', 'rebuild_cpt' ),
            'new_item'            => __( 'New Residency', 'rebuild_cpt' ),
            'edit_item'           => __( 'Edit Residency', 'rebuild_cpt' ),
            'update_item'         => __( 'Update Residency', 'rebuild_cpt' ),
            'view_item'           => __( 'View Residency', 'rebuild_cpt' ),
            'search_items'        => __( 'Search Residency', 'rebuild_cpt' ),
            'not_found'           => __( 'Not found', 'rebuild_cpt' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                => 'residency',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
        );
        $args = array(
            'label'               => __( 'Residency', 'rebuild_cpt' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', ),
            'taxonomies'          => array( 'site_category', 'residency_category' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-welcome-learn-more',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => 'residencies',
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'query_var'           => 'residency',
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
        );
        register_post_type( 'residency', $args );

    }
    add_action( 'init', 'rebuild_residency_cpt', 0 );

}

/**
 * Residency Scope
 * Used to organize into time periods 
 */
if ( ! function_exists( 'rebuild_residency_category' ) ) {

    // Register Custom Taxonomy
    function rebuild_residency_category() {

        $labels = array(
            'name'                       => _x( 'Residency Scopes', 'Taxonomy General Name', 'rebuild_cpt' ),
            'singular_name'              => _x( 'Residency Scope', 'Taxonomy Singular Name', 'rebuild_cpt' ),
            'menu_name'                  => __( 'Residency Scopes', 'rebuild_cpt' ),
            'all_items'                  => __( 'All Residency Scopes', 'rebuild_cpt' ),
            'parent_item'                => __( 'Parent Residency Scope', 'rebuild_cpt' ),
            'parent_item_colon'          => __( 'Parent Residency Scope:', 'rebuild_cpt' ),
            'new_item_name'              => __( 'New Residency Scope Name', 'rebuild_cpt' ),
            'add_new_item'               => __( 'Add New Residency Scope', 'rebuild_cpt' ),
            'edit_item'                  => __( 'Edit Residency Scope', 'rebuild_cpt' ),
            'update_item'                => __( 'Update Residency Scope', 'rebuild_cpt' ),
            'view_item'                  => __( 'View Residency Scope', 'rebuild_cpt' ),
            'separate_items_with_commas' => __( 'Separate residency scopes with commas', 'rebuild_cpt' ),
            'add_or_remove_items'        => __( 'Add or remove residency scopes', 'rebuild_cpt' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'rebuild_cpt' ),
            'popular_items'              => __( 'Popular Residency Scopes', 'rebuild_cpt' ),
            'search_items'               => __( 'Search Residency Scopes', 'rebuild_cpt' ),
            'not_found'                  => __( 'Not Found', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                       => 'residency-scope',
            'with_front'                 => true,
            'hierarchical'               => false,
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'query_var'                  => 'residency_category',
            'rewrite'                    => $rewrite,
        );
        register_taxonomy( 'residency_category', array( 'residency' ), $args );

    }

    add_action( 'init', 'rebuild_residency_category', 0 );

}

/**
 * Hide residency_category metabox from content edit screen
 * We're using ACF to select residency_category so we can limit to 1 selection
 */
if( ( ! function_exists( 'rebuild_foundation_remove_residency_category' ) ) && is_admin() ) {

  function rebuild_foundation_remove_residency_category() {

    remove_meta_box( 'site_categorydiv', 'residency', 'side' );
    remove_meta_box( 'rebuild_residency_categorydiv', 'residency', 'side' );
          
  }

  add_action( 'admin_menu', 'rebuild_foundation_remove_residency_category' );

}


?>