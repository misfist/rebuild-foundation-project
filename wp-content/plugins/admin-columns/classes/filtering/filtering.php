<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CAC_FC_URL', plugins_url( '', __FILE__ ) );
define( 'CAC_FC_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Addon class
 *
 * @since 1.0
 */
class CAC_Addon_Filtering {

	private $cpac;

	private $filtering_models;

	function __construct() {

		// init addon
		add_action( 'cac/loaded', array( $this, 'init_addon_filtering' ) );

		// Add column properties
		add_filter( 'cac/column/default_properties', array( $this, 'set_column_default_properties' ) );

		// Add column options
		add_filter( 'cac/column/default_options', array( $this, 'set_column_default_options' ) );

		// Add setting field
		add_action( 'cac/column/settings_after', array( $this, 'add_settings_field' ), 9 );

		// add setting filtering indicator
		add_action( 'cac/column/settings_meta', array( $this, 'add_label_filter_indicator' ), 9 );

		// styling & scripts
		add_action( "admin_print_styles-settings_page_codepress-admin-columns", array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_listings' ) );
	}

	/**
	 * @since 1.0
	 */
	public function scripts() {
		wp_enqueue_style( 'cac-addon-filtering-css', CAC_FC_URL . '/assets/css/filtering.min.css', array(), CAC_PRO_VERSION, 'all' );
	}

	/**
	 * @since 3.4.4
	 */
	public function scripts_listings() {

		if ( $storage_model = $this->cpac->get_current_storage_model() ) {

			wp_register_script( 'cac-addon-filtering-listings-js', CAC_FC_URL . '/assets/js/listings_screen.js', array( 'jquery' ), CAC_PRO_VERSION );
			wp_localize_script( 'cac-addon-filtering-listings-js', 'CAC_FC_Storage_Model', $storage_model->key );
			wp_enqueue_script( 'cac-addon-filtering-listings-js' );

			wp_enqueue_style( 'cac-addon-filtering-listings-css', CAC_FC_URL . '/assets/css/listings_screen.min.css', array(), CAC_PRO_VERSION, 'all' );
		}
	}

	/**
	 * @since 1.0
	 */
	public function set_column_default_properties( $properties ) {
		$properties['is_filterable'] = false;
		$properties['filterable_type'] = null;
		$properties['use_filter_operator'] = false;

		return $properties;
	}

	/**
	 * @since 1.0
	 */
	public function set_column_default_options( $options ) {
		$options['filter'] = 'off';
		$options['filter_type'] = '';
		$options['filter_operator'] = '';

		return $options;
	}

	/**
	 * @since 1.0
	 */
	public function add_settings_field( $column ) {

		if ( ! $column->properties->is_filterable ) {
			return false;
		}
		?>

		<tr class="column_filtering">
			<?php $column->label_view( __( 'Enable filtering?', 'codepress-admin-columns' ), __( 'This will make the column support filtering.', 'codepress-admin-columns' ), 'filter' ); ?>
			<td class="input" data-toggle-id="<?php $column->attr_id( 'filter' ); ?>">
				<label for="<?php $column->attr_id( 'filter' ); ?>-on">
					<input type="radio" value="on" name="<?php $column->attr_name( 'filter' ); ?>" id="<?php $column->attr_id( 'filter' ); ?>-on"<?php checked( $column->options->filter, 'on' ); ?>>
					<?php _e( 'Yes'); ?>
				</label>
				<label for="<?php $column->attr_id( 'filter' ); ?>-off">
					<input type="radio" value="off" name="<?php $column->attr_name( 'filter' ); ?>" id="<?php $column->attr_id( 'filter' ); ?>-off"<?php checked( $column->options->filter, '' ); ?><?php checked( $column->options->filter, 'off' ); ?>>
					<?php _e( 'No'); ?>
				</label>
			</td>
		</tr>


	<?php
		// Additional settings fields
		if ( isset( $column->properties->filterable_type ) && 'date' == $column->properties->filterable_type ){
			$column->display_field_select(
				'filter_type',
				__( 'Date filter type', 'codepress-admin-columns' ),
				array(
					'' => __( 'Daily' ),
					'monthly' => __( 'Monthly' ),
					'yearly' => __( 'Yearly' ),
				)
			);
		}

		if ( isset( $column->properties->use_filter_operator ) && $column->properties->use_filter_operator ) {
			$column->display_field_select(
				'filter_operator',
				__( 'Filter by:', 'codepress-admin-columns' ),
				$column->get_filter_operators(),
				__( "This will allow you to set the filter's operator.", 'codepress-admin-columns' ),
				'filter'
			);
		}
	}

	/**
	 * @since 1.0
	 */
	public function add_label_filter_indicator( $column ) {
		if ( $column->properties->is_filterable ) : ?>
		<span title="<?php esc_attr_e( 'filter', 'codepress-admin-columns' ); ?>" class="filtering <?php echo $column->options->filter; ?>"  data-indicator-id="<?php $column->attr_id( 'filter' ); ?>"></span>
		<?php
		endif;
	}

	/**
	 * Init Addons
	 *
	 * @since 1.0
	 */
	public function init_addon_filtering( $cpac ) {

		$this->cpac = $cpac;

		// Abstract
		include_once 'classes/model.php';

		// Childs
		include_once 'classes/media.php';
		include_once 'classes/post.php';
		include_once 'classes/user.php';
		include_once 'classes/comment.php';

		// Posts
		foreach ( $this->cpac->get_post_types() as $post_type ) {
			if ( $storage_model = $cpac->get_storage_model( $post_type ) ) {
				new CAC_Filtering_Model_Post( $storage_model );
			}
		}

		// User
		if ( $storage_model = $this->cpac->get_storage_model( 'wp-users' ) ) {
			new CAC_Filtering_Model_User( $storage_model );
		}

		// Media
		if ( $storage_model = $this->cpac->get_storage_model( 'wp-media' ) ) {
			new CAC_Filtering_Model_Media( $storage_model );
		}

		// Comment
		if ( $storage_model = $this->cpac->get_storage_model( 'wp-comments' ) ) {
			new CAC_Filtering_Model_Comment( $storage_model );
		}
	}

	/**
	 * @since 3.6
	 */
	public function get_filtering_model( $storage_model ) {
		return isset( $this->filtering_models[ $storage_model ] ) ? $this->filtering_models[ $storage_model ] : false;
	}

	/**
	 * Check whether the plugin is loaded
	 * Loading is done when the cpac property is set, which usually occurs on the cac/loaded action
	 *
	 * @since 3.0.8.4
	 *
	 * @return bool Whether Admin Columns is loaded
	 */
	public function is_loaded() {

		return ( ! empty( $this->cpac ) );
	}
}
new CAC_Addon_Filtering;