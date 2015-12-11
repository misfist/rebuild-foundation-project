<?php

/**
 * Rebuild Foundation iCal Template
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

global $post;

// File Values
$calendar_name = __( 'Rebuild Foundation ', 'rebuild_cpt' );
$filename = $post->post_name . '.ics';

// Event Values
$event_id = $post->ID;
$meta = get_post_meta( $post->ID );
$event_guid = $post->guid;
$event_title = $post->post_title;
$event_description = $post->post_content;

$event_meta = get_post_meta( $post->ID );
$event_url = get_permalink( $post->ID );

$start_time = date( 'H:i:s', $meta['start_time'][0] );
$end_time = date( 'H:i:s', $meta['end_time'][0] );
$start_date = $meta['start_date'][0];
$end_date = $meta['end_date'][0];

$start = strtotime( $start_date . ' ' . $start_time );
$end = strtotime( $end_date . ' ' . $end_time );

$location_name = rebuild_get_location_name( $event_id );
$location_address = rebuild_convert_location_to_string( $event_id );
$event_location = $location_name . ', ' . $location_address;

//  - Read up on RFC 5545, the iCalendar specification. There is a lot of helpful info in there, such as formatting rules. There are also many more options to set, including alarms, invitees, busy status, etc.
// https://www.ietf.org/rfc/rfc5545.txt

if(! function_exists( 'rebuild_date_to_cal' ) ) {
    function rebuild_date_to_cal( $timestamp ) {
      return date( 'Ymd\THis\Z', $timestamp );
    }
}

// Escapes a string of characters
if(! function_exists( 'escape_string' ) ) {
    function escape_string( $string ) {
      return preg_replace( '/([\,;])/','\\\$1', $string );
    }
}

// Original PHP code by Chirp Internet: www.chirp.com.au
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

// Generate ical content
$ical = 'BEGIN:VCALENDAR' . "\r\n";
$ical .= 'VERSION:2.0' . "\r\n";
$ical .= 'PRODID:-//' . escape_string( $calendar_name ) .'//NONSGML v1.0//EN' . "\r\n";
$ical .= 'CALSCALE:GREGORIAN' . "\r\n";
$ical .= 'BEGIN:VTIMEZONE' . "\r\n";
$ical .= 'TZURL:http://tzurl.org/zoneinfo-outlook/America/Chicago' . "\r\n";
$ical .= 'TZID:America/Chicago' . "\r\n";
$ical .= 'X-LIC-LOCATION:America/Chicago' . "\r\n";
$ical .= 'BEGIN:DAYLIGHT' . "\r\n";
$ical .= 'TZOFFSETFROM:-0600' . "\r\n";
$ical .= 'TZOFFSETTO:-0500' . "\r\n";
$ical .= 'TZNAME:CDT' . "\r\n";
$ical .= 'DTSTART:19700308T020000' . "\r\n";
$ical .= 'END:DAYLIGHT' . "\r\n";
$ical .= 'BEGIN:STANDARD' . "\r\n";
$ical .= 'TZOFFSETFROM:-0500' . "\r\n";
$ical .= 'TZOFFSETTO:-0600' . "\r\n";
$ical .= 'TZNAME:CST' . "\r\n";
$ical .= 'DTSTART:19701101T020000' . "\r\n";
$ical .= 'END:STANDARD' . "\r\n";
$ical .= 'END:VTIMEZONE' . "\r\n";
$ical .= 'BEGIN:VEVENT' . "\r\n";
$ical .= 'DTSTAMP:' . rebuild_date_to_cal( time() ) . "\r\n";
$ical .= 'UID:' . $event_guid . "\r\n";
$ical .= 'DTSTART;TZID="America/Chicago":' . rebuild_date_to_cal( $start ) . "\r\n";
$ical .= 'DTEND;TZID="America/Chicago":' . rebuild_date_to_cal( $end ) . "\r\n";
$ical .= 'DESCRIPTION:' . escape_string( rebuild_truncate_text( $event_description, 300 ) ) . "\r\n";
$ical .= 'SUMMARY:' . escape_string( $event_title ) . "\r\n";
$ical .= 'URL;VALUE=URI:' . escape_string( $event_url ) . "\r\n";
$ical .= 'LOCATION:' . escape_string( $event_location ) . "\r\n";
$ical .= 'END:VEVENT' . "\r\n";
$ical .= 'END:VCALENDAR' . "\r\n";

//set correct content-type-header
header( 'Content-type: text/calendar; charset=utf-8' );
header( 'Content-Disposition: attachment; filename=' . $filename );

echo $ical;

exit;
?>