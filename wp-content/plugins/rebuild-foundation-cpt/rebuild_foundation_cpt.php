<?php 

/**
 * Rebuild Foundation Custom Post Types
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 *
 * @wordpress-plugin
 * Plugin Name:       Rebuild Foundation Custom Post Types
 * Plugin URI:        
 * Description:       Adds custom post types and taxonomy for sites, events, exhibitions and locations.
 * Version:           1.0.0
 * Author:            Pea
 * Author URI:        http://misfist.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rebuild_cpt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* ---------------------------------- *
 * Constants
 * ---------------------------------- */

if ( !defined( 'REBUILD_CPT_PLUGIN_DIR' ) ) {
    define( 'REBUILD_CPT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'REBUILD_CPT_PLUGIN_URL' ) ) {
    define( 'REBUILD_CPT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Include files
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-site-cpt.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-event-cpt.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-exhibition-cpt.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-staff-cpt.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-location-cpt.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-rewrite-rules.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-custom-fields.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-custom-columns.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-pre-get-query.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/rebuild-helpers.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/ical.php' );
include_once( REBUILD_CPT_PLUGIN_DIR . '/inc/google-calendar.php' );


function rebuild_plugin_activation() {

    // Then flush them
    flush_rewrite_rules();

}
register_activation_hook( __FILE__, 'rebuild_plugin_activation');
 
 
function rebuild_plugin_deactivation() {
 
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rebuild_plugin_deactivation');


?>