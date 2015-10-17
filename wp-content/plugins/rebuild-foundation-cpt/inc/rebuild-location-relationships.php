<?php

/**
 * Rebuild Foundation Location Relationship
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 *
 */

if(! function_exists( 'rebuild_location_relationships' ) ) {

    function rebuild_location_relationships() {
        p2p_register_connection_type( array(
            'name' => 'location_to_all',
            'from' => 'rebuild_location',
            'to' => array(
                'rebuild_site',
                'rebuild_event',
                'rebuild_exhibition'
            ),
            'cardinality' => 'one-to-many',
            'title' => array(
                'to' => __( 'Location', 'rebuild_cpt' )
            ),
            'to_labels' => array(
                'singular_name' => __( 'Location', 'rebuild_cpt' ),
                'search_items' => __( 'Search locations', 'rebuild_cpt' ),
                'not_found' => __( 'No locations found.', 'rebuild_cpt' ),
                'create' => __( 'Assign Location', 'rebuild_cpt' ),
            ),
            'admin_box' => array(
                'show' => 'to',
                'context' => 'advanced'
            )
        ) );

    }
    add_action( 'p2p_init', 'rebuild_location_relationships' );

}


?>