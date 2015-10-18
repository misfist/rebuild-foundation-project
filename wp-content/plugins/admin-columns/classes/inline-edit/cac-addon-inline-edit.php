<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Addon information
define( 'CAC_INLINEEDIT_URL', plugin_dir_url( __FILE__ ) );
define( 'CAC_INLINEEDIT_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Main Inline Edit Addon plugin class
 *
 * @since 1.0
 */
class CACIE_Addon_InlineEdit {

	/**
	 * Admin Columns main plugin class instance
	 *
	 * @since 1.0
	 * @var CPAC
	 */
	public $cpac;

	/**
	 * Main plugin directory
	 *
	 * @since 1.0
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	function __construct() {

		$this->plugin_basename = plugin_basename( __FILE__ );

		// Admin Columns-dependent setup
		add_action( 'cac/loaded', array( $this, 'init' ) );

		// add column properties for column types
		add_filter( 'cac/column/properties', array( $this, 'set_column_default_properties' ) );

		// add column options
		add_filter( 'cac/column/default_options', array( $this, 'set_column_default_options' ) );

		// add setting field to column editing box
		add_action( 'cac/column/settings_after', array( $this, 'add_settings_field' ), 10 );

		// add setting editing indicator
		add_action( 'cac/column/settings_meta', array( $this, 'add_label_edit_indicator' ), 10 );

		// add general settings
		add_action( 'cac/settings/general', array( $this, 'add_settings' ) );

		// Add notifications to the plugin screen
		add_action( 'after_plugin_row_' . $this->plugin_basename, array( $this, 'display_plugin_row_notices' ), 11 );
	}

	/**
	 * Init
	 *
	 * @since 1.0
	 */
	public function init( $cpac ) {

		$this->cpac = $cpac;

		// load files
		require_once 'inc/roles.php';
		require_once 'inc/arrays.php';
		require_once 'inc/acf-fieldoptions.php';
		require_once 'inc/woocommerce.php';

		// init addon
		$this->init_addon();

		// scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * @since 3.1.2
	 */
	public function add_settings( $options ) {

		$is_custom_field_editable = isset( $options['custom_field_editable'] ) ? $options['custom_field_editable'] : '';
		?>
			<p>
				<label for="custom_field_editable">
					<input name="cpac_general_options[custom_field_editable]" id="custom_field_editable" type="checkbox" value="1" <?php checked( $is_custom_field_editable, '1' ); ?>>
					<?php _e( 'Enable inline editing for Custom Fields. Default is <code>off</code>', 'codepress-admin-columns' ); ?>
				</label>
				<a href="javascript:;" class="cpac-pointer" rel="acp-custom_field_editable" data-pos="right"><?php _e( 'Instructions', 'codepress-admin-columns' ); ?></a>
			</p>
			<div id="acp-custom_field_editable" style="display:none;">
				<h3><?php _e( 'Notice', 'codepress-admin-columns' ); ?></h3>
				<p>
					<?php _e( 'Inline edit will display all the raw values in an editable text field.', 'codepress-admin-columns' ); ?>
				</p>
				<p>
					<?php _e( 'Except for Checkmark, Media Library, Post Title and Username.', 'codepress-admin-columns' ); ?>
				</p>
				<p>
					<?php printf( __( "Please read <a href='%s'>our documentation</a> if you plan to use these fields.", 'codepress-admin-columns' ), $this->cpac->settings()->get_url( 'documentation') . 'faq/enable-inline-editing-custom-fields/' ); ?>
				</p>
			</div>
		<?php
	}

	/**
	 * Whether this request is a columns screen (i.e. a content overview page) for which inline edit is available
	 *
	 * @since 1.0
	 *
     * @return bool Returns true if the current screen is a columns screen, false otherwise
	 */
	public function is_columns_screen() {

		global $pagenow;

		$columns_screen = in_array( $pagenow, array( 'edit.php', 'users.php', 'upload.php', 'edit-tags.php', 'edit-comments.php' ) );

		/**
		 * Filter whether the current screen is a columns screen for which inline edit is available (i.e. a content overview page)
		 * Useful for advanced used with custom content overview pages
		 *
		 * @since 1.0
		 *
		 * @param bool $columns_screen Whether the current request is a columns screen
		 */
		$columns_screen = apply_filters( 'cac/inline-edit/is_columns_screen', $columns_screen );

		return $columns_screen;
	}

	/**
	 * Load script translations
	 *
	 * @since 1.0
	 */
	public function scripts_locale() {
		$locale = substr( get_locale(), 0, 2 );

		// Select 2 translations
		if ( file_exists(  CAC_INLINEEDIT_DIR . 'library/select2/select2_locale_' . $locale . '.js' ) ) {
			wp_register_script( 'select2-locale' , CAC_INLINEEDIT_URL . 'library/select2/select2_locale_' . $locale . '.js', array( 'jquery' ), CAC_PRO_VERSION );
			wp_enqueue_script( 'select2-locale' );
		}
	}

	/**
	 * Register and enqueue scripts and styles
	 *
	 * @since 1.0
	 */
	public function scripts( $hook ) {

		// Libraries
		wp_register_script( 'bootstrap', CAC_INLINEEDIT_URL . 'library/bootstrap/bootstrap.min.js', array( 'jquery' ), CAC_PRO_VERSION );
		wp_register_script( 'select2', CAC_INLINEEDIT_URL . 'library/select2/select2.min.js', array( 'jquery' ), CAC_PRO_VERSION );
		wp_register_style( 'select2-css', CAC_INLINEEDIT_URL . 'library/select2/select2.css', array(), CAC_PRO_VERSION );
		wp_register_style( 'select2-bootstrap', CAC_INLINEEDIT_URL . 'library/select2/select2-bootstrap.css', array(), CAC_PRO_VERSION );
		wp_register_script( 'bootstrap-editable', CAC_INLINEEDIT_URL . 'library/bootstrap-editable/js/bootstrap-editable.js', array( 'jquery', 'bootstrap' ), CAC_PRO_VERSION );
		wp_register_style( 'bootstrap-editable', CAC_INLINEEDIT_URL . 'library/bootstrap-editable/css/bootstrap-editable.css', array(), CAC_PRO_VERSION );
		wp_register_script( 'moment', CAC_INLINEEDIT_URL . 'library/moment/moment.min.2.4.0.js', array( 'jquery' ), CAC_PRO_VERSION );

		// Core
		wp_register_script( 'cacie-xeditable-input-wc-price', CAC_INLINEEDIT_URL . 'assets/js/xeditable/input/wc-price.js', array( 'jquery', 'bootstrap-editable' ), CAC_PRO_VERSION );
		wp_register_script( 'cacie-xeditable-input-wc-stock', CAC_INLINEEDIT_URL . 'assets/js/xeditable/input/wc-stock.js', array( 'jquery', 'bootstrap-editable' ), CAC_PRO_VERSION );
		wp_register_script( 'cacie-xeditable-input-wc-usage', CAC_INLINEEDIT_URL . 'assets/js/xeditable/input/wc-usage.js', array( 'jquery', 'bootstrap-editable' ), CAC_PRO_VERSION );
		wp_register_script( 'cacie-xeditable-input-dimensions', CAC_INLINEEDIT_URL . 'assets/js/xeditable/input/dimensions.js', array( 'jquery', 'bootstrap-editable' ), CAC_PRO_VERSION );
		wp_register_script( 'cacie-admin-edit', CAC_INLINEEDIT_URL . 'assets/js/admin-edit.js', array( 'jquery', 'bootstrap-editable', 'select2', 'moment', 'cacie-xeditable-input-wc-price', 'cacie-xeditable-input-wc-stock', 'cacie-xeditable-input-wc-usage', 'cacie-xeditable-input-dimensions' ), CAC_PRO_VERSION );
		wp_register_style( 'cacie-admin-edit', CAC_INLINEEDIT_URL . 'assets/css/admin-edit.css', array(), CAC_PRO_VERSION );
		wp_register_script( 'cacie-admin-options-admincolumns', CAC_INLINEEDIT_URL . 'assets/js/admin-options-admincolumns.js', array( 'jquery' ), CAC_PRO_VERSION );
		wp_register_style( 'cacie-admin-options-admincolumns', CAC_INLINEEDIT_URL . 'assets/css/admin-options-admincolumns.css', array(), CAC_PRO_VERSION );

		// Column screen
		if ( $this->is_columns_screen() ) {
			wp_enqueue_script( 'jquery' );

			// Libraries CSS
			wp_enqueue_style( 'select2-css' );
			wp_enqueue_style( 'select2-bootstrap' );
			wp_enqueue_style( 'bootstrap-editable' );

			// Core
			wp_enqueue_script( 'cacie-admin-edit' );
			wp_enqueue_style( 'cacie-admin-edit' );

			// Translations
			wp_localize_script( 'cacie-admin-edit', 'qie_i18n', array(
				'select_author'	=> __( 'Select author', 'codepress-admin-columns' ),
				'edit'			=> __( 'Edit' ),
				'redo'			=> __( 'Redo', 'codepress-admin-columns' ),
				'undo'			=> __( 'Undo', 'codepress-admin-columns' ),
				'delete'		=> __( 'Delete', 'codepress-admin-columns' ),
				'download'		=> __( 'Download', 'codepress-admin-columns' ),
				'errors'	 	=> array(
					'field_required' => __( 'This field is required.', 'codepress-admin-columns' ),
					'invalid_float' => __( 'Please enter a valid float value.', 'codepress-admin-columns' ),
					'invalid_floats' => __( 'Please enter valid float values.', 'codepress-admin-columns' )
				),
				'inline_edit' => __( 'Inline Edit', 'codepress-admin-columns' ),
			) );

			// WP Mediapicker
			wp_enqueue_media();

			// WP Colorpicker
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );

			// Translations
			$this->scripts_locale();
		}

		// Column settings
		if ( $this->cpac->is_settings_screen() ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'cacie-date-save-format-field' );
			wp_enqueue_script( 'cacie-admin-options-admincolumns' );
			wp_enqueue_style( 'cacie-admin-options-admincolumns' );
		}
	}

	/**
	 * Basic setup for this add-on
	 *
	 * @since 1.0
	 */
	public function init_addon() {

		// Abstract
		include_once 'classes/model.php';

		// Posts
		include_once 'classes/post.php';
		if ( $post_types = $this->cpac->get_post_types() ) {
			foreach ( $post_types as $post_type ) {
				if ( $storage_model = $this->cpac->get_storage_model( $post_type ) ) {
					new CACIE_Editable_Model_Post( $storage_model );
				}
			}
		}

		// Users
		include_once 'classes/user.php';
		if ( $storage_model = $this->cpac->get_storage_model( 'wp-users' ) ) {
			new CACIE_Editable_Model_User( $storage_model );
		}

		// Media
		include_once 'classes/media.php';
		if ( $storage_model = $this->cpac->get_storage_model( 'wp-media' ) ) {
			new CACIE_Editable_Model_Media( $storage_model );
		}

		// Taxonomy
		include_once 'classes/taxonomy.php';
		if ( $taxonomies = $this->cpac->get_taxonomies() ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( $storage_model = $this->cpac->get_storage_model( 'wp-taxonomy_' . $taxonomy ) ) {
					new CACIE_Editable_Model_Taxonomy( $storage_model );
				}
			}
		}

		// Comment
		include_once 'classes/comment.php';
		if ( $storage_model = $this->cpac->get_storage_model( 'wp-comments' ) ) {
			new CACIE_Editable_Model_Comment( $storage_model );
		}
	}

	/**
	 * Add column type setting defaults
	 *
	 * @since 1.0
	 */
	public function set_column_default_properties( $properties ) {

		if ( ! isset( $properties['is_editable'] ) ) {
			$properties['is_editable'] = false;
		}

		return $properties;
	}

	/**
	 * Add option defaults for columns
	 *
	 * @since 1.0
	 */
	public function set_column_default_options( $options ) {

		$options['edit'] = 'off';
		$options['date_save_format'] = '';
		$options['enable_term_creation'] = 'off';

		return $options;
	}

	/**
	 * Add settings fields to column edit box
	 *
	 * @since 1.0
	 */
	public function add_settings_field( $column ) {

		if ( ! $column->properties->is_editable ) {
			return false;
        }

		?>
		<tr class="column_editing">
			<?php $column->label_view( __( 'Enable editing?', 'codepress-admin-columns' ), __( 'This will make the column support inline editing.', 'codepress-admin-columns' ), 'editing' ); ?>
			<td class="input" data-toggle-id="<?php $column->attr_id( 'edit' ); ?>">
				<label for="<?php $column->attr_id( 'edit' ); ?>-on">
					<input type="radio" value="on" name="<?php $column->attr_name( 'edit' ); ?>" id="<?php $column->attr_id( 'edit' ); ?>-on"<?php checked( $column->options->edit, 'on' ); ?> />
					<?php _e( 'Yes'); ?>
				</label>
				<label for="<?php $column->attr_id( 'edit' ); ?>-off">
					<input type="radio" value="off" name="<?php $column->attr_name( 'edit' ); ?>" id="<?php $column->attr_id( 'edit' ); ?>-off"<?php checked( $column->options->edit, '' ); ?><?php checked( $column->options->edit, 'off' ); ?> />
					<?php _e( 'No'); ?>
				</label>
			</td>
		</tr>
		<?php

		// Additional settings fields
		switch ( $column->properties->type ) {
			case 'date':
				$this->add_settings_field_date_save_format( $column );
				break;
			case 'column-meta':
				if ( isset( $column->options->field_type ) && in_array( $column->options->field_type, array( 'date' )  ) ) {
					$this->add_settings_field_date_save_format( $column );
				}
				break;
			case 'column-taxonomy':
			case 'categories':
			case 'tags':
				$column->display_field_radio(
					'enable_term_creation',
					__( 'Allow creating new terms', 'codepress-admin-columns' ),
					array(
						'on' => __( 'Yes' ),
						'off' => __( 'No' )
					),
					'', // description
					'edit' // toggle_id
				);
				break;
		}
	}

	/**
	 * Add date save format to column edit box
	 *
	 * @since 1.0
	 *
	 * @param CPAC_Column Column object instance
	 */
	public function add_settings_field_date_save_format( $column ) {

		// Date save format: settings
		$field_key		= 'date_save_format';
		$label			= __( 'Date save format', 'codepress-admin-columns' );
		$description	= __( 'Fill in the date format as it is stored. This is used to accurately determine the date.', 'codepress-admin-columns' );

		// store format
		$default_format   = 'YYYY-MM-DD HH:mm:SS';
		$date_save_format = isset( $column->options->date_save_format ) ? $column->options->date_save_format : $default_format;
		?>
		<tr class="column_<?php echo $field_key; ?> <?php echo 'on' == $column->options->edit ? '' : 'hide'; ?>" data-additional-option-id="<?php echo $column->attr_id( 'edit' ); ?>">
			<?php $column->label_view( $label, $description, $field_key ); ?>
			<td class="input">
				<input type="text" name="<?php $column->attr_name( $field_key ); ?>" id="<?php $column->attr_id( $field_key ); ?>" value="<?php echo $date_save_format; ?>" placeholder="<?php _e( 'Fill in a date save format', 'codepress-admin-columns' ); ?>"/>
				<p class="description">
					<?php printf( __( 'Defaults to: %s.', 'codepress-admin-columns' ), $default_format ); ?>
					<a target='_blank' href='http://momentjs.com/docs/#/displaying/format/'><?php _e( 'See all available formats', 'codepress-admin-columns' ); ?>.</a>
				</p>
			</td>
		</tr>

		<?php
	}

	/**
	 * Label in column admin screen column header
	 *
	 * @since 1.0
	 */
	public function add_label_edit_indicator( $column ) {
		if ( $column->properties->is_editable ) : ?>
		<span class="editing <?php echo $column->options->edit; ?>" data-indicator-id="<?php $column->attr_id( 'edit' ); ?>"></span>
		<?php endif;
	}

	/**
	 * Shows a message below the plugin on the plugins page
	 *
	 * @since 1.0
	 */
	public function display_plugin_row_notices() {

		if ( $this->is_cpac_enabled() ) {
			return;
		}
		?>
		<tr class="plugin-update-tr">
			<td colspan="3" class="plugin-update">
				<div class="update-message">
					<?php printf( __( 'The Inline Edit add-on is enabled but not effective. It requires %s in order to work.', 'codepress-admin-columns' ), '<a href="' . admin_url( 'plugin-install.php' ) . '?tab=search&s=Admin+Columns&plugin-search-input=Search+Plugins' . '">Admin Columns</a>' ); ?>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Whether the main plugin is enabled
	 *
	 * @since 1.0
	 *
	 * @return bool Returns true if the main Admin Columns is enabled, false otherwise
	 */
	public function is_cpac_enabled() {

		return class_exists( 'CPAC', false );
	}
}

new CACIE_Addon_InlineEdit();
