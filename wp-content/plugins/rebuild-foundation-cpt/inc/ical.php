<?php

/**
 * Rebuild Foundation iCal
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

if(! function_exists( 'rebuild_ical_endpoint' ) ) {

    function rebuild_ical_endpoint() {

        add_rewrite_endpoint( 'ical', EP_PERMALINK );

    }

    add_action( 'init', 'rebuild_ical_endpoint' );

}

if(! function_exists( 'rebuild_ical_rewrite_template' ) ) {

    function rebuild_ical_rewrite_template() {
     
        global $wp_query;
     
        // if this is not a request for json or a singular object then bail
        if ( ! isset( $wp_query->query_vars['ical'] ) || ! is_singular() )
            return;
     
        // include custom template
        include REBUILD_CPT_PLUGIN_DIR . '/templates/ical-template.php';
        exit;

    }
     
    add_action( 'template_redirect', 'rebuild_ical_rewrite_template' );

}


?>