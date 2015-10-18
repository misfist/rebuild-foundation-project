<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CAC_SC_URL', plugin_dir_url( __FILE__ ) );
define( 'CAC_SC_DIR', plugin_dir_path( __FILE__ ) );

// only run plugin in the admin interface
if ( ! is_admin() ) {
	return false;
}

/**
 * Addon class
 *
 * @since 1.0
 */
class CAC_Addon_Sortable {

	/**
	 * @since 1.0
	 */
	function __construct() {

		// styling & scripts
		add_action( "admin_print_styles-settings_page_codepress-admin-columns", array( $this, 'scripts' ) );

		// add column properties
		add_filter( 'cac/column/properties', array( $this, 'set_column_default_properties' ) );

		// add column options
		add_filter( 'cac/column/default_options', array( $this, 'set_column_default_options' ) );

		// add setting field
		add_action( 'cac/column/settings_after', array( $this, 'add_settings_field' ), 9 );

		// add setting sort indicator
		add_action( 'cac/column/settings_meta', array( $this, 'add_label_sort_indicator' ), 9 );

		// init addon
		add_action( 'cac/loaded', array( $this, 'init_addon_sortables' ) );

		// add general settings
		add_action( 'cac/settings/general', array( $this, 'add_settings' ) );
	}

	/**
	 * @since 1.0
	 */
	public function add_settings( $options ) {
		?>
			<p>
				<label for="show_all_results">
					<input name="cpac_general_options[show_all_results]" id="show_all_results" type="checkbox" value="1" <?php checked( isset( $options['show_all_results'] ) ? $options['show_all_results'] : '', '1' ); ?>>
					<?php _e( 'Show all results when sorting. Default is <code>off</code>.', 'codepress-admin-columns' ); ?>
				</label>
			</p>
		<?php
	}

	/**
	 * Add Addon to Admin Columns list
	 *
	 * @since 1.0
	 */
	public function add_addon( $addons ) {
		$addons['cac-sortable'] = __( 'Sortable add-on', 'codepress-admin-columns' );

		return $addons;
	}

	/**
	 * @since 1.0
	 */
	public function scripts() {

		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'cpac-settings', 'codepress-admin-columns' ) ) ) {
			wp_enqueue_style( 'cac-addon-sortable-columns-css', CAC_SC_URL . 'assets/css/sortable.css', array(), CAC_PRO_VERSION, 'all' );
		}
	}

	/**
	 * @since 1.0
	 */
	function set_column_default_properties( $properties ) {

		if ( ! isset( $properties['is_sortable'] ) ) {
			$properties['is_sortable'] = false;
		}

		return $properties;
	}

	/**
	 * @since 1.0
	 */
	function set_column_default_options( $options ) {

		if ( ! isset( $options['sort'] ) ) {
			$options['sort'] = 'off';
		}

		return $options;
	}

	/**
	 * @since 1.0
	 */
	function add_settings_field( $column ) {

		if ( ! $column->properties->is_sortable ) {
			return false;
		}

		$sort = isset( $column->options->sort ) ? $column->options->sort : '';
		?>
		<tr class="column_sorting">
			<?php $column->label_view( __( 'Enable sorting?', 'codepress-admin-columns' ), __( 'This will make the column support sorting.', 'codepress-admin-columns' ), 'sorting' ); ?>
			<td class="input" data-toggle-id="<?php $column->attr_id( 'sort' ); ?>">
				<label for="<?php $column->attr_id( 'sort' ); ?>-on">
					<input type="radio" value="on" name="<?php $column->attr_name( 'sort' ); ?>" id="<?php $column->attr_id( 'sort' ); ?>-on"<?php checked( $column->options->sort, 'on' ); ?> />
					<?php _e( 'Yes'); ?>
				</label>
				<label for="<?php $column->attr_id( 'sort' ); ?>-off">
					<input type="radio" value="off" name="<?php $column->attr_name( 'sort' ); ?>" id="<?php $column->attr_id( 'sort' ); ?>-off"<?php checked( $column->options->sort, '' ); ?><?php checked( $column->options->sort, 'off' ); ?> />
					<?php _e( 'No'); ?>
				</label>
			</td>
		</tr>

	<?php
	}

	/**
	 * Meta Label in the column header
	 *
	 * @since 1.0
	 */
	function add_label_sort_indicator( $column ) {

		if ( ! $column->properties->is_sortable ) {
			return false;
		}

		?>
		<span title="<?php echo esc_attr( __( 'sort', 'codepress-admin-columns' ) ); ?>" class="sorting <?php echo $column->options->sort; ?>" data-indicator-id="<?php $column->attr_id( 'sort' ); ?>"></span>
		<?php
	}

	/**
	 * Init Addons
	 *
	 * @since 1.0
	 */
	function init_addon_sortables( $cpac ) {

		// Abstract
		include_once 'classes/model.php';

		// Childs
		include_once 'classes/post.php';
		include_once 'classes/media.php';
		include_once 'classes/user.php';
		include_once 'classes/comment.php';
		include_once 'classes/link.php';

		// Posts
		foreach ( $cpac->get_post_types() as $post_type ) {
			if ( $storage_model = $cpac->get_storage_model( $post_type ) ) {
				new CAC_Sortable_Model_Post( $storage_model );
			}
		}

		// Media
		if ( $storage_model = $cpac->get_storage_model( 'wp-media' ) ) {
			new CAC_Sortable_Model_Media( $storage_model );
		}

		// User
		if ( $storage_model = $cpac->get_storage_model( 'wp-users' ) ) {
			new CAC_Sortable_Model_User( $storage_model );
		}

		// Comment
		if ( $storage_model = $cpac->get_storage_model( 'wp-comments' ) ) {
			new CAC_Sortable_Model_Comment( $storage_model );
		}

		// Link
		if ( $storage_model = $cpac->get_storage_model( 'wp-links' ) ) {
			new CAC_Sortable_Model_Link( $storage_model );
		}
	}
}

new CAC_Addon_Sortable();
