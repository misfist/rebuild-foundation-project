<?php
/**
 * Stripe Payment Processing Functions
 *
 * @package     Give
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-1.0.php GNU Public License
 * @since       1.1
 */


/**
 * Process stripe checkout submission
 *
 * @access      public
 * @since       1.0
 *
 * @param array $purchase_data
 *
 * @return      void
 */
function give_stripe_process_stripe_payment( $purchase_data ) {

	global $give_options;

	if ( ! class_exists( 'Stripe' ) ) {
		require_once GIVE_STRIPE_PLUGIN_DIR . '/Stripe/Stripe.php';
	}

	if ( give_is_test_mode() ) {
		$secret_key = trim( $give_options['test_secret_key'] );
	} else {
		$secret_key = trim( $give_options['live_secret_key'] );
	}

	$purchase_summary = give_get_purchase_summary( $purchase_data, false );

	// make sure we don't have any left over errors present
	give_clear_errors();

	if ( ! isset( $_POST['give_stripe_token'] ) ) {

		// check for fallback mode
		if ( isset( $give_options['stripe_js_fallback'] ) ) {

			$card_data = give_stripe_process_post_data( $purchase_data );

		} else {

			// no Stripe token
			give_set_error( 'no_token', __( 'Missing Stripe token. Please contact support.', 'give-stripe' ) );
			give_record_gateway_error( __( 'Missing Stripe Token', 'give-stripe' ), __( 'A Stripe token failed to be generated. Please check Stripe logs for more information', 'give-stripe' ) );

		}

	} else {
		$card_data = $_POST['give_stripe_token'];
	}

	$errors = give_get_errors();

	if ( ! $errors ) {

		try {

			Stripe::setApiKey( $secret_key );

			// setup the payment details
			$payment_data = array(
				'price'           => $purchase_data['price'],
				'give_form_title' => $purchase_data['post_data']['give-form-title'],
				'give_form_id'    => intval( $purchase_data['post_data']['give-form-id'] ),
				'date'            => $purchase_data['date'],
				'user_email'      => $purchase_data['user_email'],
				'purchase_key'    => $purchase_data['purchase_key'],
				'currency'        => give_get_currency(),
				'user_info'       => $purchase_data['user_info'],
				'status'          => 'pending',
				'gateway'         => 'stripe'
			);

			$customer_exists = false;

			if ( is_user_logged_in() ) {

				$user = get_user_by( 'email', $purchase_data['user_email'] );

				if ( $user ) {

					$customer_id = get_user_meta( $user->ID, give_stripe_get_customer_key(), true );

					if ( $customer_id ) {

						$customer_exists = true;

						try {

							// Update the customer to ensure their card data is up to date
							$cu = Stripe_Customer::retrieve( $customer_id );

							if ( isset( $cu->deleted ) && $cu->deleted ) {

								// This customer was deleted
								$customer_exists = false;

							} else {

								$cu->card = $card_data;
								$cu->save();

							}

							// No customer found
						}
						catch ( Exception $e ) {

							$customer_exists = false;

						}

					}

				}

			}

			if ( ! $customer_exists ) {

				// Create a customer first so we can retrieve them later for future payments
				$customer = Stripe_Customer::create( array(
						'description' => $purchase_data['user_email'],
						'email'       => $purchase_data['user_email'],
						'card'        => $card_data
					)
				);

				$customer_id = is_array( $customer ) ? $customer['id'] : $customer->id;

				if ( is_user_logged_in() ) {
					update_user_meta( $user->ID, give_stripe_get_customer_key(), $customer_id );
				}
			}
			if ( class_exists( 'Give_Recurring' ) && Give_Recurring()->is_purchase_recurring( $purchase_data ) && ( ! empty( $customer ) || $customer_exists ) ) {
				//handled by Give Recurring Add-on
				return;

			} elseif ( ! empty( $customer ) || $customer_exists ) {

				// Process a normal one-time charge purchase
				if ( ! isset( $give_options['stripe_preapprove_only'] ) ) {

					if ( give_stripe_is_zero_decimal_currency() ) {
						$amount = $purchase_data['price'];
					} else {
						$amount = $purchase_data['price'] * 100;
					}
					$unsupported_characters = array( '<', '>', '"', '\'' );

					$statement_descriptor = apply_filters( 'give_stripe_statement_descriptor', substr( $purchase_summary, 0, 22 ), $purchase_data );

					$statement_descriptor = str_replace( $unsupported_characters, '', $statement_descriptor );

					$charge = Stripe_Charge::create( array(
							"amount"               => $amount,
							"currency"             => give_get_currency(),
							"customer"             => $customer_id,
							"description"          => html_entity_decode( $purchase_summary, ENT_COMPAT, 'UTF-8' ),
							'statement_descriptor' => $statement_descriptor,
							'metadata'             => array(
								'email' => $purchase_data['user_info']['email']
							)
						)
					);
				}

				// record the pending payment
				$payment = give_insert_payment( $payment_data );

			} else {

				give_record_gateway_error( __( 'Customer Creation Failed', 'give-stripe' ), sprintf( __( 'Customer creation failed while processing a payment. Payment Data: %s', 'give-stripe' ), json_encode( $payment_data ) ), $payment );

			}

			//Pre-Approval Processing
			if ( $payment && ( ! empty( $customer_id ) || ! empty( $charge ) ) ) {

				if ( isset( $give_options['stripe_preapprove_only'] ) ) {
					give_update_payment_status( $payment, 'preapproval' );
					add_post_meta( $payment, '_give_stripe_customer_id', $customer_id );
				} else {
					give_update_payment_status( $payment, 'publish' );
				}

				// You should be using Stripe's API here to retrieve the invoice then confirming it's been paid
				if ( ! empty( $charge ) ) {

					give_insert_payment_note( $payment, 'Stripe Charge ID: ' . $charge->id );

					if ( function_exists( 'give_set_payment_transaction_id' ) ) {

						give_set_payment_transaction_id( $payment, $charge->id );

					}

				}
				if ( ! empty( $customer_id ) ) {
					give_insert_payment_note( $payment, 'Stripe Customer ID: ' . $customer_id );
				}

				give_send_to_success_page();

			} else {

				give_set_error( 'payment_not_recorded', __( 'Your payment could not be recorded, please contact the site administrator.', 'give-stripe' ) );

				// if errors are present, send the user back to the purchase page so they can be corrected
				give_send_back_to_checkout( '?payment-mode=stripe' );

			}
		}
		catch ( Stripe_CardError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			if ( isset( $err['message'] ) ) {
				give_set_error( 'payment_error', $err['message'] );
			} else {
				give_set_error( 'payment_error', __( 'There was an error processing your payment, please ensure you have entered your card number correctly.', 'give-stripe' ) );
			}

			give_record_gateway_error( __( 'Stripe Error', 'give-stripe' ), sprintf( __( 'There was an error while processing a Stripe payment. Payment data: %s', 'give-stripe' ), json_encode( $err ) ), 0 );
			give_send_back_to_checkout( '?payment-mode=stripe' );

		}
		catch ( Stripe_ApiConnectionError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			give_set_error( 'payment_error', __( 'There was an error processing your payment (Stripe\'s API is down), please try again', 'give-stripe' ) );
			give_record_gateway_error( __( 'Stripe Error', 'give-stripe' ), sprintf( __( 'There was an error processing your payment (Stripe\'s API was down). Error: %s', 'give-stripe' ), json_encode( $err['message'] ) ), 0 );
			give_send_back_to_checkout( '?payment-mode=stripe' );

		}
		catch ( Stripe_InvalidRequestError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			// Bad Request of some sort. Maybe Christoff was here ;)
			if ( isset( $err['message'] ) ) {
				give_set_error( 'request_error', $err['message'] );
			} else {
				give_set_error( 'request_error', __( 'The Stripe API request was invalid, please try again', 'give-stripe' ) );
			}
			give_send_back_to_checkout( '?payment-mode=stripe' );

		}
		catch ( Stripe_ApiError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			if ( isset( $err['message'] ) ) {
				give_set_error( 'request_error', $err['message'] );
			} else {
				give_set_error( 'request_error', __( 'The Stripe API request was invalid, please try again', 'give-stripe' ) );
			}
			give_set_error( 'request_error', sprintf( __( 'The Stripe API request was invalid, please try again. Error: %s', 'give-stripe' ), json_encode( $err['message'] ) ) );
			give_send_back_to_checkout( '?payment-mode=stripe' );

		}
		catch ( Stripe_AuthenticationError $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			// Authentication error. Stripe keys in settings are bad.
			if ( isset( $err['message'] ) ) {
				give_set_error( 'request_error', $err['message'] );
			} else {
				give_set_error( 'api_error', __( 'The API keys entered in settings are incorrect', 'give-stripe' ) );
			}

			give_send_back_to_checkout( '?payment-mode=stripe' );
		}
		catch ( Stripe_Error $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			// generic stripe error
			if ( isset( $err['message'] ) ) {
				give_set_error( 'request_error', $err['message'] );
			} else {
				give_set_error( 'api_error', __( 'Something went wrong.', 'give-stripe' ) );
			}
			give_send_back_to_checkout( '?payment-mode=stripe' );
		}
		catch ( Exception $e ) {
			// some sort of other error
			$body = $e->getJsonBody();
			$err  = $body['error'];
			if ( isset( $err['message'] ) ) {
				give_set_error( 'request_error', $err['message'] );
			} else {
				give_set_error( 'api_error', __( 'Something went wrong.', 'give-stripe' ) );
			}
			give_send_back_to_checkout( '?payment-mode=stripe' );

		}
	} else {
		give_send_back_to_checkout( '?payment-mode=stripe' );
	}
}

add_action( 'give_gateway_stripe', 'give_stripe_process_stripe_payment' );


/**
 * Listen for Stripe events, primarily recurring payments
 *
 * @access      public
 * @since       1.0
 * @return      void
 */

function give_stripe_event_listener() {

	if ( isset( $_GET['give-listener'] ) && $_GET['give-listener'] == 'stripe' ) {

		global $give_options;

		if ( ! class_exists( 'Stripe' ) ) {
			require_once GIVE_STRIPE_PLUGIN_DIR . '/Stripe/Stripe.php';
		}

		$secret_key = give_is_test_mode() ? trim( $give_options['test_secret_key'] ) : trim( $give_options['live_secret_key'] );

		Stripe::setApiKey( $secret_key );

		// retrieve the request's body and parse it as JSON
		$body       = @file_get_contents( 'php://input' );
		$event_json = json_decode( $body );

		// for extra security, retrieve from the Stripe API
		$event_id = $event_json->id;

		if ( isset( $event_json->id ) ) {

			status_header( 200 );

			$event = Stripe_Event::retrieve( $event_json->id );

			$invoice = $event->data->object;

			switch ( $event->type ) :

				case 'invoice.payment_succeeded' :

					if ( ! class_exists( 'Give_Recurring' ) ) {
						break;
					}

					// Process a subscription payment

					// retrieve the customer who made this payment (only for subscriptions)
					$user_id = Give_Recurring_Customer::get_user_id_by_customer_id( $invoice->customer );

					// retrieve the customer ID from WP database
					$customer_id = Give_Recurring_Customer::get_customer_id( $user_id );

					// check to confirm this is a stripe subscriber
					if ( $user_id && $customer_id ) {

						$cu = Stripe_Customer::retrieve( $customer_id );

						// Get all subscriptions for this customer
						$plans            = $cu->subscriptions->data;
						$subscriptions    = wp_list_pluck( $plans, 'plan' );
						$subscription_ids = ! empty( $subscriptions ) ? wp_list_pluck( $subscriptions, 'id' ) : array();
						$plan_data        = $invoice->lines->data;
						$invoice_plan     = wp_list_pluck( $plan_data, 'plan' );
						$invoice_plan     = array_pop( $invoice_plan );
						$plan_id          = $invoice_plan->id;

						// Make sure this charge is for the user's subscription
						if ( ! empty( $subscription_ids ) && ! in_array( $plan_id, $subscription_ids ) ) {
							die( '-3' );
						}

						// Retrieve the original payment details
						$parent_payment_id = Give_Recurring_Customer::get_customer_payment_id( $user_id );

						if ( false !== get_transient( '_give_recurring_payment_' . $parent_payment_id ) ) {
							// Store the charge for the payment.
							$charge = isset( $invoice->charge ) ? $invoice->charge : false;
							if ( $charge ) {
								give_insert_payment_note( $parent_payment_id, 'Stripe Charge ID: ' . $charge );

								if ( function_exists( 'give_set_payment_transaction_id' ) ) {
									give_set_payment_transaction_id( $parent_payment_id, $charge );
								}
							}
							die( '2' ); // This is the initial payment
						}

						try {

							// Store the payment
							Give_Recurring()->record_subscription_payment( $parent_payment_id, $invoice->total / 100, $invoice->charge );

							// Set the customer's status to active
							Give_Recurring_Customer::set_customer_status( $user_id, 'active' );

							// Calculate the customer's new expiration date
							$new_expiration = Give_Recurring_Customer::calc_user_expiration( $user_id, $parent_payment_id );

							// Set the customer's new expiration date
							Give_Recurring_Customer::set_customer_expiration( $user_id, $new_expiration );

						}
						catch ( Exception $e ) {
							die( '3' ); // Something not as expected
						}

					} else {
						die( '-4' ); // The user ID or customer ID could not be retrieved.
					}

					break;

				case 'customer.subscription.deleted' :

					if ( ! class_exists( 'Give_Recurring' ) ) {
						break;
					}

					// Process a cancellation

					// retrieve the customer who made this payment (only for subscriptions)
					$user_id = apply_filters( 'give_recurring_subscription_deleted_user_id', Give_Recurring_Customer::get_user_id_by_customer_id( $invoice->customer ), $invoice->customer );

					$parent_payment_id = apply_filters( 'give_recurring_subscription_deleted_payment_id', Give_Recurring_Customer::get_customer_payment_id( $user_id ), $user_id );

					// Set the customer's status to active
					Give_Recurring_Customer::set_customer_status( $user_id, 'cancelled' );

					give_update_payment_status( $parent_payment_id, 'cancelled' );

					break;

				case 'charge.refunded' :

					global $wpdb;

					$charge = $event->data->object;

					if ( $charge->refunded ) {

						$payment_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_give_payment_transaction_id' AND meta_value = %s LIMIT 1", $charge->id ) );

						if ( $payment_id ) {

							give_update_payment_status( $payment_id, 'refunded' );
							give_insert_payment_note( $payment_id, __( 'Charge refunded in Stripe.', 'give-stripe' ) );

						}

					}

					break;

			endswitch;

			do_action( 'give_stripe_event_' . $event->type, $event );

			die( '1' ); // Completed successfully

		} else {
			status_header( 500 );
			die( '-1' ); // Failed
		}
		die( '-2' ); // Failed
	}
}

add_action( 'init', 'give_stripe_event_listener' );


/**
 * Process refund in Stripe
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function give_stripe_process_refund( $payment_id, $new_status, $old_status ) {

	global $give_options;

	if ( empty( $_POST['give_refund_in_stripe'] ) ) {
		return;
	}

	if ( 'publish' != $old_status && 'revoked' != $old_status ) {
		return;
	}

	if ( 'refunded' != $new_status ) {
		return;
	}

	$charge_id = false;

	$notes = give_get_payment_notes( $payment_id );

	foreach ( $notes as $note ) {
		if ( preg_match( '/^Stripe Charge ID: ([^\s]+)/', $note->comment_content, $match ) ) {
			$charge_id = $match[1];
			break;
		}
	}

	// Bail if no charge ID was found
	if ( empty( $charge_id ) ) {
		return;
	}

	if ( ! class_exists( 'Stripe' ) ) {
		require_once GIVE_STRIPE_PLUGIN_DIR . '/Stripe/Stripe.php';
	}

	$secret_key = give_is_test_mode() ? trim( $give_options['test_secret_key'] ) : trim( $give_options['live_secret_key'] );

	Stripe::setApiKey( $secret_key );

	$ch = Stripe_Charge::retrieve( $charge_id );


	try {
		$ch->refund();

		give_insert_payment_note( $payment_id, __( 'Charge refunded in Stripe', 'give-stripe' ) );

	}
	catch ( Exception $e ) {

		// some sort of other error
		$body = $e->getJsonBody();
		$err  = $body['error'];

		if ( isset( $err['message'] ) ) {
			$error = $err['message'];
		} else {
			$error = __( 'Something went wrong while refunding the Charge in Stripe.', 'give-stripe' );
		}

		wp_die( $error, __( 'Error', 'give-stripe' ), array( 'response' => 400 ) );

	}

	do_action( 'give_stripe_payment_refunded', $payment_id );


}

add_action( 'give_update_payment_status', 'give_stripe_process_refund', 200, 3 );


/**
 * Process the POST Data for the Credit Card Form, if a token wasn't supplied
 *
 * @since  1.0
 *
 * @param array $purchase_data
 *
 * @return array The credit card data from the $_POST
 */
function give_stripe_process_post_data( $purchase_data ) {
	if ( ! isset( $_POST['card_name'] ) || strlen( trim( $_POST['card_name'] ) ) == 0 ) {
		give_set_error( 'no_card_name', __( 'Please enter a name for the credit card.', 'give-stripe' ) );
	}

	if ( ! isset( $_POST['card_number'] ) || strlen( trim( $_POST['card_number'] ) ) == 0 ) {
		give_set_error( 'no_card_number', __( 'Please enter a credit card number.', 'give-stripe' ) );
	}

	if ( ! isset( $_POST['card_cvc'] ) || strlen( trim( $_POST['card_cvc'] ) ) == 0 ) {
		give_set_error( 'no_card_cvc', __( 'Please enter a CVC/CVV for the credit card.', 'give-stripe' ) );
	}

	if ( ! isset( $_POST['card_exp_month'] ) || strlen( trim( $_POST['card_exp_month'] ) ) == 0 ) {
		give_set_error( 'no_card_exp_month', __( 'Please enter a expiration month.', 'give-stripe' ) );
	}

	if ( ! isset( $_POST['card_exp_year'] ) || strlen( trim( $_POST['card_exp_year'] ) ) == 0 ) {
		give_set_error( 'no_card_exp_year', __( 'Please enter a expiration year.', 'give-stripe' ) );
	}

	$card_data = array(
		'number'          => $purchase_data['card_info']['card_number'],
		'name'            => $purchase_data['card_info']['card_name'],
		'exp_month'       => $purchase_data['card_info']['card_exp_month'],
		'exp_year'        => $purchase_data['card_info']['card_exp_year'],
		'cvc'             => $purchase_data['card_info']['card_cvc'],
		'address_line1'   => $purchase_data['card_info']['card_address'],
		'address_line2'   => $purchase_data['card_info']['card_address_2'],
		'address_city'    => $purchase_data['card_info']['card_city'],
		'address_zip'     => $purchase_data['card_info']['card_zip'],
		'address_state'   => $purchase_data['card_info']['card_state'],
		'address_country' => $purchase_data['card_info']['card_country']
	);

	return $card_data;
}
