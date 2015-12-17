<?php
/**
 * Traditional PDF Receipt Template
 *
 * Builds and renders the traditional PDF receipt template .
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

function give_pdf_template_traditional( $give_pdf, $give_pdf_payment, $give_pdf_payment_meta, $give_pdf_buyer_info, $give_pdf_payment_gateway, $give_pdf_payment_method, $address_line_2_line_height, $company_name, $give_pdf_payment_date, $give_pdf_payment_status ) {
	global $give_options;

	$give_pdf->AddFont( 'times', '' );
	$give_pdf->AddFont( 'times', 'B' );
	$give_pdf->AddFont( 'times', 'BI' );
	$give_pdf->AddFont( 'times', 'I' );

	$font  = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freeserif' : 'times';
	$fontb = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freesans' : 'helvetica';

	$give_pdf->SetMargins( 8, 20, 8 );
	$give_pdf->SetX( 20 );

	$give_pdf->AddPage();

	$give_pdf->Image( GIVE_PDF_PLUGIN_URL . '/templates/traditional/header_background.jpg', 8, 20, 194, 32, 'JPEG', false, 'LTR', false, - 72, 'L' );

	$give_pdf->SetFont( $font, '', 22 );

	$give_pdf->SetTextColor( 255, 255, 255 );

	$give_pdf->SetY( 20 );

	if ( isset( $give_options['give_pdf_logo_upload'] ) && ! empty( $give_options['give_pdf_logo_upload'] ) ) {
		$give_pdf->Image( $give_options['give_pdf_logo_upload'], 8.5, 26, '', '11', '', false, 'LTR', false, 96 );
	} else {
		$give_pdf->SetY( 26 );
		$give_pdf->SetFont( $fontb, '', 22 );
		$give_pdf->SetTextColor( 255, 255, 255 );
		$give_pdf->Cell( 0, 0, $company_name, 0, 2, 'L', false );
	}

	$give_pdf->SetY( 42 );
	$give_pdf->Cell( 0, 0, __( 'DONATION RECEIPT', 'give_pdf' ), 0, 2, 'L', false );

	$give_pdf->SetXY( 8, 57 );

	$give_pdf->SetTextColor( 50, 50, 50 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 0, 6, strtoupper( $give_pdf_payment_date ), 0, 2, 'R', false );

	$give_pdf->SetY( 57 );

	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 40, 6, __( 'DONATION ID', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetFont( $font, '', 10 );
	$give_pdf->Cell( 0, 6, give_pdf_get_payment_number( $give_pdf_payment->ID ), 0, 2, 'L', false );
	$give_pdf->SetX( 8 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 40, 6, __( 'TRANSACTION KEY', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetFont( $font, '', 10 );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_meta['key'], 0, 2, 'L', false );
	$give_pdf->SetX( 8 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 40, 6, __( 'DONATION STATUS', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetFont( $font, '', 10 );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_status, 0, 2, 'L', false );
	$give_pdf->SetX( 8 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 40, 6, __( 'PAYMENT METHOD', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetFont( $font, '', 10 );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_method, 0, 2, 'L', false );


	$give_pdf->SetXY( 8, 90 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 0, 12, __( 'FROM:', 'give_pdf' ), 0, 2, 'R', false );
	$give_pdf->SetFont( $font, '', 10 );

	if ( ! empty( $give_options['give_pdf_name'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_name'] ), give_pdf_get_settings( $give_pdf, 'name' ), 0, 2, 'R', false );
	}
	if ( ! empty( $give_options['give_pdf_address_line1'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_line1'] ), give_pdf_get_settings( $give_pdf, 'addr_line1' ), 0, 2, 'R', false );
	}

	if ( ! empty( $give_options['give_pdf_address_line2'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_line2'] ), give_pdf_get_settings( $give_pdf, 'addr_line2' ), 0, 2, 'R', false );
	}
	if ( ! empty( $give_options['give_pdf_address_city_state_zip'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_city_state_zip'] ), give_pdf_get_settings( $give_pdf, 'city_state_zip' ), 0, 2, 'R', false );
	}
	if ( ! empty( $give_options['give_pdf_email_address'] ) ) {
		$give_pdf->SetTextColor( 41, 102, 152 );
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_email_address'] ), give_pdf_get_settings( $give_pdf, 'email' ), 0, 2, 'R', false );
	}
	if ( isset( $give_options['give_pdf_url'] ) && $give_options['give_pdf_url'] ) {
		$give_pdf->SetTextColor( 41, 102, 152 );
		$give_pdf->Cell( 0, 6, get_option( 'siteurl' ), 0, 2, 'R', false );
	}
	$give_pdf->SetTextColor( 50, 50, 50 );

	$give_pdf->Ln( 12 );

	$give_pdf->Ln();
	$give_pdf->SetXY( 8, 90 );
	$give_pdf->SetFont( $font, 'B', 10 );
	$give_pdf->Cell( 0, 12, __( 'TO:', 'give_pdf' ), 0, 2, 'L', false );
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
			$countries = edd_get_country_list();
			$country   = isset( $countries[ $give_pdf_buyer_info['address']['country'] ] ) ? $countries[ $give_pdf_buyer_info['address']['country'] ] : $give_pdf_buyer_info['address']['country'];
			$give_pdf->Cell( 0, 6, $country, 0, 2, 'L', false );
		}
	}

	$give_pdf->Ln( 35 );

	$give_pdf->SetX( 8 );


	$give_pdf->SetDrawColor( 0, 0, 0 );
	$give_pdf->SetFont( $font, 'B', 11 );
	$give_pdf->Cell( 193, 8, __( 'RECEIPT ITEMS', 'give_pdf' ), 1, 2, 'C', false );

	$give_pdf->Ln( 0.2 );

	$give_pdf->SetX( 8 );

	$give_pdf->SetDrawColor( 0, 0, 0 );
	$give_pdf->SetFont( $font, '', 9 );

	$give_pdf->Cell( 150, 7, __( 'Donation Name', 'give_pdf' ), 'BRL', 0, 'C', false );
	$give_pdf->Cell( 43, 7, __( 'Donation Total', 'give_pdf' ), 'BR', 0, 'C', false );

	$give_pdf->Ln( 0.2 );

	$give_pdf->Ln();

	if ( $give_pdf_payment_meta ):

		$give_pdf->SetX( 8 );

		$give_pdf->SetDrawColor( 0, 0, 0 );

		$give_pdf->SetX( 8 );

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


		$dimensions = $give_pdf->getPageDimensions();
		$has_border = false;
		$linecount  = $give_pdf->getNumLines( $give_pdf_donation_title, 82 );

		$give_pdf->MultiCell( 150, $linecount * 8, $give_pdf_donation_title, 'L', 'C', false, 0, 8 );
		$give_pdf->Cell( 43, 8, $total, 'R', 2, 'C', false );


		$give_pdf->SetX( 8 );


		$fees = give_get_payment_fees( $give_pdf_payment->ID );
		if ( ! empty ( $fees ) ) :
			foreach ( $fees as $fee ) :
				$fee_amount = html_entity_decode( give_currency_filter( $fee['amount'] ), ENT_COMPAT, 'UTF-8' );

				$give_pdf->SetX( 8 );
				$give_pdf->Cell( 150, 8, $fee['label'], 'TLBR', 0, 'R', false );
				$give_pdf->Cell( 43, 8, $fee_amount, 'BTR', 2, 'C', true );
			endforeach;
		endif;


		$give_pdf->SetX( 8 );
		$give_pdf->SetFont( $font, 'B', 11 );
		$give_pdf->Cell( 150, 10, __( 'Total Donation', 'give_pdf' ) . '  ', 'BLRT', 0, 'R', false );
		$give_pdf->Cell( 43, 10, $total, 'BRT', 2, 'C', false );

		$give_pdf->Ln( 10 );

		if ( isset( $give_options['give_pdf_additional_notes'] ) && ! empty ( $give_options['give_pdf_additional_notes'] ) ) {

			$give_pdf->SetX( 8 );
			$give_pdf->SetFont( $font, '', 13 );
			$give_pdf->Cell( 0, 6, __( 'ADDITIONAL NOTES:', 'give_pdf' ), 0, 2, 'L', false );
			$give_pdf->Ln( 2 );

			$give_pdf->SetX( 8 );
			$give_pdf->SetFont( $font, '', 10 );
			$give_pdf->MultiCell( 0, 6, give_pdf_get_settings( $give_pdf, 'notes' ), 0, 'L', false );

		}

		$give_pdf->Ln( 10 );

		$give_pdf->SetFont( $font, 'B', 10 );
		$give_pdf->Cell( 0, 8, __( 'THANK YOU FOR YOUR DONATION!', 'give_pdf' ), 0, 2, 'C', false );

	endif;

}

add_action( 'give_pdf_template_traditional', 'give_pdf_template_traditional', 10, 10 );
