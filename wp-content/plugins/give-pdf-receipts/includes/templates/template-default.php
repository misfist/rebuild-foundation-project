<?php
/**
 * Default PDF Receipt Template
 *
 * Builds and renders the default PDF receipt template .
 *
 * @since 1.0
 *
 * @uses  HTML2PDF
 * @uses  TCPDF
 *
 * @param object $give_pdf                 PDF Receipt Object
 * @param object $give_pdf_payment         Payment Data Object
 * @param array  $give_pdf_payment_meta    Payment Meta
 * @param array  $give_pdf_buyer_info      Buyer Info
 * @param string $give_pdf_payment_gateway Payment Gateway
 * @param string $give_pdf_payment_method  Payment Method
 * @param string $company_name             Company Name
 * @param string $give_pdf_payment_date    Payment Date
 * @param        string                    give_pdf_payment_status Payment Status
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function give_pdf_template_default( $give_pdf, $give_pdf_payment, $give_pdf_payment_meta, $give_pdf_buyer_info, $give_pdf_payment_gateway, $give_pdf_payment_method, $address_line_2_line_height, $company_name, $give_pdf_payment_date, $give_pdf_payment_status ) {
	global $give_options;

	$give_pdf->SetMargins( 8, 8, 8 );
	$give_pdf->SetX( 8 );

	$give_pdf->AddPage();

	$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freesans' : 'Helvetica';

	$give_pdf->Ln( 5 );

	if ( isset( $give_options['give_pdf_logo_upload'] ) && ! empty( $give_options['give_pdf_logo_upload'] ) ) {
		$give_pdf->Image( $give_options['give_pdf_logo_upload'], 8, 20, '', '11', '', false, 'LTR', false, 96 );
	} else {
		$give_pdf->SetFont( $font, '', 22 );
		$give_pdf->SetTextColor( 50, 50, 50 );
		$give_pdf->Cell( 0, 0, $company_name, 0, 2, 'L', false );
	}

	$give_pdf->SetFont( $font, '', 18 );
	$give_pdf->SetY( 45 );
	$give_pdf->Cell( 0, 0, __( 'Donation Receipt', 'give_pdf' ), 0, 2, 'L', false );

	$give_pdf->SetXY( 8, 60 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 0, 6, __( 'From', 'give_pdf' ), 0, 2, 'L', false );
	$give_pdf->SetFont( $font, '', 10 );
	if ( ! empty( $give_options['give_pdf_name'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_name'] ), give_pdf_get_settings( $give_pdf, 'name' ), 0, 2, 'L', false );
	}
	if ( ! empty( $give_options['give_pdf_address_line1'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_line1'] ), give_pdf_get_settings( $give_pdf, 'addr_line1' ), 0, 2, 'L', false );
	}
	if ( ! empty( $give_options['give_pdf_address_line2'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_line2'] ), give_pdf_get_settings( $give_pdf, 'addr_line2' ), 0, 2, 'L', false );
	}
	if ( ! empty( $give_options['give_pdf_address_city_state_zip'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_city_state_zip'] ), give_pdf_get_settings( $give_pdf, 'city_state_zip' ), 0, 2, 'L', false );
	}
	if ( ! empty( $give_options['give_pdf_email_address'] ) ) {
		$give_pdf->SetTextColor( 41, 102, 152 );
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_email_address'] ), give_pdf_get_settings( $give_pdf, 'email' ), 0, 2, 'L', false );
	}
	if ( isset( $give_options['give_pdf_url'] ) && $give_options['give_pdf_url'] ) {
		$give_pdf->Cell( 0, 6, get_option( 'siteurl' ), 0, 2, 'L', false );
	}
	$give_pdf->SetTextColor( 50, 50, 50 );

	$give_pdf->Ln( 12 );

	$give_pdf->Ln();
	$give_pdf->SetXY( 60, 60 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 0, 6, __( 'To', 'give_pdf' ), 0, 2, 'L', false );
	$give_pdf->SetFont( $font, '', 10 );
	$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_pdf_buyer_info['first_name'] ), $give_pdf_buyer_info['first_name'] . ' ' . $give_pdf_buyer_info['last_name'], 0, 2, 'L', false );
	$give_pdf->SetTextColor( 41, 102, 152 );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_meta['email'], 0, 2, 'L', false );
	$give_pdf->SetTextColor( 50, 50, 50 );

	if ( ! empty( $give_pdf_buyer_info['address'] ) ) {
		$give_pdf->Cell( 0, 6, $give_pdf_buyer_info['address']['line1'], 0, 2, 'L', false );
		if ( ! empty( $give_pdf_buyer_info['address']['line2'] ) ) {
			$give_pdf->Cell( 0, 0, $give_pdf_buyer_info['address']['line2'], 0, 2, 'L', false );
		}
		$give_pdf->Cell( 0, 6, $give_pdf_buyer_info['address']['city'] . ' ' . $give_pdf_buyer_info['address']['state'] . ' ' . $give_pdf_buyer_info['address']['zip'], 0, 2, 'L', false );
		if ( ! empty( $give_pdf_buyer_info['address']['country'] ) ) {
			$countries = give_get_country_list();
			$country   = isset( $countries[ $give_pdf_buyer_info['address']['country'] ] ) ? $countries[ $give_pdf_buyer_info['address']['country'] ] : $give_pdf_buyer_info['address']['country'];
			$give_pdf->Cell( 0, 6, $country, 0, 2, 'L', false );
		}
	}

	$give_pdf->Ln( 5 );

	$give_pdf->SetX( 60 );
	$give_pdf->SetTextColor( 110, 110, 110 );
	$give_pdf->Cell( 30, 6, __( 'Receipt Date', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( 50, 50, 50 );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_date, 0, 2, 'L', false );

	$give_pdf->SetX( 60 );

	$give_pdf->SetTextColor( 110, 110, 110 );
	$give_pdf->Cell( 30, 6, __( 'Receipt ID', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( 50, 50, 50 );
	$give_pdf->Cell( 0, 6, give_pdf_get_payment_number( $give_pdf_payment->ID ), 0, 2, 'L', false );
	$give_pdf->SetX( 60 );
	$give_pdf->SetTextColor( 110, 110, 110 );
	$give_pdf->Cell( 30, 6, __( 'Transaction Key', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( 50, 50, 50 );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_meta['key'], 0, 2, 'L', false );
	$give_pdf->SetX( 60 );
	$give_pdf->SetTextColor( 110, 110, 110 );
	$give_pdf->Cell( 30, 6, __( 'Donation Status', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( 50, 50, 50 );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_status, 0, 2, 'L', false );
	$give_pdf->SetX( 60 );
	$give_pdf->SetTextColor( 110, 110, 110 );
	$give_pdf->Cell( 30, 6, __( 'Payment Method', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( 50, 50, 50 );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_method, 0, 2, 'L', false );

	$give_pdf->Ln( 5 );

	$give_pdf->SetX( 61 );

	$give_pdf->SetFillColor( 224, 224, 224 );
	$give_pdf->SetDrawColor( 209, 209, 209 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 140, 8, __( 'Receipt Items', 'give_pdf' ), 1, 2, 'C', true );

	$give_pdf->Ln( 0.2 );

	$give_pdf->SetX( 61 );

	$give_pdf->SetDrawColor( 194, 221, 231 );
	$give_pdf->SetFillColor( 238, 238, 238 );
	$give_pdf->SetFont( $font, '', 9 );

	$give_pdf->Cell( 102, 7, __( 'Donation Name', 'give_pdf' ), 'BL', 0, 'C', false );
	$give_pdf->Cell( 38, 7, __( 'Donation Total', 'give_pdf' ), 'BR', 0, 'C', false );

	$give_pdf->Ln( 0.2 );


	$give_pdf->Ln();

	if ( $give_pdf_payment_meta ) :
		$give_pdf->SetX( 61 );

		$give_pdf->SetDrawColor( 238, 238, 238 );

		$give_pdf->SetX( 61 );

		$give_pdf->SetFont( $font, '', 10 );

		$payment_id       = $give_pdf_payment->ID;
		$item             = get_post( $payment_id );
		$payment_meta     = give_get_payment_meta( $payment_id );
		$user_info        = give_get_payment_meta_user_info( $payment_id );
		$user_id          = give_get_payment_user_id( $payment_id );
		$payment_date     = strtotime( $item->post_date );
		$price_id         = isset( $give_pdf_payment_meta['price_id'] ) ? $give_pdf_payment_meta['price_id'] : null;
		$give_pdf_form_id = isset( $payment_meta['form_id'] ) ? $payment_meta['form_id'] : '';
		$user_info        = maybe_unserialize( $give_pdf_payment_meta['user_info'] );
		$total            = html_entity_decode( give_currency_filter( give_format_amount( give_get_payment_amount( $give_pdf_payment->ID ) ) ), ENT_COMPAT, 'UTF-8' );


		$give_pdf_donation_title = html_entity_decode( get_the_title( $give_pdf_form_id ), ENT_COMPAT, 'UTF-8' );

		if ( isset( $price_id ) && ! empty( $price_id ) ) {
			$give_pdf_donation_title .= ' - ' . give_get_price_option_name( $give_pdf_form_id, $price_id, $payment_id );
		}

		$give_pdf_donation_price = html_entity_decode( give_currency_filter( give_format_amount( give_get_payment_amount( $payment_id ) ) ), ENT_COMPAT, 'UTF-8' );

		$dimensions = $give_pdf->getPageDimensions();
		$has_border = false;
		$linecount  = $give_pdf->getNumLines( $give_pdf_donation_title, 82 );

		$give_pdf->MultiCell( 102, $linecount * 5, $give_pdf_donation_title, 0, 'L', false, 0, 61 );
		$give_pdf->SetFillColor( 250, 250, 250 );
		$give_pdf->Cell( 38, $linecount * 5, $give_pdf_donation_price, 'B', 2, 'R', true );
		$give_pdf->SetX( 61 );
		$give_pdf->Cell( 102, 8, '', 'T' );

		$give_pdf->Ln( 5 );
		$give_pdf->SetX( 61 );

		$give_pdf->SetFillColor( 224, 224, 224 );
		$give_pdf->SetDrawColor( 209, 209, 209 );
		$give_pdf->SetFont( $font, 'B', 10 );
		$give_pdf->Cell( 140, 8, __( 'Receipt Total', 'give_pdf' ), 1, 2, 'C', true );

		$give_pdf->Ln( 0.2 );

		$give_pdf->SetDrawColor( 238, 238, 238 );
		$give_pdf->SetFillColor( 250, 250, 250 );


		$give_pdf->SetX( 61 );

		$fees = give_get_payment_fees( $give_pdf_payment->ID );
		if ( ! empty ( $fees ) ) :
			foreach ( $fees as $fee ) :
				$fee_amount = html_entity_decode( give_currency_filter( $fee['amount'] ), ENT_COMPAT, 'UTF-8' );

				if ( function_exists( 'iconv' ) ) {
					// Ensure characters like euro; are properly converted. See GithuB issue #472 and #1570
					//$fee_amount = iconv('UTF-8', 'windows-1252', $fee_amount );
				}

				$give_pdf->SetX( 61 );
				$give_pdf->Cell( 102, 8, $fee['label'], 'B', 0, 'L', false );
				$give_pdf->Cell( 38, 8, $fee_amount, 'B', 2, 'R', true );
			endforeach;
		endif;
		$give_pdf->SetX( 61 );


		$give_pdf->SetX( 61 );
		$give_pdf->SetFont( $font, 'B', 11 );
		$give_pdf->Cell( 102, 10, __( 'Total Donation', 'give_pdf' ), 'B', 0, 'L', false );
		$give_pdf->Cell( 38, 10, $total, 'B', 2, 'R', true );

		$give_pdf->Ln( 10 );

		if ( isset( $give_options['give_pdf_additional_notes'] ) && ! empty ( $give_options['give_pdf_additional_notes'] ) ) {

			$give_pdf->SetX( 60 );
			$give_pdf->SetFont( $font, '', 13 );
			$give_pdf->Cell( 0, 6, __( 'Additional Notes', 'give_pdf' ), 0, 2, 'L', false );
			$give_pdf->Ln( 2 );

			$give_pdf->SetX( 60 );
			$give_pdf->SetFont( $font, '', 10 );
			$give_pdf->MultiCell( 0, 6, give_pdf_get_settings( $give_pdf, 'notes' ), 0, 'L', false );

		}

	endif;
}

add_action( 'give_pdf_template_default', 'give_pdf_template_default', 10, 10 );
