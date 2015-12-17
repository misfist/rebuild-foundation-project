<?php

/**
 * Give MailChimp Ecommerce360 class
 *
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Give_MC_Ecommerce_360 {

	/**
	 * MailChimp API Key
	 *
	 * @var string | NULL
	 */
	public $key = null;

	public function __construct() {

		if ( ! function_exists( 'give_get_option' ) ) {
			return;
		}

		$api_key = give_get_option( 'give_mailchimp_api' );

		if ( ! empty( $api_key ) ) {
			$this->key = trim( $api_key );
		}

		add_action( 'init', array( $this, 'set_ecommerce360_session' ) );

		add_action( 'give_insert_payment', array( $this, 'set_ecommerce360_flags' ), 10, 2 );
		add_action( 'give_complete_purchase', array( $this, 'record_ecommerce360_donation' ) );
		add_action( 'give_update_payment_status', array( $this, 'delete_ecommerce360_donation' ), 10, 3 );
	}

	/**
	 * Sets flags in post meta so that we can detect them when completing a purchase via IPN
	 *
	 * @param  integer $payment_id
	 * @param  array   $payment_data
	 *
	 * @return bool
	 */
	public function set_ecommerce360_flags( $payment_id = 0, $payment_data = array() ) {

		// Make sure an API key has been entered
		if ( empty( $this->key ) ) {
			return false;
		}

		// Don't record details if we're in test mode
		if ( give_is_test_mode() ) {
			return false;
		}

		$mc_cid_key = self::_give_ec360_get_session_id( 'campaign' );
		$mc_eid_key = self::_give_ec360_get_session_id( 'email' );

		$campaign_id = Give()->session->get( $mc_cid_key );
		$email_id    = Give()->session->get( $mc_eid_key );

		if ( isset( $campaign_id ) && isset( $email_id ) ) {

			add_post_meta( $payment_id, '_give_mc_campaign_id', $campaign_id, true );
			add_post_meta( $payment_id, '_give_mc_email_id', $email_id, true );

			Give()->session->set( $mc_cid_key, null );
			Give()->session->set( $mc_eid_key, null );

		}

	}

	/**
	 * Send purchase details to MailChimp's Ecommerce360 Add-on.
	 *
	 * @param  integer $payment_id [description]
	 *
	 * @return bool
	 */
	public function record_ecommerce360_donation( $payment_id = 0 ) {

		// Make sure an API key has been entered
		if ( empty( $this->key ) ) {
			return false;
		}

		// Don't record details if we're in test mode
		if ( give_is_test_mode() ) {
			return false;
		}

		$payment   = give_get_payment_meta( $payment_id );
		$user_info = give_get_payment_meta_user_info( $payment_id );
		$amount    = give_get_payment_amount( $payment_id );

		// Get the categories that this download belongs to, if any
		$terms = get_the_terms( $payment['form_id'], 'give_forms_category' );

		if ( $terms && ! is_wp_error( $terms ) ) {
			$categories = array();

			foreach ( $terms as $term ) {
				$categories[] = $term->name;
			}

			$category_id   = $terms[0]->term_id;
			$category_name = join( " - ", $categories );
		} else {
			$category_id   = 1;
			$category_name = 'Form';
		}


		$item = array(
			'line_num'      => 1,
			'product_id'    => (int) $payment['form_id'], // int only and required, lame
			'product_name'  => $payment['form_title'],
			'category_id'   => $category_id,          // int, required
			'category_name' => $category_name,        // string, required
			'qty'           => 1,
			// 1 always
			'cost'          => $amount,
			// double, cost of single line item
		);

		$order = array(
			'id'         => (string) $payment_id,
			// string
			'email'      => $user_info['email'],
			// string
			'total'      => $amount,
			// double
			'store_id'   => self::_give_ec360_get_store_id(),
			// string, 32 char limit
			'store_name' => home_url(),
			// string
			'items'      => array( $item ),
			// should contain an array of arrays, since it's designed to allow submitting multiple products/items in a given call
		);

		// Set Ecommerce360 variables if they exist
		$campaign_id = get_post_meta( $payment_id, '_give_mc_campaign_id', true );
		$email_id    = get_post_meta( $payment_id, '_give_mc_email_id', true );

		if ( ! empty( $campaign_id ) ) {
			$order['campaign_id'] = $campaign_id;
		}

		if ( ! empty( $email_id ) ) {
			$order['email_id'] = $email_id;
		}

		// Send to MailChimp
		$options   = array(
			'CURLOPT_FOLLOWLOCATION' => false
		);
		$mailchimp = new Give_MailChimp_API( $this->key, $options );

		try {
			$result = $mailchimp->call( 'ecomm/order-add', array( 'order' => $order ) );
			give_insert_payment_note( $payment_id, __( 'Donation details have been added to MailChimp successfully', 'give_mailchimp' ) );

		}
		catch ( Exception $e ) {
			give_insert_payment_note( $payment_id, __( 'MailChimp Ecommerce360 Error: ', 'give_mailchimp' ) . $e->getMessage() );

			return false;
		}

		return true;
	}


	/**
	 * Remove an order from MailChimp if the payment was refunded
	 *
	 * @return bool
	 */
	public function delete_ecommerce360_donation( $payment_id, $new_status, $old_status ) {
		if ( 'publish' != $old_status && 'revoked' != $old_status ) {
			return;
		}

		if ( 'refunded' != $new_status ) {
			return;
		}

		// Make sure an API key has been entered
		if ( empty( $this->key ) ) {
			return false;
		}

		// Send to MailChimp
		$options   = array(
			'CURLOPT_FOLLOWLOCATION' => false
		);
		$mailchimp = new Give_MailChimp_API( $this->key, $options );

		try {
			$result = $mailchimp->call( 'ecomm/order-del', array(
				'store_id' => self::_give_ec360_get_store_id(),
				'order_id' => $payment_id
			) );
			give_insert_payment_note( $payment_id, __( 'Donation details have been removed from MailChimp successfully', 'give_mailchimp' ) );

			return true;
		}
		catch ( Exception $e ) {
			give_insert_payment_note( $payment_id, __( 'MailChimp Ecommerce360 Error: ', 'give_mailchimp' ) . $e->getMessage() );

			return false;
		}
	}

	/**
	 * Enables MailChimp's Ecommerce360 tracking from the parameters
	 * added to a newsletter campaign
	 *
	 * @uses campaign UID
	 * @uses member email's UID
	 */
	public function set_ecommerce360_session() {
		$mc_cid = isset( $_GET['mc_cid'] ) ? $_GET['mc_cid'] : '';
		$mc_eid = isset( $_GET['mc_eid'] ) ? $_GET['mc_eid'] : '';

		if ( ! empty( $mc_cid ) && ! empty( $mc_eid ) ) {
			Give()->session->set( self::_give_ec360_get_session_id( 'campaign' ), filter_var( $mc_cid, FILTER_SANITIZE_STRING ) );
			Give()->session->set( self::_give_ec360_get_session_id( 'email' ), filter_var( $mc_eid, FILTER_SANITIZE_STRING ) );
		}
	}

	/**
	 * Returns the unique EC360 session keys for this Give installation.
	 *
	 * @param  string $type campaign | email
	 *
	 * @return string Key identifier for stored sessions
	 */
	protected static function _give_ec360_get_session_id( $type = 'campaign' ) {
		$prefix = substr( $type, 0, 1 );

		return sprintf( 'give_mc360_%1$s_%2$sid', substr( self::_give_ec360_get_store_id(), 0, 10 ), $prefix );
	}

	/**
	 * Returns the store ID variable for use in the MailChimp API
	 *
	 * @return string
	 */
	protected static function _give_ec360_get_store_id() {
		return md5( home_url() );
	}

}
