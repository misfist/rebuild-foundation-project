<?php

/**
 * Rebuild Foundation Google Calendar Link
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

/*
 * Google Calendar Link
 * Generates a link with variables used by Google Calendar
 * http://exhibita.com/blog/create-google-calendar-entry-with-a-link/
 */

if(! function_exists( 'rebuild_google_calendar_link' ) ) {

    function generate_calendar_button() {

        global $post;

        // Event Values
        $event_id = get_the_ID();
        $meta = get_post_meta( $event_id );
        $event_title = $post->post_title;
        $event_description = rebuild_truncate_text( $post->post_content, 100 );

        $event_meta = get_post_meta( $event_id );
        $event_url = get_permalink( $event_id );

        $start_time = date( 'H:i:s', $meta['start_time'][0] );
        $end_time = date( 'H:i:s', $meta['end_time'][0] );
        $start_date = $meta['start_date'][0];
        $end_date = $meta['end_date'][0];

        $start = strtotime( $start_date . ' ' . $start_time );
        $end = strtotime( $end_date . ' ' . $end_time );

        $location_id = get_field( 'location', $event_id, false );
        $location_name = get_the_title( $location_id );
        $location_address = rebuild_convert_location_to_string( $event_id );
        $event_location = $location_name . ', ' . $location_address;
        $site_name = get_bloginfo( 'name' );
        $site_url = get_bloginfo( 'url' );

        $url = 'https://www.google.com/calendar/event?action=TEMPLATE';

        $description = rebuild_truncate_text( $event_description, 200 );

        $parts = array(
            'text' => urlencode( $event_title ),
            'details' => urlencode( $description ),
            'dates' => urlencode( rebuild_date_to_cal( $start ) ) . "/" . urlencode( rebuild_date_to_cal( $end ) ),
            'czt' => urlencode( 'America/Chicago' ),
            'location' => urlencode( $event_location ),
            'sprop' => 'website:' . urlencode( $event_url ),
        );

        $full_link = $url;

        foreach ( $parts as $key => $value ) {
            $full_link .= "&" . $key . "=" . $value;
        }

        return $full_link;

    }
}

/*
 * iCal Formatted Date
 * Helper Function to create ical formatted date
 */

if(! function_exists( 'rebuild_date_to_cal' ) ) {

    function rebuild_date_to_cal( $timestamp ) {
      return date( 'Ymd\THis', $timestamp );
    }

}

/*
 * Truncate Text
 * Helper Function to truncate text
 */

if(! function_exists( 'rebuild_truncate_text' ) ) {

    function rebuild_truncate_text( $string, $limit, $break = '.', $pad = '...' ) {
      // return with no change if string is shorter than $limit
      if( strlen( $string ) <= $limit ) return $string;

      // is $break present between $limit and the end of the string?
      if( false !== ( $breakpoint = strpos( $string, $break, $limit ) ) ) {
        if( $breakpoint < strlen( $string ) - 1 ) {
          $string = substr( $string, 0, $breakpoint ) . $pad;
        }
      }

      return $string;
    }

}

?>