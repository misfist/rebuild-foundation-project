<?php

/**
 * MailChimp class, Add-on of the base newsletter class
 *
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Give_MailChimp extends Give_Newsletter {
	/**
	 * Sets up the checkout label
	 */
	public function init() {

		add_action( 'cmb2_save_options-page_fields', array( $this, 'save_settings' ), 10, 4 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100 );

	}


	/**
	 * Load Admin Scripts
	 *
	 * Enqueues the required admin scripts.
	 *
	 * @since 1.0
	 * @global       $post
	 *
	 * @param string $hook Page hook
	 *
	 * @return void
	 */
	function admin_scripts( $hook ) {

		global $wp_version, $post, $post_type;

		//Directories of assets
		$js_dir     = GIVE_MAILCHIMP_URL . 'assets/js/admin/';
		$js_plugins = GIVE_MAILCHIMP_URL . 'assets/js/plugins/';
		$css_dir    = GIVE_MAILCHIMP_URL . 'assets/css/admin/';


		//Forms CPT Script
		if ( $post_type === 'give_forms' ) {

			wp_enqueue_style( 'give-mailchimp-admin-css', $css_dir . 'admin-forms.css', GIVE_MAILCHIMP_VERSION );

			wp_enqueue_script( 'give-mailchimp-admin-forms-scripts', $js_dir . 'admin-forms.js', array( 'jquery' ), GIVE_MAILCHIMP_VERSION, false );

		}


	}


	/**
	 * Retrieves the lists from MailChimp
	 */
	public function get_lists() {

		global $give_options;

		if ( ! empty( $give_options['give_mailchimp_api'] ) ) {

			$list_data = get_transient( 'give_mailchimp_list_data' );

			if ( false === $list_data ) {

				$api       = new Give_MailChimp_API( trim( $give_options['give_mailchimp_api'] ) );
				$list_data = $api->call( 'lists/list' );

				set_transient( 'give_mailchimp_list_data', $list_data, 24 * 24 * 24 );
			}

			if ( ! empty( $list_data ) ) {
				foreach ( $list_data->data as $key => $list ) {

					$this->lists[ $list->id ] = $list->name;

				}
			}
		}

		return (array) $this->lists;
	}

	/**
	 * Retrive the list of groupings associated with a list id
	 *
	 * @param  string $list_id List id for which groupings should be returned
	 *
	 * @return array  $groups_data Data about the groups
	 */
	public function get_groupings( $list_id = '' ) {

		global $give_options;

		if ( ! empty( $give_options['give_mailchimp_api'] ) ) {

			$grouping_data = get_transient( 'give_mailchimp_groupings_' . $list_id );

			if ( false === $grouping_data ) {

				if ( ! class_exists( 'Give_MailChimp_API' ) ) {
					require_once( GIVE_MAILCHIMP_PATH . '/includes/MailChimp.class.php' );
				}

				$api           = new Give_MailChimp_API( trim( $give_options['give_mailchimp_api'] ) );
				$grouping_data = $api->call( 'lists/interest-groupings', array( 'id' => $list_id ) );

				set_transient( 'give_mailchimp_groupings_' . $list_id, $grouping_data, 24 * 24 * 24 );
			}

			$groups_data = array();

			if ( $grouping_data && ! isset( $grouping_data->status ) ) {

				foreach ( $grouping_data as $grouping ) {

					$grouping_id   = $grouping->id;
					$grouping_name = $grouping->name;

					foreach ( $grouping->groups as $groups ) {

						$group_name                                       = $groups->name;
						$groups_data["$list_id|$grouping_id|$group_name"] = $grouping_name . ' - ' . $group_name;

					}

				}

			}

		}

		return $groups_data;
	}

	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {

		$give_mailchimp_settings = array(
			array(
				'name' => __( 'MailChimp Settings', 'give_mailchimp' ),
				'desc' => '<hr>',
				'id'   => 'give_title_mailchimp',
				'type' => 'give_title'
			),
			array(
				'id'   => 'give_mailchimp_api',
				'name' => __( 'MailChimp API Key', 'give_mailchimp' ),
				'desc' => __( 'Enter your MailChimp API key', 'give_mailchimp' ),
				'type' => 'text',
				'size' => 'regular'
			),
			array(
				'id'   => 'give_mailchimp_double_opt_in',
				'name' => __( 'Double Opt-In', 'give_mailchimp' ),
				'desc' => __( 'When checked, users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.', 'give_mailchimp' ),
				'type' => 'checkbox'
			),
			array(
				'id'   => 'give_mailchimp_show_checkout_signup',
				'name' => __( 'Enable Globally?', 'give_mailchimp' ),
				'desc' => __( 'Allow customers to signup for the list selected below on all forms? Note: the list(s) can be customized per form.', 'give_mailchimp' ),
				'type' => 'checkbox'
			),
			array(
				'id'      => 'give_mailchimp_list',
				'name'    => __( 'Default List', 'givea' ),
				'desc'    => __( 'Select the list you wish for all donors to subscribe to by default. Note: the list(s) can be customized per form.', 'give_mailchimp' ),
				'type'    => 'select',
				'options' => $this->get_lists()
			),
			array(
				'id'         => 'give_mailchimp_label',
				'name'       => __( 'Default Label', 'give_mailchimp' ),
				'desc'       => __( 'This is the text shown by default next to the MailChimp sign up checkbox. Yes, this can also be customized per form.', 'give_mailchimp' ),
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => __( 'Subscribe to our newsletter', 'give_mailchimp' ),
				),
			),
		);

		return array_merge( $settings, $give_mailchimp_settings );
	}


	/**
	 * Flush the list transient on save
	 *
	 * @description Hooks into CMB2 options save action and deleted transient
	 *
	 * @param $object_id
	 * @param $cmb_id
	 * @param $updated
	 * @param $this_object
	 *
	 * @return mixed
	 */
	public function save_settings( $object_id, $cmb_id, $updated, $this_object ) {

		$api_option = give_get_option( 'give_mailchimp_api' );

		if ( isset( $api_option ) && ! empty( $api_option ) ) {
			delete_transient( 'give_mailchimp_list_data' );
		}

	}

	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		global $give_options;

		return ! empty( $give_options['give_mailchimp_show_checkout_signup'] );
	}

	/**
	 * Subscribe Email
	 *
	 * @description Subscribe an email to a list
	 *
	 * @param array $user_info
	 * @param bool  $list_id
	 * @param bool  $opt_in_overridde
	 *
	 * @return bool
	 */
	public function subscribe_email( $user_info = array(), $list_id = false, $opt_in_overridde = false ) {

		global $give_options;

		// Make sure an API key has been entered
		if ( empty( $give_options['give_mailchimp_api'] ) ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if ( ! $list_id ) {
			$list_id = ! empty( $give_options['give_mailchimp_list'] ) ? $give_options['give_mailchimp_list'] : false;
			if ( ! $list_id ) {
				return false;
			}
		}

		if ( ! class_exists( 'Give_MailChimp_API' ) ) {
			require_once( GIVE_MAILCHIMP_PATH . '/includes/MailChimp.class.php' );
		}

		$api    = new Give_MailChimp_API( trim( $give_options['give_mailchimp_api'] ) );
		$opt_in = isset( $give_options['give_mailchimp_double_opt_in'] ) && ! $opt_in_overridde;

		$merge_vars = array( 'FNAME' => $user_info['first_name'], 'LNAME' => $user_info['last_name'] );

		if ( strpos( $list_id, '|' ) != false ) {
			$parts = explode( '|', $list_id );

			$list_id     = $parts[0];
			$grouping_id = $parts[1];
			$group_name  = $parts[2];

			$groupings = array(
				array(
					'id'     => $grouping_id,
					'groups' => array( $group_name )
				)
			);

			$merge_vars['groupings'] = $groupings;
		}

		$result = $api->call( 'lists/subscribe', apply_filters( 'give_mc_subscribe_vars', array(
			'id'                => $list_id,
			'email'             => array( 'email' => $user_info['email'] ),
			'merge_vars'        => $merge_vars,
			'double_optin'      => $opt_in,
			'update_existing'   => true,
			'replace_interests' => false,
			'send_welcome'      => false,
		) ) );

		if ( $result ) {
			return true;
		}

		return false;

	}

}
