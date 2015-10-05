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
 * Description:       Adds custom post types and taxonomy for sites and exhibitions
 * Version:           1.0.0
 * Author:            Pea
 * Author URI:        http://misfist.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rebuild-foundation-cpt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Required files for registering the post type and taxonomies.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-sites-post-type.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-exhibitions-post-type.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/interface-gamajo-registerable.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-gamajo-post-type.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-gamajo-taxonomy.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-sites-post-type-post-type.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-sites-post-type-taxonomy-category.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-sites-post-type-taxonomy-tag.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-sites-post-type-registrations.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-exhibitions-post-type-post-type.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-exhibitions-post-type-taxonomy-category.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-exhibitions-post-type-taxonomy-tag.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-exhibitions-post-type-registrations.php';

// Required files for custom fields.

if ( file_exists( dirname( __FILE__ ) . '/vendor/cmb2/init.php' ) ) {

    // Custom metabox plugin
    require_once dirname( __FILE__ ) . '/vendor/cmb2/init.php';

    // Custom address field
    require_once dirname( __FILE__ ) . '/includes/custom-field-types/address-field-type.php';

    // Custom metabox date range field
    require_once dirname( __FILE__ ) . '/includes/custom-field-types/wds-cmb2-date-range-field.php';

    require_once dirname( __FILE__ ) . '/includes/custom-fields/rebuild_sites-custom-fields.php';
    require_once dirname( __FILE__ ) . '/includes/custom-fields/rebuild_exhibitions-custom-fields.php';
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/cmb2-taxonomy/init.php' ) ) {

    // Custom metabox taxonomy extension
    require_once dirname( __FILE__ ) . '/vendor/cmb2-taxonomy/init.php';

}


// Instantiate registration class, so we can add it as a dependency to main plugin class.
$rebuild_sites_post_type_registrations = new Rebuild_Sites_Post_Type_Registrations;
$rebuild_exhibitions_post_type_registrations = new Rebuild_Exhibitions_Post_Type_Registrations;

// Instantiate main plugin file, so activation callback does not need to be static.
$rebuild_sites_post_type = new Rebuild_Sites_Post_Type( $rebuild_sites_post_type_registrations );
$rebuild_exhibitions_post_type = new Rebuild_Exhibitions_Post_Type( $rebuild_exhibitions_post_type_registrations );

// Register callback that is fired when the plugin is activated.
register_activation_hook( __FILE__, array( $rebuild_sites_post_type, 'activate' ) );
register_activation_hook( __FILE__, array( $rebuild_exhibitions_post_type, 'activate' ) );

// Initialise registrations for post-activation requests.
$rebuild_sites_post_type_registrations->init();
$rebuild_exhibitions_post_type_registrations->init();


add_action( 'init', 'rebuild_sites_init', 100 );
add_action( 'init', 'rebuild_exhibitions_init', 100 );
/**
 * Adds styling to the dashboard for the post type and adds portfolio posts
 * to the "At a Glance" metabox.
 *
 * Adds custom taxonomy body classes to portfolio posts on the front end.
 *
 * @since 0.8.3
 */
function rebuild_sites_init() {
    if ( is_admin() ) {
        global $rebuild_sites_post_type_admin, $rebuild_sites_post_type_registrations;
        // Loads for users viewing the WordPress dashboard
        if ( ! class_exists( 'Gamajo_Dashboard_Glancer' ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-gamajo-dashboard-glancer.php';  // WP 3.8
        }
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-sites-post-type-admin.php';
        $rebuild_sites_post_type_admin = new Rebuild_Sites_Post_Type_Admin( $rebuild_sites_post_type_registrations );
        $rebuild_sites_post_type_admin->init();
    } else {
        // Loads for users viewing the front end
        if ( apply_filters( 'rebuild_sites_post_type_add_taxonomy_terms_classes', true ) ) {
            if ( ! class_exists( 'Gamajo_Single_Entry_Term_Body_Classes' ) ) {
                require_once plugin_dir_path( __FILE__ ) . 'includes/class-gamajo-single-entry-term-body-classes.php';
            }
            $rebuild_sites_post_type_body_classes = new Gamajo_Single_Entry_Term_Body_Classes;
            $rebuild_sites_post_type_body_classes->init( 'rebuild-sites' );
        }
    }
}

/**
 * Adds styling to the dashboard for the post type and adds portfolio posts
 * to the "At a Glance" metabox.
 *
 * Adds custom taxonomy body classes to portfolio posts on the front end.
 *
 * @since 0.8.3
 */
function rebuild_exhibitions_init() {
    if ( is_admin() ) {
        global $rebuild_exhibitions_post_type_admin, $rebuild_exhibitions_post_type_registrations;
        // Loads for users viewing the WordPress dashboard
        if ( ! class_exists( 'Gamajo_Dashboard_Glancer' ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-gamajo-dashboard-glancer.php';  // WP 3.8
        }
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-rebuild-exhibitions-post-type-admin.php';
        $rebuild_exhibitions_post_type_admin = new Rebuild_Exhibitions_Post_Type_Admin( $rebuild_exhibitions_post_type_registrations );
        $rebuild_exhibitions_post_type_admin->init();
    } else {
        // Loads for users viewing the front end
        if ( apply_filters( 'rebuild_exhibitions_post_type_add_taxonomy_terms_classes', true ) ) {
            if ( ! class_exists( 'Gamajo_Single_Entry_Term_Body_Classes' ) ) {
                require_once plugin_dir_path( __FILE__ ) . 'includes/class-gamajo-single-entry-term-body-classes.php';
            }
            $rebuild_exhibitions_post_type_body_classes = new Gamajo_Single_Entry_Term_Body_Classes;
            $rebuild_exhibitions_post_type_body_classes->init( 'rebuild-exhibitions' );
        }
    }
}
