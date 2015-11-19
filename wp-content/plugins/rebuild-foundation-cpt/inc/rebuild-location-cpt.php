<?php

/**
 * Rebuild Foundation Location Custom Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */


/*
 * Location Custom Post Type
 *
 */

if ( ! function_exists('rebuild_location_cpt') ) {

    // Register Custom Post Type
    function rebuild_location_cpt() {

        $labels = array(
            'name'                => _x( 'Location', 'Post Type General Name', 'rebuild_cpt' ),
            'singular_name'       => _x( 'Location', 'Post Type Singular Name', 'rebuild_cpt' ),
            'menu_name'           => __( 'Locations', 'rebuild_cpt' ),
            'name_admin_bar'      => __( 'Location', 'rebuild_cpt' ),
            'parent_item_colon'   => __( 'Parent Location:', 'rebuild_cpt' ),
            'all_items'           => __( 'All Locations', 'rebuild_cpt' ),
            'add_new_item'        => __( 'Add New Location', 'rebuild_cpt' ),
            'add_new'             => __( 'Add New Location', 'rebuild_cpt' ),
            'new_item'            => __( 'New Location', 'rebuild_cpt' ),
            'edit_item'           => __( 'Edit Location', 'rebuild_cpt' ),
            'update_item'         => __( 'Update Location', 'rebuild_cpt' ),
            'view_item'           => __( 'View Location', 'rebuild_cpt' ),
            'search_items'        => __( 'Search Location', 'rebuild_cpt' ),
            'not_found'           => __( 'Not found', 'rebuild_cpt' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                => 'location',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
        );
        $args = array(
            'label'               => __( 'Location', 'rebuild_cpt' ),
            'labels'              => $labels,
            'supports'            => array( 'title' ),
            'taxonomies'          => array( 'site_category' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-location-alt',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => 'locations',
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'query_var'           => 'location',
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
        );
        register_post_type( 'location', $args );

    }
    add_action( 'init', 'rebuild_location_cpt', 0 );

}

/*
* Remove the site category metabox
*/

if(! function_exists( 'remove_location_site_category_meta' ) ) {

    function remove_location_site_category_meta() {
        remove_meta_box( 'site_categorydiv', 'location', 'side');
    }

    add_action( 'admin_menu' , 'remove_location_site_category_meta' );

}

?>