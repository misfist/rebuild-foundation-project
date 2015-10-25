<?php

/**
 * Rebuild Foundation Event Tag Taxonomy
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
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
            'slug'                       => 'event-tags',
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
        register_taxonomy( 'rebuild_event_tag', array( 'rebuild_event' ), $args );

    }
    add_action( 'init', 'rebuild_event_tag', 0 );

}

?>