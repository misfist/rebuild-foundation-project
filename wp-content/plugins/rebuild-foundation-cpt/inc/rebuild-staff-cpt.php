<?php

/**
 * Rebuild Foundation Staff Custom Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

/*
 * Staff Custom Post Type
 *
 */

if ( ! function_exists('rebuild_foundation_staff_post_type') ) {

    // Register Custom Post Type
    function rebuild_foundation_staff_post_type() {

        $labels = array(
            'name'                  => _x( 'Our Staff', 'Post Type General Name', 'rebuild_cpt' ),
            'singular_name'         => _x( 'Staff Member', 'Post Type Singular Name', 'rebuild_cpt' ),
            'menu_name'             => __( 'Staff', 'rebuild_cpt' ),
            'name_admin_bar'        => __( 'Staff', 'rebuild_cpt' ),
            'parent_item_colon'     => __( 'Parent Staff Member:', 'rebuild_cpt' ),
            'all_items'             => __( 'All Staff', 'rebuild_cpt' ),
            'add_new_item'          => __( 'Add Staff Member', 'rebuild_cpt' ),
            'add_new'               => __( 'Add Staff Member', 'rebuild_cpt' ),
            'new_item'              => __( 'New Staff Member', 'rebuild_cpt' ),
            'edit_item'             => __( 'Edit Staff Member', 'rebuild_cpt' ),
            'update_item'           => __( 'Update Staff Member', 'rebuild_cpt' ),
            'view_item'             => __( 'View Staff Member', 'rebuild_cpt' ),
            'search_items'          => __( 'Search Staff Member', 'rebuild_cpt' ),
            'not_found'             => __( 'Not found', 'rebuild_cpt' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'rebuild_cpt' ),
            'items_list'            => __( 'Staff list', 'rebuild_cpt' ),
            'items_list_navigation' => __( 'Staff list navigation', 'rebuild_cpt' ),
            'filter_items_list'     => __( 'Filter staff members', 'rebuild_cpt' ),
        );
        $args = array(
            'label'                 => __( 'Staff Member', 'rebuild_cpt' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', ),
            'taxonomies'            => array( 'staff_category' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-id',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,        
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        register_post_type( 'staff', $args );

    }
    add_action( 'init', 'rebuild_foundation_staff_post_type', 0 );

}

/*
 * Staff Group Custom Taxonomy
 *
 */

if ( ! function_exists( 'rebuild_foundation_staff_category' ) ) {

    // Register Custom Taxonomy
    function rebuild_foundation_staff_category() {

        $labels = array(
            'name'                       => _x( 'Staff Groups', 'Taxonomy General Name', 'rebuild_cpt' ),
            'singular_name'              => _x( 'Staff Group', 'Taxonomy Singular Name', 'rebuild_cpt' ),
            'menu_name'                  => __( 'Staff Group', 'rebuild_cpt' ),
            'all_items'                  => __( 'All Groups', 'rebuild_cpt' ),
            'parent_item'                => __( 'Parent Group', 'rebuild_cpt' ),
            'parent_item_colon'          => __( 'Parent Group:', 'rebuild_cpt' ),
            'new_item_name'              => __( 'New Group Name', 'rebuild_cpt' ),
            'add_new_item'               => __( 'Add New Group', 'rebuild_cpt' ),
            'edit_item'                  => __( 'Edit Group', 'rebuild_cpt' ),
            'update_item'                => __( 'Update Group', 'rebuild_cpt' ),
            'view_item'                  => __( 'View Group', 'rebuild_cpt' ),
            'separate_items_with_commas' => __( 'Separate groups with commas', 'rebuild_cpt' ),
            'add_or_remove_items'        => __( 'Add or remove groups', 'rebuild_cpt' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'rebuild_cpt' ),
            'popular_items'              => __( 'Popular Groups', 'rebuild_cpt' ),
            'search_items'               => __( 'Search Groups', 'rebuild_cpt' ),
            'not_found'                  => __( 'Not Found', 'rebuild_cpt' ),
            'items_list'                 => __( 'Groups list', 'rebuild_cpt' ),
            'items_list_navigation'      => __( 'Groups list navigation', 'rebuild_cpt' ),
        );
        $rewrite = array(
            'slug'                       => 'group',
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
            'rewrite'                    => $rewrite,
        );
        register_taxonomy( 'staff_category', array( 'staff' ), $args );

        

    }

add_action( 'init', 'rebuild_foundation_staff_category', 0 );

}

/*
* Remove the metabox, will be added with ACF
*/

if(! function_exists( 'remove_staff_category_meta' ) ) {

    function remove_staff_category_meta() {
        remove_meta_box( 'staff_categorydiv', 'staff', 'side');
    }

    add_action( 'admin_menu' , 'remove_staff_category_meta' );

}

?>