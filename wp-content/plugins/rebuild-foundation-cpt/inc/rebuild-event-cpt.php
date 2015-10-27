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

/*
 * Event Custom Post Type
 *
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
            'taxonomies'          => array( 'site_category', 'event_category', 'event_tag' ),
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
        register_post_type( 'event', $args );

    }
    add_action( 'init', 'rebuild_event_cpt', 0 );

}

/*
 * Event Category
 *
 */

if ( ! function_exists( 'rebuild_event_category' ) ) {

    // Register Custom Taxonomy
    function rebuild_event_category() {

        $labels = array(
            'name'                       => _x( 'Event Categories', 'Taxonomy General Name', 'rebuild_cpt' ),
            'singular_name'              => _x( 'Event Category', 'Taxonomy Singular Name', 'rebuild_cpt' ),
            'menu_name'                  => __( 'Event Categories', 'rebuild_cpt' ),
            'all_items'                  => __( 'All Event Categories', 'rebuild_cpt' ),
            'parent_item'                => __( 'Parent Event Category', 'rebuild_cpt' ),
            'parent_item_colon'          => __( 'Parent Event Category:', 'rebuild_cpt' ),
            'new_item_name'              => __( 'New Event Category Name', 'rebuild_cpt' ),
            'add_new_item'               => __( 'Add New Event Category', 'rebuild_cpt' ),
            'edit_item'                  => __( 'Edit Event Category', 'rebuild_cpt' ),
            'update_item'                => __( 'Update Event Category', 'rebuild_cpt' ),
            'view_item'                  => __( 'View Event Category', 'rebuild_cpt' ),
            'separate_items_with_commas' => __( 'Separate event categories with commas', 'rebuild_cpt' ),
            'add_or_remove_items'        => __( 'Add or remove event categories', 'rebuild_cpt' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'rebuild_cpt' ),
            'popular_items'              => __( 'Popular Event Categories', 'rebuild_cpt' ),
            'search_items'               => __( 'Search Event Categories', 'rebuild_cpt' ),
            'not_found'                  => __( 'Not Found', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                       => 'event-category',
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
            'query_var'                  => 'event_category',
            'rewrite'                    => $rewrite,
        );
        register_taxonomy( 'event_category', array( 'event' ), $args );

    }
    add_action( 'init', 'rebuild_event_category', 0 );

}

/*
 * Event Tag
 *
 */

if ( ! function_exists( 'rebuild_event_tag' ) ) {

    // Register Custom Taxonomy
    function rebuild_event_tag() {

        $labels = array(
            'name'                       => _x( 'Event Tags', 'Taxonomy General Name', 'rebuild_cpt' ),
            'singular_name'              => _x( 'Event Tag', 'Taxonomy Singular Name', 'rebuild_cpt' ),
            'menu_name'                  => __( 'Event Tags', 'rebuild_cpt' ),
            'all_items'                  => __( 'All Event Tags', 'rebuild_cpt' ),
            'parent_item'                => __( 'Parent Event Tag', 'rebuild_cpt' ),
            'parent_item_colon'          => __( 'Parent Event Tag:', 'rebuild_cpt' ),
            'new_item_name'              => __( 'New Event Tag Name', 'rebuild_cpt' ),
            'add_new_item'               => __( 'Add New Event Tag', 'rebuild_cpt' ),
            'edit_item'                  => __( 'Edit Event Tag', 'rebuild_cpt' ),
            'update_item'                => __( 'Update Event Tag', 'rebuild_cpt' ),
            'view_item'                  => __( 'View Event Category', 'rebuild_cpt' ),
            'separate_items_with_commas' => __( 'Separate event tags with commas', 'rebuild_cpt' ),
            'add_or_remove_items'        => __( 'Add or remove event tags', 'rebuild_cpt' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'rebuild_cpt' ),
            'popular_items'              => __( 'Popular Event Tags', 'rebuild_cpt' ),
            'search_items'               => __( 'Search Event Tags', 'rebuild_cpt' ),
            'not_found'                  => __( 'Not Found', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                       => 'event-tag',
            'with_front'                 => true,
            'hierarchical'               => false,
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'query_var'                  => 'event_tag',
            'rewrite'                    => $rewrite,
        );
        register_taxonomy( 'event_tag', array( 'event' ), $args );

    }
    add_action( 'init', 'rebuild_event_tag', 0 );

}


?>