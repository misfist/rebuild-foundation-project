<?php
/**
 * Stripe Admin Functions
 *
 * @package     Give
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-1.0.php GNU Public License
 * @since       1.1
 */


/**
 * Register the gateway settings
 *
 * @access      public
 * @since       1.0
 *
 * @param $settings
 *
 * @return array
 */
function give_stripe_add_settings( $settings ) {

	$stripe_settings = array(
		array(
			'name' => __( 'Stripe Settings', 'give-stripe' ),
			'desc' => '<hr>',
			'id'   => 'give_title',
			'type' => 'give_title'
		),
		array(
			'name' => __( 'Live Secret Key', 'give-stripe' ),
			'desc' => __( 'Enter your live secret key, found in your Stripe Account Settings', 'give-stripe' ),
			'id'   => 'live_secret_key',
			'type' => 'text',
		),
		array(
			'name' => __( 'Live Publishable Key', 'give-stripe' ),
			'desc' => __( 'Enter your live publishable key, found in your Stripe Account Settings', 'give-stripe' ),
			'id'   => 'live_publishable_key',
			'type' => 'text'
		),
		array(
			'name' => __( 'Test Secret Key', 'give-stripe' ),
			'desc' => __( 'Enter your test secret key, found in your Stripe Account Settings', 'give-stripe' ),
			'id'   => 'test_secret_key',
			'type' => 'text'
		),
		array(
			'name' => __( 'Test Publishable Key', 'give-stripe' ),
			'desc' => __( 'Enter your test publishable key, found in your Stripe Account Settings', 'give-stripe' ),
			'id'   => 'test_publishable_key',
			'type' => 'text',
		),
		array(
			'name' => __( 'Stripe JS Fallback Support', 'give-stripe' ),
			'desc' => __( 'Check this if your site has problems with processing cards using Stripe JS. This option makes card processing slightly less secure.', 'give-stripe' ),
			'id'   => 'stripe_js_fallback',
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Preapprove Only?', 'give-stripe' ),
			'desc' => __( 'Check this if you would like to preapprove payments but not charge until a later date.', 'give-stripe' ),
			'id'   => 'stripe_preapprove_only',
			'type' => 'checkbox'
		)
	);

	return array_merge( $settings, $stripe_settings );

}

add_filter( 'give_settings_gateways', 'give_stripe_add_settings' );


/**
 * Given a transaction ID, generate a link to the Stripe transaction ID details
 *
 * @since  1.0
 *
 * @param  string $transaction_id The Transaction ID
 * @param  int    $payment_id     The payment ID for this transaction
 *
 * @return string                 A link to the Transaction details
 */
function give_stripe_link_transaction_id( $transaction_id, $payment_id ) {

	$test = give_get_payment_meta( $payment_id, '_give_payment_mode' ) === 'test' ? 'test/' : '';
	$url  = '<a href="https://dashboard.stripe.com/' . $test . 'payments/' . $transaction_id . '" target="_blank">' . $transaction_id . '</a>';

	return apply_filters( 'give_stripe_link_payment_details_transaction_id', $url );

}

add_filter( 'give_payment_details_transaction_id-stripe', 'give_stripe_link_transaction_id', 10, 2 );
