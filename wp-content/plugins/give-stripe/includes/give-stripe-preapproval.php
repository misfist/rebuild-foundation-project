<?php
/**
 * Stripe Preapproval Functions
 *
 * @package     Give
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-1.0.php GNU Public License
 * @since       1.1
 */


/**
 * PreApproval Admin Messages
 *
 * @since 1.1
 * @return void
 */
function give_stripe_admin_messages() {

	if ( isset( $_GET['give-message'] ) && 'preapproval-charged' == $_GET['give-message'] ) {
		add_settings_error( 'give-stripe-notices', 'give-stripe-preapproval-charged', __( 'The preapproved payment was successfully charged.', 'give-stripe' ), 'updated' );
	}
	if ( isset( $_GET['give-message'] ) && 'preapproval-failed' == $_GET['give-message'] ) {
		add_settings_error( 'give-stripe-notices', 'give-stripe-preapproval-charged', __( 'The preapproved payment failed to be charged. View order details for further details.', 'give-stripe' ), 'error' );
	}
	if ( isset( $_GET['give-message'] ) && 'preapproval-cancelled' == $_GET['give-message'] ) {
		add_settings_error( 'give-stripe-notices', 'give-stripe-preapproval-cancelled', __( 'The preapproved payment was successfully cancelled.', 'give-stripe' ), 'updated' );
	}

	settings_errors( 'give-stripe-notices' );
}

add_action( 'admin_notices', 'give_stripe_admin_messages' );

/**
 * Trigger preapproved payment charge
 *
 * @since 1.0
 * @return void
 */
function give_stripe_process_preapproved_charge() {

	if ( empty( $_GET['nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_GET['nonce'], 'give-stripe-process-preapproval' ) ) {
		return;
	}

	$payment_id = absint( $_GET['payment_id'] );
	$charge     = give_stripe_charge_preapproved( $payment_id );

	if ( $charge ) {
		wp_redirect( esc_url_raw( add_query_arg( array( 'give-message' => 'preapproval-charged' ), admin_url( 'edit.php?post_type=give_forms&page=give-payment-history' ) ) ) );
		exit;
	} else {
		wp_redirect( esc_url_raw( add_query_arg( array( 'give-message' => 'preapproval-failed' ), admin_url( 'edit.php?post_type=give_forms&page=give-payment-history' ) ) ) );
		exit;
	}

}

add_action( 'give_charge_stripe_preapproval', 'give_stripe_process_preapproved_charge' );


/**
 * Cancel a preapproved payment
 *
 * @since 1.0
 * @return void
 */
function give_stripe_process_preapproved_cancel() {


	if ( empty( $_GET['nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_GET['nonce'], 'give-stripe-process-preapproval' ) ) {
		return;
	}

	$payment_id  = absint( $_GET['payment_id'] );
	$customer_id = get_post_meta( $payment_id, '_give_stripe_customer_id', true );

	if ( empty( $customer_id ) || empty( $payment_id ) ) {
		return;
	}

	if ( 'preapproval' !== get_post_status( $payment_id ) ) {
		return;
	}

	if ( ! class_exists( 'Stripe' ) ) {
		require_once GIVE_STRIPE_PLUGIN_DIR . '/Stripe/Stripe.php';
	}

	give_insert_payment_note( $payment_id, __( 'Preapproval cancelled', 'give-stripe' ) );
	give_update_payment_status( $payment_id, 'cancelled' );
	delete_post_meta( $payment_id, '_give_stripe_customer_id' );

	wp_redirect( esc_url_raw( add_query_arg( array( 'give-message' => 'preapproval-cancelled' ), admin_url( 'edit.php?post_type=give_forms&page=give-payment-history' ) ) ) );
	exit;
}

add_action( 'give_cancel_stripe_preapproval', 'give_stripe_process_preapproved_cancel' );


/**
 * Charge a preapproved payment
 *
 * @since 1.0
 * @return bool
 */
function give_stripe_charge_preapproved( $payment_id = 0 ) {

	global $give_options;

	if ( empty( $payment_id ) ) {
		return false;
	}

	$customer_id = get_post_meta( $payment_id, '_give_stripe_customer_id', true );


	if ( empty( $customer_id ) || empty( $payment_id ) ) {
		return false;
	}

	if ( 'preapproval' !== get_post_status( $payment_id ) ) {
		return false;
	}

	if ( ! class_exists( 'Stripe' ) ) {
		require_once GIVE_STRIPE_PLUGIN_DIR . '/Stripe/Stripe.php';
	}


	$secret_key = give_is_test_mode() ? trim( $give_options['test_secret_key'] ) : trim( $give_options['live_secret_key'] );

	Stripe::setApiKey( $secret_key );

	//Statement Descriptor
	$purchase_data          = give_get_payment_meta( $payment_id );
	$form_title = isset($purchase_data['form_title']) ? $purchase_data['form_title'] : __('Untitled donation form', 'give-stripe');
	$unsupported_characters = array( '<', '>', '"', '\'' );
	$statement_descriptor   = apply_filters( 'give_stripe_statement_descriptor', substr( $form_title, 0, 22 ), $purchase_data );

	$statement_descriptor = str_replace( $unsupported_characters, '', $statement_descriptor );

	//Currency
	if ( give_stripe_is_zero_decimal_currency() ) {
		$amount = give_get_payment_amount( $payment_id );
	} else {
		$amount = give_get_payment_amount( $payment_id ) * 100;
	}

	try {

		$charge = Stripe_Charge::create( array(
				'amount'               => $amount,
				'currency'             => give_get_currency(),
				'customer'             => $customer_id,
				'description'          => sprintf( __( 'Preapproved charge for donation %s made on the "%s" form from %s', 'give-stripe' ), give_get_payment_key( $payment_id ), $form_title, home_url() ),
				'statement_descriptor' => $statement_descriptor,
			)
		);

	}
	catch ( Stripe_CardError $e ) {

		$body = $e->getJsonBody();
		$err  = $body['error'];

		$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'give-stripe' );

	}
	catch ( Stripe_ApiConnectionError $e ) {

		$body = $e->getJsonBody();
		$err  = $body['error'];

		$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'give-stripe' );

	}
	catch ( Stripe_InvalidRequestError $e ) {

		$body = $e->getJsonBody();
		$err  = $body['error'];

		$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'give-stripe' );

	}
	catch ( Stripe_ApiError $e ) {

		$body = $e->getJsonBody();
		$err  = $body['error'];

		$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'give-stripe' );
	}
	catch ( Stripe_AuthenticationError $e ) {

		$body = $e->getJsonBody();
		$err  = $body['error'];

		$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'give-stripe' );

	}
	catch ( Stripe_Error $e ) {

		$body = $e->getJsonBody();
		$err  = $body['error'];

		$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'give-stripe' );

	}
	catch ( Exception $e ) {

		// some sort of other error
		$body = $e->getJsonBody();
		$err  = $body['error'];

		$error_message = isset( $err['message'] ) ? $err['message'] : __( 'There was an error processing this charge', 'give-stripe' );

	}

	if ( ! empty( $charge ) ) {

		give_insert_payment_note( $payment_id, 'Stripe Charge ID: ' . $charge->id );
		give_update_payment_status( $payment_id, 'publish' );
		delete_post_meta( $payment_id, '_give_stripe_customer_id' );

		return true;

	} else {

		give_insert_payment_note( $payment_id, $error_message );

		return false;
	}
}


/**
 * Register payment statuses for PreApproval
 *
 * @since 1.0
 * @return void
 */
function give_stripe_register_post_statuses() {
	register_post_status( 'preapproval', array(
		'label'                     => _x( 'Preapproved', 'Preapproved payment', 'give-stripe' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'give-stripe' )
	) );
	register_post_status( 'cancelled', array(
		'label'                     => _x( 'Cancelled', 'Cancelled payment', 'give-stripe' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'give-stripe' )
	) );
}

add_action( 'init', 'give_stripe_register_post_statuses', 110 );


/**
 * Register our new payment status labels for Give Stripe
 *
 * @since 1.0
 * @return array
 */
function give_stripe_payment_status_labels( $statuses ) {
	$statuses['preapproval'] = __( 'Preapproved', 'give-stripe' );
	$statuses['cancelled']   = __( 'Cancelled', 'give-stripe' );

	return $statuses;
}

add_filter( 'give_payment_statuses', 'give_stripe_payment_status_labels' );


/**
 * Display the Preapprove column label
 *
 * @since 1.0
 * @return array
 */
function give_stripe_payments_column( $columns ) {

	global $give_options;

	if ( isset( $give_options['stripe_preapprove_only'] ) ) {
		$columns['preapproval'] = __( 'Preapproval', 'give-stripe' );
	}

	return $columns;
}

add_filter( 'give_payments_table_columns', 'give_stripe_payments_column' );


/**
 * Show the Process / Cancel buttons for preapproved payments
 *
 * @param $value
 * @param $payment_id
 * @param $column_name
 *
 * @return string
 */
function give_stripe_payments_column_data( $value, $payment_id, $column_name ) {

	if ( $column_name == 'preapproval' ) {

		$status      = get_post_status( $payment_id );
		$customer_id = get_post_meta( $payment_id, '_give_stripe_customer_id', true );

		if ( give_is_payment_complete( $payment_id ) ) {
			return __( 'Complete', 'give-stripe' );
		} elseif ( $status == 'cancelled' ) {
			return __( 'Cancelled', 'give-stripe' );
		}

		if ( ! $customer_id ) {
			return $value;
		}

		$nonce = wp_create_nonce( 'give-stripe-process-preapproval' );

		$preapproval_args = array(
			'payment_id'  => $payment_id,
			'nonce'       => $nonce,
			'give-action' => 'charge_stripe_preapproval'
		);
		$cancel_args      = array(
			'preapproval_key' => $customer_id,
			'payment_id'      => $payment_id,
			'nonce'           => $nonce,
			'give-action'     => 'cancel_stripe_preapproval'
		);

		if ( 'preapproval' === $status ) {
			$value = '<a href="' . esc_url( add_query_arg( $preapproval_args, admin_url( 'edit.php?post_type=give_forms&page=give-payment-history' ) ) ) . '" class="button-secondary button button-small" style="width: 120px; margin: 0 0 3px; text-align:center;">' . __( 'Process Payment', 'give-stripe' ) . '</a>&nbsp;';
			$value .= '<a href="' . esc_url( add_query_arg( $cancel_args, admin_url( 'edit.php?post_type=give_forms&page=give-payment-history' ) ) ) . '" class="button-secondary button button-small" style="width: 120px; margin: 0; text-align:center;">' . __( 'Cancel Preapproval', 'give-stripe' ) . '</a>';
		}
	}

	return $value;
}

add_filter( 'give_payments_table_column', 'give_stripe_payments_column_data', 10, 3 );

