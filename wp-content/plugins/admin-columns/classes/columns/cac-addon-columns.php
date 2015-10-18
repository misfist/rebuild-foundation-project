<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CAC_ADDON_COLUMNS_DIR', plugin_dir_path( __FILE__ ) );

/**
 * @since 3.6
 */
class CACIE_Addon_Columns {

	public function __construct() {
		add_filter( 'cac/columns/custom/type=post', array( $this, 'set_post_columns' ), 10, 2 );
	}

	public function set_post_columns( $columns, $storage_model ) {
		$columns['CPAC_Column_Post_Child_Pages'] = CAC_ADDON_COLUMNS_DIR . 'post/child-pages.php';

		return $columns;
	}
}

new CACIE_Addon_Columns();
