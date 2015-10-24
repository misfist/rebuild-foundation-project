<?php

/**
 * Rebuild Foundation Exhibition Category Taxonomy
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
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
            'slug'                       => 'exhibition-categories',
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
        register_taxonomy( 'rebuild_exhibition_category', array( 'rebuild_exhibition' ), $args );

    }
    add_action( 'init', 'rebuild_exhibition_category', 0 );

}

?>