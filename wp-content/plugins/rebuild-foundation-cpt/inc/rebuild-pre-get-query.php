<?php

/**
 * Rebuild Foundation Custom Columns
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

if(! function_exists( 'rebuild_events_get_posts_query' ) ) {

  function rebuild_events_get_posts_query( $query ) {

    // bail early if is in admin
    if( is_admin() ) {
      
      return;
      
    }

    if( is_post_type_archive( 'rebuild_event' ) && $query->is_main_query() ) {

        // Check if get_query_var( 'event_year' ) is passed & is valid, otherwise set to current year
        $year = ( !empty( get_query_var( 'event_year' ) ) ) ? absint( get_query_var( 'event_year' ) ) : date( 'Y' );

        // Check if get_query_var( 'event_month' ) is passed & is valid, otherwise set to blank
        $event_month = ( !empty( get_query_var( 'event_month' ) ) && 
            in_array( get_query_var( 'event_month' ), range( 1, 12 ) ) ) ? zeroise( get_query_var( 'event_month' ), 2 ) : '';

        // If neither $event_year or $event_month, return
        if( !isset( $event_year ) && !isset( $event_month ) ) {

            return;
        }

        // If you click year only, you should see full year select
        // e.g. {$year}0101 - {$year}1231

        // If you click month only, you should see current month of current year
        // e.g. date( 'Y' ){month}01 - date( 'Y' ){month}31

        // If you click both month and year, you should see selected year and month
        // e.g. {$year}{$month}01 - {$year}{$month}31

        // otherwise, you see the full current year
        // e.g. date( 'Y' )0101 - date( 'Y' )1231

        $start_month = ( $event_month ) ? $event_month : zeroise( 1, 2 );
        $end_month =  ( $event_month ) ? $event_month : zeroise( 12, 2 );


        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'start_date',
                'compare' => '>=',
                'value'   => "{$year}{$start_month}01",
                'type'    => 'NUMERIC',
            ),
            array(
                'key'     => 'end_date',
                'compare' => '<=',
                'value'   => "{$year}{$end_month}31", // Doesn't matter if there aren't 31 days in this month, will still work,
                'type'    => 'NUMERIC',
            )
        );

        $query->set( 'meta_query', $meta_query );

    }

  }

  add_action( 'pre_get_posts', 'rebuild_events_get_posts_query' );

}

?>