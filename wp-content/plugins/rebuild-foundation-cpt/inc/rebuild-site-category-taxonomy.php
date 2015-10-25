<?php

/**
 * Rebuild Foundation Site Category Taxonomy
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
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
            'slug'                       => 'site_category',
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
        register_taxonomy( 'rebuild_site_category', array( 
            'post',
            'rebuild_site', 
            'rebuild_location', 
            'rebuild_event', 
            'rebuild_exhibition' ), $args );

    }
    add_action( 'init', 'rebuild_site_category', 0 );

}

?>