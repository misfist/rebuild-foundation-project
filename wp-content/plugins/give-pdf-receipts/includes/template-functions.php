<?php
/**
 * Template Functions
 *
 * All the template functions for the PDF receipt when they are being built or
 * generated.
 *
 * @package Give PDF Receipts
 * @since   1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Settings
 *
 * Gets the settings for PDF Receipts plugin if they exist.
 *
 * @since 1.0
 *
 * @param object $give_pdf PDF receipt object
 * @param string $setting  Setting name
 *
 * @return string Returns option if it exists.
 */
function give_pdf_get_settings( $give_pdf, $setting ) {
	global $give_options;

	$give_pdf_payment = get_post( $_GET['transaction_id'] );

	if ( 'name' == $setting ) {
		if ( isset( $give_options['give_pdf_name'] ) ) {
			return $give_options['give_pdf_name'];
		}
	}

	if ( 'addr_line1' == $setting ) {
		if ( isset( $give_options['give_pdf_address_line1'] ) ) {
			return $give_options['give_pdf_address_line1'];
		}
	}

	if ( 'addr_line2' == $setting ) {
		if ( isset( $give_options['give_pdf_address_line2'] ) ) {
			return $give_options['give_pdf_address_line2'];
		}
	}

	if ( 'city_state_zip' == $setting ) {
		if ( isset( $give_options['give_pdf_address_city_state_zip'] ) ) {
			return $give_options['give_pdf_address_city_state_zip'];
		}
	}

	if ( 'email' == $setting ) {
		if ( isset( $give_options['give_pdf_email_address'] ) ) {
			return $give_options['give_pdf_email_address'];
		}
	}

	if ( 'notes' == $setting ) {
		if ( isset( $give_options['give_pdf_additional_notes'] ) && ! empty( $give_options['give_pdf_additional_notes'] ) ) {
			$give_pdf_additional_notes = $give_options['give_pdf_additional_notes'];
			$give_pdf_additional_notes = str_replace( '{page}', 'Page' . $give_pdf->getPage(), $give_pdf_additional_notes );
			$give_pdf_additional_notes = str_replace( '{sitename}', get_bloginfo( 'name' ), $give_pdf_additional_notes );
			$give_pdf_additional_notes = str_replace( '{today}', date_i18n( get_option( 'date_format' ), time() ), $give_pdf_additional_notes );
			$give_pdf_additional_notes = str_replace( '{date}', date_i18n( get_option( 'date_format' ), strtotime( $give_pdf_payment->post_date ) ), $give_pdf_additional_notes );
			$give_pdf_additional_notes = str_replace( '{receipt_id}', give_pdf_get_payment_number( $give_pdf_payment->ID ), $give_pdf_additional_notes );
			$give_pdf_additional_notes = strip_tags( $give_pdf_additional_notes );
			$give_pdf_additional_notes = stripslashes_deep( html_entity_decode( $give_pdf_additional_notes, ENT_COMPAT, 'UTF-8' ) );

			return $give_pdf_additional_notes;
		}
	}

	return '';
}

/**
 * Calculate Line Heights
 *
 * Calculates the line heights for the 'To' block
 *
 * @since 1.0
 *
 * @param string $setting Setting name.
 *
 * @return string Returns line height.
 */
function give_pdf_calculate_line_height( $setting ) {
	global $give_options;

	if ( empty( $setting ) ) {
		return 0;
	} else {
		return 6;
	}
}

/**
 *
 * Retrieve the payment number
 *
 * @description If sequential order numbers are enabled (EDD 2.0+), this returns the order numbeer
 *
 * @since       1.0
 *
 * @param int $payment_id
 *
 * @return int|string
 */
function give_pdf_get_payment_number( $payment_id = 0 ) {
	if ( function_exists( 'give_get_payment_number' ) ) {
		return give_get_payment_number( $payment_id );
	} else {
		return $payment_id;
	}
}
