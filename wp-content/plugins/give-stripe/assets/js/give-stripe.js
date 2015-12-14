/**
 * Give - Stripe Gateway Add-on JS
 */
var give_global_vars, give_stripe_vars;
jQuery( document ).ready( function ( $ ) {

	Stripe.setPublishableKey( give_stripe_vars.publishable_key );

	$body = jQuery( 'body' );

	//Setup stripe token on submit
	$body.on( 'submit', '.give-form', function ( event ) {

		var $form = jQuery( this );

		if ( $form.find( 'input.give-gateway:checked' ).val() == 'stripe' ) {

			event.preventDefault();

			$form.addClass( 'stripe-checkout' );

			give_stripe_process_card( $form );

		}

	} );

	//Profile update
	$body.on( 'submit', '#give-recurring-form', function ( event ) {

		var $form = jQuery( this );

		event.preventDefault();

		give_stripe_process_card( $form );

	} );


} );

/**
 * Stripe Response Handler
 *
 *
 * @see https://stripe.com/docs/tutorials/forms
 * @param status
 * @param response - {
					   id: "tok_u5dg20Gra", // String of token identifier,
					   card: {...}, // Dictionary of the card used to create the token
					   created: 1426967537, // Integer of date token was created
					   currency: "usd", // String currency that the token was created in
					   livemode: true, // Boolean of whether this token was created with a live or test API key
					   object: "token", // String identifier of the type of object, always "token"
					   used: false // Boolean of whether this token has been used
					 }
 */
function give_stripe_response_handler( status, response ) {

	var $form = jQuery( '.give-form.stripe-checkout' );

	if ( typeof $form === 'undefined' || $form.length === 0 ) {
		$form = jQuery( '#give-recurring-form' );
	}

	var $form_submit_btn = $form.find( '#give-purchase-button' );

	//Check for errors
	if ( response.error ) {

		// re-enable the submit button
		$form_submit_btn.attr( "disabled", false );

		// Hide the loading animation
		jQuery( '.give-loading-animation' ).fadeOut();

		//the error
		var error = '<div class="give_errors"><p class="give_error">' + response.error.message + '</p></div>';

		// show the errors on the form
		$form.find( '#give-stripe-payment-errors' ).html( error );

		// re-add original submit button text
		if ( give_global_vars.complete_purchase ) {
			$form_submit_btn.val( give_global_vars.complete_purchase );
		} else {
			$form_submit_btn.val( 'Donate Now' );
		}


	} else {

		// token contains id, last4, and card type
		var token = response['id'];

		// insert the token into the form so it gets submitted to the server
		$form.append( "<input type='hidden' name='give_stripe_token' value='" + token + "' />" );

		// and submit
		$form.get( 0 ).submit();

	}
}

/**
 * Stripe Process CC
 *
 * @param  $form object
 * @returns {boolean}
 */
function give_stripe_process_card( $form ) {

	// disable the submit button to prevent repeated clicks
	$form.find( '#give-purchase-button' ).attr( 'disabled', 'disabled' );

	if ( $form.find( '.billing_country' ).val() == 'US' ) {
		var state = $form.find( '#card_state_us' ).val();
	} else if ( $form.find( '.billing_country' ).val() == 'CA' ) {
		var state = $form.find( '#card_state_ca' ).val();
	} else {
		var state = $form.find( '#card_state_other' ).val();
	}

	if ( typeof $form.find( '#card_state_us' ).val() != 'undefined' ) {

		if ( $form.find( '.billing_country' ).val() == 'US' ) {
			var state = $form.find( '#card_state_us' ).val();
		} else if ( $form.find( '.billing_country' ).val() == 'CA' ) {
			var state = $form.find( '#card_state_ca' ).val();
		} else {
			var state = $form.find( '#card_state_other' ).val();
		}

	} else {
		var state = $form.find( '.card_state' ).val();
	}

	// createToken returns immediately - the supplied callback submits the form if there are no errors
	Stripe.createToken( {
		number         : $form.find( '.card-number' ).val().replace(/\s+/g, ''),
		name           : $form.find( '.card-name' ).val(),
		cvc            : $form.find( '.card-cvc' ).val().replace(/\s+/g, ''),
		exp_month      : $form.find( '.card-expiry-month' ).val(),
		exp_year       : $form.find( '.card-expiry-year' ).val(),
		address_line1  : $form.find( '.card-address' ).val(),
		address_line2  : $form.find( '.card-address-2' ).val(),
		address_city   : $form.find( '.card-city' ).val(),
		address_state  : state,
		address_zip    : $form.find( '.card-zip' ).val(),
		address_country: $form.find( '#billing_country' ).val()
	}, give_stripe_response_handler );

	return false; // submit from callback
}
