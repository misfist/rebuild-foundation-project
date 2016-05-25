<?php
/**
 * @package rsvp-pro
 * @author Swim or Die Software
 * @version 2.1.8
 */
/*
Plugin Name: RSVP Pro
Text Domain: rsvp-pro-plugin
Plugin URI: http://www.rsvpproplugin.com
Description: This plugin allows guests to RSVP to an event.  
Author: Swim or Die Software
Version: 2.1.8
Author URI: http://www.swimordiesoftware.com
License: Commercial
*/
#
# INSTALLATION: see readme.txt
#
# USAGE: Once the RSVP plugin has been installed, you can set the custom text 
#        via Settings -> RSVP Options in the  admin area. 
#      
#        To add, edit, delete and see rsvp status there will be a new RSVP admin
#        area just go there.

// Exit if accessed directly.
if (!defined('ABSPATH')):
	exit;
endif;

/**
 * Main RSVP Pro Class
 *
 * @since 2.1.4
 */
final class Rsvp_Pro {
	/** Singleton ********************/

	/**
	 * @var Singleton instance variable
	 * @since 2.1.4
	 */
	private static $instance;

	/**
	 *  Creates the instance for the Rsvp_Pro object. 
	 *
	 *  Enforces that this object is a singleton and is only created once..
	 *  
	 * @since 2.1.4 
	 * @static
	 * @return object|Rsvp_Pro
	 */
	public static function instance() {
		if(!isset(self::$instance) && !(self::$instance instanceof Rsvp_Pro)) {
			self::$instance = new Rsvp_Pro();
			self::$instance->create_constants();

			self::$instance->include_files();

		}

		return self::$instance;
	}

	/**
	 * Create the bootstrap constants needed for this plugin 
	 *
	 * @access private
	 * @since 2.1.4
	 * @return void
	 */
	private function create_constants() {
		global $plugin, $mu_plugin, $network_plugin;

		$my_plugin_file = __FILE__;

  		if (isset($plugin)) {
    		$my_plugin_file = $plugin;
  		}
  		else if (isset($mu_plugin)) {
    		$my_plugin_file = $mu_plugin;
  		}
  		else if (isset($network_plugin)) {
    		$my_plugin_file = $network_plugin;
  		}

  		if(!defined('RSVP_PRO_PLUGIN_FILE')) {
  			define('RSVP_PRO_PLUGIN_FILE', $my_plugin_file);	
  		}
  		
  		if(!defined('RSVP_PRO_PLUGIN_PATH')) {
  			define('RSVP_PRO_PLUGIN_PATH', WP_PLUGIN_DIR.'/'.basename(dirname($my_plugin_file)));	
  		}
	}

	/**
	 * Include the files for the plugin to run. This function will slowly
	 * be removed as we refactor
	 *
	 * @access private
	 * @since 2.1.4
	 * @return void
	 */
	private function include_files() {

		require_once RSVP_PRO_PLUGIN_PATH.'/includes/wp-constants.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/rsvp_utils.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/rsvp_db_setup.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/rsvp_frontend.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/rsvp_licensing.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/rsvp_pro_wizard_forms.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/admin/reoccurring_handler.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/rsvp_updater.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/frontend/rsvp_pro_handle_rsvp.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/frontend/rsvp_pro_additional_attendee_js.inc.php';
		require_once RSVP_PRO_PLUGIN_PATH.'/includes/wp-rsvp.php';
	}
}

/**
 * Main function that returns the object for the RSVP Pro Plugin. 
 *
 * It will return a singleton of the main object for the plugin
 *
 * @since 2.1.4
 * @return object|Rsvp_Pro
 */
function RSVP_PRO() {
	return Rsvp_Pro::instance();
}

// Get the plugin running
RSVP_PRO();
