<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CAC_EI_URL', plugin_dir_url( __FILE__ ) );
define( 'CAC_EI_DIR', plugin_dir_path( __FILE__ ) );

// only run plugin in the admin interface
if ( ! is_admin() ) {
	return false;
}

/**
 * Loads main plugin (CPAC) into the constructor
 *
 * @since 1.0
 */
function init_cpac_export_import( $cpac ) {

	require_once CAC_EI_DIR . 'classes/export_import.php';
	new CAC_Export_Import( $cpac );
}
add_action( 'cac/loaded', 'init_cpac_export_import' );

