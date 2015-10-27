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
 * Description:       Adds custom post types and taxonomy for sites, events, exhibitions and locations
 * Version:           1.0.0
 * Author:            Pea
 * Author URI:        http://misfist.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rebuild_cpt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Include files
include_once( 'inc/rebuild-site-cpt.php' );
include_once( 'inc/rebuild-event-cpt.php' );
include_once( 'inc/rebuild-exhibition-cpt.php' );
include_once( 'inc/rebuild-location-cpt.php' );
// include_once( 'inc/rebuild-site-category-taxonomy.php' );
// include_once( 'inc/rebuild-event-category-taxonomy.php' );
// include_once( 'inc/rebuild-event-tag-taxonomy.php' );
// include_once( 'inc/rebuild-exhibition-category-taxonomy.php' );
include_once( 'inc/rebuild-rewrite-rules.php' );
include_once( 'inc/rebuild-custom-fields.php' );
include_once( 'inc/rebuild-custom-columns.php' );
include_once( 'inc/rebuild-pre-get-query.php' );
include_once( 'inc/rebuild-filters.php' );


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