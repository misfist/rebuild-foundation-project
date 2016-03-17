<?php

/**
 * Base newsletter class
 *
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class Give_Newsletter {

	/**
	 * The functions in this section must be overwritten by the Add-on using this class
	 */

	/**
	 * Defines the default label shown on checkout
	 *
	 * Other things can be done here if necessary, such as additional filters or actions
	 */
	public function init() {


	}

	/**
	 * Retrieve the newsletter lists
	 *
	 * Must return an array like this:
	 *   array(
	 *     'some_id'  => 'value1',
	 *     'other_id' => 'value2'
	 *   )
	 */
	public function get_lists() {
		return (array) $this->lists;
	}

	/**
	 * Retrieve groups for a list
	 *
	 * @param  string $list_id List id for which groupings should be returned
	 *
	 * @return array  $groups_data Data about the groups
	 */
	public function get_groupings( $list_id = '' ) {
		return array();
	}

	/**
	 * Determines if the signup checkbox should be shown on checkout
	 */
	public function show_checkout_signup() {
		return true;
	}

	/**
	 * Subscribe a donor to a list
	 *
	 * $user_info is an array containing the user ID, email, first name, and last name
	 *
	 * $list_id is the list ID the user should be subscribed to. If it is false, sign the user
	 * up for the default list defined in settings
	 *
	 */
	public function subscribe_email( $user_info = array(), $list_id = false ) {
		return true;
	}

	/**
	 * Register the plugin settings
	 *
	 */
	public function settings( $settings ) {
		return $settings;
	}


	/**
	 * The properties and functions in this section may be overwritten by the Add-on using this class
	 * but are not mandatory
	 */

	/**
	 * The ID for this newsletter Add-on, such as 'mailchimp'
	 */
	public $id;

	/**
	 * The label for the Add-on, probably just shown as the title of the metabox
	 */
	public $label;

	/**
	 * Newsletter lists retrieved from the API
	 */
	public $lists;

	/**
	 * Text shown on the checkout, if none is set in the settings
	 */
	public $checkout_label;

	/**
	 * Give Options
	 */
	public $give_options;

	/**
	 * Class constructor
	 */
	public function __construct( $_id = 'newsletter', $_label = 'Newsletter' ) {

		$this->id    = $_id;
		$this->label = $_label;

		add_action( 'init', array( $this, 'textdomain' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );

		add_filter( 'give_settings_addons', array( $this, 'settings' ) );
		add_action( 'give_purchase_form_before_submit', array( $this, 'checkout_fields' ), 100, 1 );
		//		add_action( 'give_checkout_before_gateway', array( $this, 'checkout_signup' ), 10, 3 );
		add_action( 'give_complete_form_donation', array( $this, 'completed_donation_signup' ), 10, 3 );

		//Get it started
		add_action( 'init', array( $this, 'init' ) );


	}


	/**
	 * Load the plugin's textdomain
	 */
	public function textdomain() {
		// Load the translations
		load_plugin_textdomain( 'give_mailchimp', false, GIVE_MAILCHIMP_PATH . '/languages/' );
	}

	/**
	 * Output the signup checkbox on the checkout screen, if enabled
	 *
	 * @param int $form_id
	 */
	public function checkout_fields( $form_id ) {

		$enable_mc_form  = get_post_meta( $form_id, '_give_mailchimp_enable', true );
		$disable_mc_form = get_post_meta( $form_id, '_give_mailchimp_disable', true );

		//Check disable vars to see if this form should have the MC Opt-in field
		if ( ! $this->show_checkout_signup() && $enable_mc_form !== 'true' || $disable_mc_form === 'true' ) {
			return;
		}

		$this->give_options    = give_get_settings();
		$custom_checkout_label = get_post_meta( $form_id, '_give_mailchimp_custom_label', true );

		//What's the label gonna be?
		if ( ! empty( $custom_checkout_label ) ) {
			$this->checkout_label = trim( $custom_checkout_label );
		} elseif ( ! empty( $this->give_options['give_mailchimp_label'] ) ) {
			$this->checkout_label = trim( $this->give_options['give_mailchimp_label'] );
		} else {
			$this->checkout_label = __( 'Subscribe to our newsletter', 'give_mailchimp' );
		}

		ob_start(); ?>
		<fieldset id="give_<?php echo $this->id; ?>" class="give-mc-fieldset">
			<p>
				<input name="give_<?php echo $this->id; ?>_signup" id="give_<?php echo $this->id; ?>_signup" type="checkbox" checked="checked" />
				<label for="give_<?php echo $this->id; ?>_signup"><?php echo $this->checkout_label; ?></label>
			</p>
		</fieldset>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Checkout Signup
	 *
	 * @description Check if a customer needs to be subscribed at checkout
	 *
	 * @param $posted
	 * @param $user_info
	 * @param $valid_data
	 */
	public function checkout_signup( $posted, $user_info, $valid_data ) {

		// Check for global newsletter
		if ( isset( $posted[ 'give_' . $this->id . '_signup' ] ) ) {

			$this->subscribe_email( $user_info );

		}

	}

	/**
	 * Complete Donation Sign up
	 *
	 * @description  Check if a customer needs to be subscribed on completed donation on a specific form
	 *
	 * @param int $form_id
	 * @param int $payment_id
	 * @param     $payment_meta
	 */
	public function completed_donation_signup( $form_id = 0, $payment_id = 0, $payment_meta ) {

		$user_info  = give_get_payment_meta_user_info( $payment_id );
		$form_lists = get_post_meta( $form_id, '_give_' . $this->id, true );

		//check to see if the user has elected to subscribe
		if ( ! isset( $_POST['give_mailchimp_signup'] ) || $_POST['give_mailchimp_signup'] !== 'on' ) {
			return;
		}

		//Check if $form_lists is set
		if ( empty( $form_lists ) ) {
			//Not set so use global list
			$form_lists = (array) give_get_option( 'give_mailchimp_list' );
		}

		$lists = array_unique( $form_lists );

		foreach ( $lists as $list ) {

			$this->subscribe_email( $user_info, $list );

		}

	}

	/**
	 * Register the metabox on the 'give_forms' post type
	 */
	public function add_metabox() {

		if ( current_user_can( 'edit_give_forms', get_the_ID() ) ) {
			add_meta_box( 'give_' . $this->id, $this->label, array( $this, 'render_metabox' ), 'give_forms', 'side' );
		}

	}

	/**
	 * Display the metabox, which is a list of newsletter lists
	 */
	public function render_metabox() {

		global $post;
		$this->give_options = give_get_settings();

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'give_mailchimp_meta_box', 'give_mailchimp_meta_box_nonce' );

		//Using a custom label?
		$custom_label = get_post_meta( $post->ID, '_give_mailchimp_custom_label', true );

		//Global label
		$global_label = isset( $this->give_options['give_mailchimp_label'] ) ? $this->give_options['give_mailchimp_label'] : __( 'Signup for the newsletter', 'give_mailchimp' );;

		//Globally enabled option
		$globally_enabled = give_get_option( 'give_mailchimp_show_checkout_signup' );
		$enable_option    = get_post_meta( $post->ID, '_give_mailchimp_enable', true );
		$disable_option   = get_post_meta( $post->ID, '_give_mailchimp_disable', true );

		if ( $globally_enabled == 'on' ) {

			//Output option to DISABLE MC for this form
			echo '<p style="margin: 1em 0 0;"><label>';
			echo '<input type="checkbox" name="_give_mailchimp_disable" class="give-mc-disable"  value="true" ' . checked( 'true', $disable_option, false ) . '>';
			echo '&nbsp;' . __( 'Disable MailChimp Opt-in' );
			echo '</label></p>';

		} else {

			//Output option to ENABLE MC for this form
			echo '<p style="margin: 1em 0 0;"><label>';
			echo '<input type="checkbox" name="_give_mailchimp_enable" class="give-mc-enable" value="true" ' . checked( 'true', $enable_option, false ) . '>';
			echo '&nbsp;' . __( 'Enable MailChimp Opt-in' );
			echo '</label></p>';
		}

		// Display the form, using the current value.
		echo '<div class="give-mailchimp-field-wrap" ' . ( $globally_enabled == false && empty( $enable_option ) ? "style='display:none;'" : '' ) . ' >';
		echo '<p>';
		echo '<label for="_give_mailchimp_custom_label" style="font-weight:bold;">' . __( 'Custom Label', 'give_mailchimp' ) . '</label> ';
		echo '<span class="cmb2-metabox-description" style="margin: 0 0 10px;">' . __( 'Customize the label for the MailChimp opt-in checkbox', 'give_mailchimp' ) . '</span>';
		echo '<input type="text" id="_give_mailchimp_custom_label" name="_give_mailchimp_custom_label" value="' . esc_attr( $custom_label ) . '" placeholder="' . esc_attr( $global_label ) . '"size="25" />';
		echo '</p>';


		//First check that API key is inserted
		echo '<p>';
		echo '<label for="give_mailchimp_lists"  style="font-weight:bold;">' . __( 'MailChimp Opt-in', 'give_mailchimp' ) . '</label> ';

		echo '<span class="cmb2-metabox-description" style="margin: 0 0 10px;">' . __( 'Customize the list(s) and/or group(s) you wish donors to subscribe to if they opt-in.', 'give_mailchimp' ) . '</span>';

		$checked = (array) get_post_meta( $post->ID, '_give_' . esc_attr( $this->id ), true );

		echo '<div class="give-mc-list-wrap">';

		foreach ( $this->get_lists() as $list_id => $list_name ) {
			echo '<label class="list">';
			echo '<input type="checkbox" name="_give_' . esc_attr( $this->id ) . '[]" value="' . esc_attr( $list_id ) . '"' . checked( true, in_array( $list_id, $checked ), false ) . '>';
			echo '<span>' . $list_name . '</span>';
			echo '</label>';

			$groupings = $this->get_groupings( $list_id );
			if ( ! empty( $groupings ) ) {
				foreach ( $groupings as $group_id => $group_name ) {
					echo '<label class="group">';
					echo '<input type="checkbox" name="_give_' . esc_attr( $this->id ) . '[]" value="' . esc_attr( $group_id ) . '"' . checked( true, in_array( $group_id, $checked ), false ) . '>';
					echo '<span>' . $group_name . '</span>';
					echo '</label>';
				}
			}
		}
		echo '</div>';//.give-mc-list-wrap
		echo '</p>';
		echo '</div>';
		//end of form

	}

	/**
	 * Save the metabox data
	 *
	 * @param int $post_id The ID of the post being saved.
	 *
	 * @return void
	 */
	public function save_metabox( $post_id ) {

		$this->give_options = give_get_settings();

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		// Check if our nonce is set.
		if ( ! isset( $_POST['give_mailchimp_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['give_mailchimp_meta_box_nonce'], 'give_mailchimp_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( $_POST['post_type'] == 'give_forms' ) {

			if ( ! current_user_can( 'edit_give_forms', $post_id ) ) {
				return $post_id;
			}

		} else {

			if ( ! current_user_can( 'edit_give_forms', $post_id ) ) {
				return $post_id;
			}

		}

		// OK, its safe for us to save the data now.

		// Sanitize the user input.
		$give_mailchimp_custom_label = sanitize_text_field( $_POST['_give_mailchimp_custom_label'] );
		$give_mailchimp_custom_lists = ! empty( $_POST['_give_mailchimp'] ) ? $_POST['_give_mailchimp'] : $this->give_options['give_mailchimp_list'];

		// Update the meta field.
		update_post_meta( $post_id, '_give_mailchimp_custom_label', $give_mailchimp_custom_label );
		update_post_meta( $post_id, '_give_mailchimp', $give_mailchimp_custom_lists );
		update_post_meta( $post_id, '_give_mailchimp_enable', esc_html( $_POST['_give_mailchimp_enable'] ) );
		update_post_meta( $post_id, '_give_mailchimp_disable', esc_html( $_POST['_give_mailchimp_disable'] ) );

	}

}