<?php
/**
 * Stripe Helper Functions
 *
 * @package     Give
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-1.0.php GNU Public License
 * @since       1.1
 */


/**
 * Stripe uses it's own credit card form because the card details are tokenized.
 *
 * @description We don't want the name attributes to be present on the fields in order to prevent them from getting posted to the server
 *
 * @access      public
 * @since       1.0
 * @return      string $form
 */
function give_stripe_credit_card_form( $form_id, $echo = true ) {

	global $give_options;

	$stripe_js_fallback = isset( $give_options['stripe_js_fallback'] );

	ob_start();

	do_action( 'give_before_cc_fields', $form_id ); ?>

	<fieldset id="give_cc_fields" class="give-do-validate">

		<legend><?php _e( 'Credit Card Info', 'give' ); ?></legend>

		<?php if ( is_ssl() ) : ?>
			<div id="give_secure_site_wrapper">
				<span class="give-icon padlock"></span>
				<span><?php _e( 'This is a secure SSL encrypted payment.', 'give' ); ?></span>
			</div>
		<?php endif; ?>

		<p id="give-card-number-wrap" class="form-row form-row-two-thirds">
			<label for="card_number" class="give-label">
				<?php _e( 'Card Number', 'give' ); ?>
				<span class="give-required-indicator">*</span>
				<span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e( 'The (typically) 16 digits on the front of your credit card.', 'give' ); ?>"></span>
				<span class="card-type"></span>
			</label>
			<input type="tel" autocomplete="off" <?php if ( isset( $give_options['stripe_js_fallback'] ) ) {
				echo 'name="card_number" ';
			} ?> id="card_number" class="card-number give-input required" placeholder="<?php _e( 'Card number', 'give' ); ?>" />
		</p>

		<p id="give-card-cvc-wrap" class="form-row form-row-one-third">
			<label for="card_cvc" class="give-label">
				<?php _e( 'CVC', 'give' ); ?>
				<span class="give-required-indicator">*</span>
				<span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'give' ); ?>"></span>
			</label>
			<input type="tel" size="4" autocomplete="off" <?php if ( isset( $give_options['stripe_js_fallback'] ) ) {
				echo 'name="card_cvc" ';
			} ?>id="card_cvc" class="card-cvc give-input required" placeholder="<?php _e( 'Security code', 'give' ); ?>" />
		</p>

		<p id="give-card-name-wrap" class="form-row form-row-two-thirds">
			<label for="card_name" class="give-label">
				<?php _e( 'Name on the Card', 'give' ); ?>
				<span class="give-required-indicator">*</span>
				<span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e( 'The name printed on the front of your credit card.', 'give' ); ?>"></span>
			</label>

			<input type="text" autocomplete="off" <?php echo $stripe_js_fallback ? 'name="card_name" ' : ''; ?>id="card_name" class="card-name give-input required" placeholder="<?php _e( 'Card name', 'give' ); ?>" />
		</p>

		<?php do_action( 'give_before_cc_expiration' ); ?>

		<p class="card-expiration form-row form-row-one-third">
			<label for="card_expiry" class="give-label">
				<?php _e( 'Expiration (MM/YY)', 'give' ); ?>
				<span class="give-required-indicator">*</span>
				<span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e( 'The date your credit card expires, typically on the front of the card.', 'give' ); ?>"></span>
			</label>

			<input type="hidden" id="card_exp_month" <?php echo $stripe_js_fallback ? 'name="card_exp_month" ' : ''; ?>class="card-expiry-month" />
			<input type="hidden" id="card_exp_year" <?php echo $stripe_js_fallback ? 'name="card_exp_year" ' : ''; ?>class="card-expiry-year" />

			<input type="tel" autocomplete="off" <?php echo $stripe_js_fallback ? 'name="card_expiry" ' : ''; ?>id="card_expiry" class="card-expiry give-input required" placeholder="<?php _e( 'MM / YY', 'give' ); ?>" />
		</p>

		<?php do_action( 'give_after_cc_expiration', $form_id ); ?>

	</fieldset>
	<?php

	do_action( 'give_after_cc_fields', $form_id );

	$form = ob_get_clean();

	if ( false !== $echo ) {
		echo $form;
	}

	return $form;
}

add_action( 'give_stripe_cc_form', 'give_stripe_credit_card_form' );

/**
 * Add an errors div
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function give_stripe_add_stripe_errors() {
	echo '<div id="give-stripe-payment-errors"></div>';
}

add_action( 'give_after_cc_fields', 'give_stripe_add_stripe_errors', 999 );


/**
 * Get the meta key for storing Stripe customer IDs in
 *
 * @access      public
 * @since       1.0
 * @return      string $key
 */
function give_stripe_get_customer_key() {

	$key = '_give_stripe_customer_id';
	if ( give_is_test_mode() ) {
		$key .= '_test';
	}

	return $key;
}

/**
 * Determines if the shop is using a zero-decimal currency
 *
 * @access      public
 * @since       1.0
 * @return      bool
 */
function give_stripe_is_zero_decimal_currency() {

	$ret      = false;
	$currency = give_get_currency();

	switch ( $currency ) {

		case 'BIF' :
		case 'CLP' :
		case 'DJF' :
		case 'GNF' :
		case 'JPY' :
		case 'KMF' :
		case 'KRW' :
		case 'MGA' :
		case 'PYG' :
		case 'RWF' :
		case 'VND' :
		case 'VUV' :
		case 'XAF' :
		case 'XOF' :
		case 'XPF' :

			$ret = true;
			break;

	}

	return $ret;
}


/**
 * Given a Payment ID, extract the transaction ID from Stripe
 *
 * @param  string $payment_id Payment ID
 *
 * @return string                   Transaction ID
 */
function give_stripe_get_payment_transaction_id( $payment_id ) {

	$notes          = give_get_payment_notes( $payment_id );
	$transaction_id = '';

	foreach ( $notes as $note ) {
		if ( preg_match( '/^Stripe Charge ID: ([^\s]+)/', $note->comment_content, $match ) ) {
			$transaction_id = $match[1];
			continue;
		}
	}

	return apply_filters( 'give_stripe_set_payment_transaction_id', $transaction_id, $payment_id );
}

add_filter( 'give_get_payment_transaction_id-stripe', 'give_stripe_get_payment_transaction_id', 10, 1 );


/**
 * Display the payment status filters
 *
 * @since 1.0
 * @return array
 */
function give_stripe_payment_status_filters( $views ) {
	$payment_count        = wp_count_posts( 'give_payment' );
	$preapproval_count    = '&nbsp;<span class="count">(' . $payment_count->preapproval . ')</span>';
	$cancelled_count      = '&nbsp;<span class="count">(' . $payment_count->cancelled . ')</span>';
	$current              = isset( $_GET['status'] ) ? $_GET['status'] : '';
	$views['preapproval'] = sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'preapproval', admin_url( 'edit.php?post_type=give_forms&page=give-payment-history' ) ) ), $current === 'preapproval' ? ' class="current"' : '', __( 'Preapproval Pending', 'give-stripe' ) . $preapproval_count );
	$views['cancelled']   = sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'cancelled', admin_url( 'edit.php?post_type=give_forms&page=give-payment-history' ) ) ), $current === 'cancelled' ? ' class="current"' : '', __( 'Cancelled', 'give-stripe' ) . $cancelled_count );

	return $views;
}

add_filter( 'give_payments_table_views', 'give_stripe_payment_status_filters' );
