<?php

/**
 * Rebuild Foundation Event Custom Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

if ( ! function_exists('rebuild_event_cpt') ) {

    // Register Custom Post Type
    function rebuild_event_cpt() {

        $labels = array(
            'name'                => _x( 'Events', 'Post Type General Name', 'rebuild_cpt' ),
            'singular_name'       => _x( 'Event', 'Post Type Singular Name', 'rebuild_cpt' ),
            'menu_name'           => __( 'Events', 'rebuild_cpt' ),
            'name_admin_bar'      => __( 'Event', 'rebuild_cpt' ),
            'parent_item_colon'   => __( 'Parent Event:', 'rebuild_cpt' ),
            'all_items'           => __( 'All Events', 'rebuild_cpt' ),
            'add_new_item'        => __( 'Add New Event', 'rebuild_cpt' ),
            'add_new'             => __( 'Add New Event', 'rebuild_cpt' ),
            'new_item'            => __( 'New Event', 'rebuild_cpt' ),
            'edit_item'           => __( 'Edit Event', 'rebuild_cpt' ),
            'update_item'         => __( 'Update Event', 'rebuild_cpt' ),
            'view_item'           => __( 'View Event', 'rebuild_cpt' ),
            'search_items'        => __( 'Search Event', 'rebuild_cpt' ),
            'not_found'           => __( 'Not found', 'rebuild_cpt' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                => 'event',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
        );
        $args = array(
            'label'               => __( 'Event', 'rebuild_cpt' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', ),
            'taxonomies'          => array( 'rebuild_site_category', ' rebuild_event_category', ' rebuild_event_tag' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-calendar-alt',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => 'events',
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'query_var'           => 'event',
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
        );
        register_post_type( 'rebuild_event', $args );

    }
    add_action( 'init', 'rebuild_event_cpt', 0 );

}

?>