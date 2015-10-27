<?php

/**
 * Rebuild Foundation Exhibition Custom Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

/*
 * Exhibition Custom Post Type
 *
 */

if ( ! function_exists('rebuild_exhibition_cpt') ) {

    // Register Custom Post Type
    function rebuild_exhibition_cpt() {

        $labels = array(
            'name'                => _x( 'Exhibitions', 'Post Type General Name', 'rebuild_cpt' ),
            'singular_name'       => _x( 'Exhibition', 'Post Type Singular Name', 'rebuild_cpt' ),
            'menu_name'           => __( 'Exhibitions', 'rebuild_cpt' ),
            'name_admin_bar'      => __( 'Exhibition', 'rebuild_cpt' ),
            'parent_item_colon'   => __( 'Parent Exhibition:', 'rebuild_cpt' ),
            'all_items'           => __( 'All Exhibitions', 'rebuild_cpt' ),
            'add_new_item'        => __( 'Add New Exhibition', 'rebuild_cpt' ),
            'add_new'             => __( 'Add New Exhibition', 'rebuild_cpt' ),
            'new_item'            => __( 'New Exhibition', 'rebuild_cpt' ),
            'edit_item'           => __( 'Edit Exhibition', 'rebuild_cpt' ),
            'update_item'         => __( 'Update Exhibition', 'rebuild_cpt' ),
            'view_item'           => __( 'View Exhibition', 'rebuild_cpt' ),
            'search_items'        => __( 'Search Exhibition', 'rebuild_cpt' ),
            'not_found'           => __( 'Not found', 'rebuild_cpt' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                => 'exhibition',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
        );
        $args = array(
            'label'               => __( 'Exhibition', 'rebuild_cpt' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', ),
            'taxonomies'          => array( 'site_category', 'exhibition_category' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-format-gallery',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => 'exhibitions',
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'query_var'           => 'exhibition',
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
        );
        register_post_type( 'exhibition', $args );

    }
    add_action( 'init', 'rebuild_exhibition_cpt', 0 );

}

/*
 * Exhibition Category
 *
 */


if ( ! function_exists( 'rebuild_exhibition_category' ) ) {

    // Register Custom Taxonomy
    function rebuild_exhibition_category() {

        $labels = array(
            'name'                       => _x( 'Exhibition Categories', 'Taxonomy General Name', 'rebuild_cpt' ),
            'singular_name'              => _x( 'Exhibition Category', 'Taxonomy Singular Name', 'rebuild_cpt' ),
            'menu_name'                  => __( 'Exhibition Categories', 'rebuild_cpt' ),
            'all_items'                  => __( 'All Exhibition Categories', 'rebuild_cpt' ),
            'parent_item'                => __( 'Parent Exhibition Category', 'rebuild_cpt' ),
            'parent_item_colon'          => __( 'Parent Exhibition Category:', 'rebuild_cpt' ),
            'new_item_name'              => __( 'New Exhibition Category Name', 'rebuild_cpt' ),
            'add_new_item'               => __( 'Add New Exhibition Category', 'rebuild_cpt' ),
            'edit_item'                  => __( 'Edit Exhibition Category', 'rebuild_cpt' ),
            'update_item'                => __( 'Update Exhibition Category', 'rebuild_cpt' ),
            'view_item'                  => __( 'View Exhibition Category', 'rebuild_cpt' ),
            'separate_items_with_commas' => __( 'Separate exhibition categories with commas', 'rebuild_cpt' ),
            'add_or_remove_items'        => __( 'Add or remove exhibition categories', 'rebuild_cpt' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'rebuild_cpt' ),
            'popular_items'              => __( 'Popular Exhibition Categories', 'rebuild_cpt' ),
            'search_items'               => __( 'Search Exhibition Categories', 'rebuild_cpt' ),
            'not_found'                  => __( 'Not Found', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                       => 'exhibition-category',
            'with_front'                 => true,
            'hierarchical'               => false,
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => false,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'query_var'                  => 'exhibition_category',
            'rewrite'                    => $rewrite,
        );
        register_taxonomy( 'exhibition_category', array( 'exhibition' ), $args );

    }

    add_action( 'init', 'rebuild_exhibition_category', 0 );

}

?>