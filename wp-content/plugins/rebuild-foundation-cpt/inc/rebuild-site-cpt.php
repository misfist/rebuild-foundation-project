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

if ( ! function_exists('rebuild_site_cpt') ) {

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
            'taxonomies'          => array( 'rebuild_site_category' ),
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
            //'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'query_var'           => 'site',
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
        );
        register_post_type( 'rebuild_site', $args );

    }
    add_action( 'init', 'rebuild_site_cpt', 0 );

}

?>