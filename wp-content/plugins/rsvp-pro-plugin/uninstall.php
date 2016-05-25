<?php
/**
 * Uninstall RSVP Pro Plugin
 *
 * @package     rsvp pro
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2016, MDE Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.7
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load EDD file
include_once( 'wp-rsvp.php' );

global $wpdb;

if( get_option(RSVP_PRO_GLOBAL_OPTION_DELETE_TABLES) == "Y") {

	/*
	
	define("PRO_ATTENDEES_TABLE", $wpdb->prefix."rsvpAttendees");
define("PRO_ATTENDEE_SUB_EVENTS_TABLE", $wpdb->prefix."rsvpAttendeeSubEvents");
define("PRO_ASSOCIATED_ATTENDEES_TABLE", $wpdb->prefix."rsvpAssociatedAttendees");
define("PRO_QUESTIONS_TABLE", $wpdb->prefix."rsvpProCustomQuestions");
define("PRO_QUESTION_TYPE_TABLE", $wpdb->prefix."rsvpProQuestionTypes");
define("PRO_ATTENDEE_ANSWERS", $wpdb->prefix."rsvpAttendeeAnswers");
define("PRO_QUESTION_ANSWERS_TABLE", $wpdb->prefix."rsvpProCustomQuestionAnswers");
define("PRO_QUESTION_ATTENDEES_TABLE", $wpdb->prefix."rsvpProCustomQuestionAttendees");
define("PRO_EVENT_TABLE", $wpdb->prefix."rsvpEvents");
define("PRO_EVENT_ATTENDEE_TABLE", $wpdb->prefix."rsvpEventAttendees");
	 */
	// Delete the tables
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpAttendeeSubEvents" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpAssociatedAttendees" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpAttendees" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpProCustomQuestions" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpProQuestionTypes" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpAttendeeAnswers" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpProCustomQuestionAnswers" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpProCustomQuestionAttendees" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpEvents" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rsvpEventAttendees" );

	// Delete the options...
	delete_option("rsvp_pro_db_version");
	delete_option(RSVP_PRO_GLOBAL_OPTION_DELETE_TABLES);
	delete_option(RSVP_PRO_GLOBAL_OPTION_STYLES);
}