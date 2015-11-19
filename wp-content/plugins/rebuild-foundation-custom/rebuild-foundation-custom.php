<?php 

/**
 * Rebuild Foundation Custom
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom
 *
 * @wordpress-plugin
 * Plugin Name:       Rebuild Foundation Custom Functions
 * Plugin URI:        
 * Description:       Adds custom functions to the site
 * Version:           1.0.0
 * Author:            Pea
 * Author URI:        http://misfist.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rebuild_custom
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* ---------------------------------- *
 * Constants
 * ---------------------------------- */

if ( !defined( 'REBUILD_CUSTOM_PLUGIN_DIR' ) ) {
    define( 'REBUILD_CUSTOM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'REBUILD_CUSTOM_PLUGIN_URL' ) ) {
    define( 'REBUILD_CUSTOM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Include files
include_once( REBUILD_CUSTOM_PLUGIN_DIR . '/inc/rebuild-foundation-shortcodes.php' );
include_once( REBUILD_CUSTOM_PLUGIN_DIR . '/inc/rebuild-foundation-fields.php' );
include_once( REBUILD_CUSTOM_PLUGIN_DIR . '/inc/disable-theme-and-plugin-editor.php' );
include_once( REBUILD_CUSTOM_PLUGIN_DIR . '/inc/rebuild-foundation-functions.php' );




?>