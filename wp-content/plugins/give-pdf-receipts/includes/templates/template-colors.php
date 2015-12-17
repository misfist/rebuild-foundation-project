<?php
/**
 * Different Colored PDF Receipt Template
 *
 * Builds and renders the different colored PDF receipt template .
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

function give_pdf_template_colours( $give_pdf, $give_pdf_payment, $give_pdf_payment_meta, $give_pdf_buyer_info, $give_pdf_payment_gateway, $give_pdf_payment_method, $address_line_2_line_height, $company_name, $give_pdf_payment_date, $give_pdf_payment_status ) {
	global $give_options;

	if ( ! isset( $give_options['give_pdf_templates'] ) ) {
		$give_options['give_pdf_templates'] = 'default';
	}

	switch ( $give_options['give_pdf_templates'] ) {
		case 'blue':
			$colors = array(
				'body'     => array( 8, 75, 110 ),
				'emphasis' => array( 71, 155, 198 ),
				'title'    => array( 0, 127, 192 ),
				'header'   => array( 202, 226, 238 ),
				'sub'      => array( 234, 242, 245 ),
				'border'   => array( 166, 205, 226 ),
				'notes'    => array( 7, 46, 66 )
			);
			break;

		case 'red':
			$colors = array(
				'body'     => array( 110, 8, 8 ),
				'emphasis' => array( 198, 71, 71 ),
				'title'    => array( 192, 0, 0 ),
				'header'   => array( 238, 202, 202 ),
				'sub'      => array( 245, 243, 243 ),
				'border'   => array( 226, 166, 166 ),
				'notes'    => array( 66, 7, 7 )
			);
			break;

		case 'green':
			$colors = array(
				'body'     => array( 8, 110, 39 ),
				'emphasis' => array( 71, 198, 98 ),
				'title'    => array( 0, 192, 68 ),
				'header'   => array( 202, 238, 212 ),
				'sub'      => array( 243, 245, 244 ),
				'border'   => array( 166, 226, 179 ),
				'notes'    => array( 7, 66, 28 )
			);
			break;

		case 'orange':
			$colors = array(
				'body'     => array( 110, 54, 8 ),
				'emphasis' => array( 198, 134, 71 ),
				'title'    => array( 192, 81, 0 ),
				'header'   => array( 238, 219, 202 ),
				'sub'      => array( 245, 245, 243 ),
				'border'   => array( 226, 224, 166 ),
				'notes'    => array( 65, 66, 7 )
			);
			break;

		case 'yellow':
			$colors = array(
				'body'     => array( 109, 110, 8 ),
				'emphasis' => array( 197, 198, 71 ),
				'title'    => array( 192, 190, 0 ),
				'header'   => array( 238, 238, 202 ),
				'sub'      => array( 245, 244, 243 ),
				'border'   => array( 226, 193, 166 ),
				'notes'    => array( 66, 38, 7 )
			);
			break;

		case 'purple':
			$colors = array(
				'body'     => array( 66, 8, 110 ),
				'emphasis' => array( 137, 71, 198 ),
				'title'    => array( 72, 0, 192 ),
				'header'   => array( 208, 202, 238 ),
				'sub'      => array( 244, 243, 245 ),
				'border'   => array( 189, 166, 226 ),
				'notes'    => array( 35, 7, 66 )
			);
			break;

		case 'pink':
			$colors = array(
				'body'     => array( 110, 8, 82 ),
				'emphasis' => array( 198, 71, 152 ),
				'title'    => array( 92, 0, 65 ),
				'header'   => array( 238, 202, 232 ),
				'sub'      => array( 245, 243, 245 ),
				'border'   => array( 226, 166, 213 ),
				'notes'    => array( 66, 7, 51 )
			);
			break;
	} // end switch

	$give_pdf->AddFont( 'opensans', '' );
	$give_pdf->AddFont( 'opensansb', '' );

	$font  = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freesans' : 'opensans';
	$fontb = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freesans' : 'opensansb';

	$give_pdf->SetMargins( 8, 8, 8 );
	$give_pdf->SetX( 8 );

	$give_pdf->AddPage();

	$give_pdf->Ln( 5 );

	if ( isset( $give_options['give_pdf_logo_upload'] ) && ! empty( $give_options['give_pdf_logo_upload'] ) ) {
		$give_pdf->Image( $give_options['give_pdf_logo_upload'], 8, 20, '', '11', '', false, 'LTR', false, 96 );
	} else {
		$give_pdf->SetFont( $font, '', 22 );
		$give_pdf->SetTextColor( 50, 50, 50 );
		$give_pdf->Cell( 0, 0, $company_name, 0, 2, 'L', false );
	} // end if

	$give_pdf->SetFont( $font, '', 18 );
	$give_pdf->SetTextColor( $colors['title'][0], $colors['title'][1], $colors['title'][2] );
	$give_pdf->SetY( 45 );
	$give_pdf->Cell( 0, 0, __( 'Receipt', 'give_pdf' ), 0, 2, 'L', false );

	$give_pdf->SetTextColor( $colors['body'][0], $colors['body'][1], $colors['body'][2] );
	$give_pdf->SetXY( 8, 60 );
	$give_pdf->SetFont( $fontb, '', 10 );
	$give_pdf->Cell( 0, 6, __( 'From', 'give_pdf' ), 0, 2, 'L', false );

	$give_pdf->SetFont( $font, '', 10 );

	if ( ! empty( $give_options['give_pdf_name'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_name'] ), give_pdf_get_settings( $give_pdf, 'name' ), 0, 2, 'L', false );
	} // end if

	if ( ! empty( $give_options['give_pdf_address_line1'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_line1'] ), give_pdf_get_settings( $give_pdf, 'addr_line1' ), 0, 2, 'L', false );
	} // end if

	if ( ! empty( $give_options['give_pdf_address_line2'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_line2'] ), give_pdf_get_settings( $give_pdf, 'addr_line2' ), 0, 2, 'L', false );
	} // end if

	if ( ! empty( $give_options['give_pdf_address_city_state_zip'] ) ) {
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_address_city_state_zip'] ), give_pdf_get_settings( $give_pdf, 'city_state_zip' ), 0, 2, 'L', false );
	} // end if

	if ( ! empty( $give_options['give_pdf_email_address'] ) ) {
		$give_pdf->SetTextColor( 41, 102, 152 );
		$give_pdf->Cell( 0, give_pdf_calculate_line_height( $give_options['give_pdf_email_address'] ), give_pdf_get_settings( $give_pdf, 'email' ), 0, 2, 'L', false );
	} // end if

	if ( isset( $give_options['give_pdf_url'] ) && $give_options['give_pdf_url'] ) {
		$give_pdf->SetTextColor( 41, 102, 152 );
		$give_pdf->Cell( 0, 6, get_option( 'siteurl' ), 0, 2, 'L', false );
	} // end if

	$give_pdf->SetTextColor( $colors['body'][0], $colors['body'][1], $colors['body'][2] );

	$give_pdf->Ln( 13 );

	$give_pdf->SetXY( 60, 60 );
	$give_pdf->SetFont( $fontb, '', 10 );
	$give_pdf->Cell( 0, 6, __( 'To', 'give_pdf' ), 0, 2, 'L', false );
	$give_pdf->SetFont( $font, '', 10 );
	$give_pdf->Cell( 0, 6, $give_pdf_buyer_info['first_name'] . ' ' . $give_pdf_buyer_info['last_name'], 0, 2, 'L', false );
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
	} // end if

	$give_pdf->Ln( 5 );
	$give_pdf->SetX( 60 );
	$give_pdf->SetTextColor( $colors['emphasis'][0], $colors['emphasis'][1], $colors['emphasis'][2] );
	$give_pdf->Cell( 30, 6, __( 'Receipt Date', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( $colors['body'][0], $colors['body'][1], $colors['body'][2] );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_date, 0, 2, 'L', false );

	$give_pdf->SetX( 60 );

	$give_pdf->SetTextColor( $colors['emphasis'][0], $colors['emphasis'][1], $colors['emphasis'][2] );
	$give_pdf->Cell( 30, 6, __( 'Receipt ID', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( $colors['body'][0], $colors['body'][1], $colors['body'][2] );
	$give_pdf->Cell( 0, 6, give_pdf_get_payment_number( $give_pdf_payment->ID ), 0, 2, 'L', false );
	$give_pdf->SetX( 60 );
	$give_pdf->SetTextColor( $colors['emphasis'][0], $colors['emphasis'][1], $colors['emphasis'][2] );
	$give_pdf->Cell( 30, 6, __( 'Transaction Key', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( $colors['body'][0], $colors['body'][1], $colors['body'][2] );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_meta['key'], 0, 2, 'L', false );
	$give_pdf->SetX( 60 );
	$give_pdf->SetTextColor( $colors['emphasis'][0], $colors['emphasis'][1], $colors['emphasis'][2] );
	$give_pdf->Cell( 30, 6, __( 'Donation Status', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( $colors['body'][0], $colors['body'][1], $colors['body'][2] );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_status, 0, 2, 'L', false );
	$give_pdf->SetX( 60 );
	$give_pdf->SetTextColor( $colors['emphasis'][0], $colors['emphasis'][1], $colors['emphasis'][2] );
	$give_pdf->Cell( 30, 6, __( 'Donation Method', 'give_pdf' ), 0, 0, 'L', false );
	$give_pdf->SetTextColor( $colors['body'][0], $colors['body'][1], $colors['body'][2] );
	$give_pdf->Cell( 0, 6, $give_pdf_payment_method, 0, 2, 'L', false );

	$give_pdf->Ln( 5 );
	$give_pdf->SetX( 61 );

	$give_pdf->SetFillColor( $colors['header'][0], $colors['header'][1], $colors['header'][2] );
	$give_pdf->SetDrawColor( $colors['border'][0], $colors['border'][1], $colors['border'][2] );
	$give_pdf->SetFont( $fontb, '', 10 );
	$give_pdf->Cell( 140, 8, __( 'Donation', 'give_pdf' ), 1, 2, 'C', true );

	$give_pdf->Ln( 0.2 );

	$give_pdf->SetX( 61 );

	$give_pdf->SetFillColor( $colors['sub'][0], $colors['sub'][1], $colors['sub'][2] );
	$give_pdf->SetFont( $font, '', 9 );


	$give_pdf->Cell( 102, 7, __( 'DONATION NAME', 'give_pdf' ), 'BL', 0, 'C', false );
	$give_pdf->Cell( 38, 7, __( 'DONATION TOTAL', 'give_pdf' ), 'BR', 0, 'C', false );

	$give_pdf->Ln( 0.2 );


	$give_pdf->Ln();

	if ( $give_pdf_payment_meta ) {
		$give_pdf->SetX( 61 );

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

		$give_pdf_download_title = html_entity_decode( get_the_title( $give_pdf_form_id ), ENT_COMPAT, 'UTF-8' );

		if ( isset( $price_id ) && ! empty( $price_id ) ) {
			$give_pdf_download_title .= ' - ' . give_get_price_option_name( $give_pdf_form_id, $price_id, $payment_id );
		}


		$dimensions = $give_pdf->getPageDimensions();
		$has_border = false;
		$linecount  = $give_pdf->getNumLines( $give_pdf_download_title, 82 );


		$give_pdf->MultiCell( 102, $linecount * 5, $give_pdf_download_title, 'L', 'C', false, 0, 61 );
		$give_pdf->SetFillColor( 250, 250, 250 );
		$give_pdf->Cell( 38, $linecount * 5, $total, 'BR', 2, 'C', true );
		$give_pdf->SetX( 61 );
		$give_pdf->Cell( 102, 8, '', 'T' );


		$give_pdf->Ln( 5 );
		$give_pdf->SetX( 61 );

		$give_pdf->SetFillColor( $colors['header'][0], $colors['header'][1], $colors['header'][2] );
		$give_pdf->SetFont( $fontb, '', 10 );
		$give_pdf->Cell( 140, 8, __( 'Receipt Totals', 'give_pdf' ), 1, 2, 'C', true );

		$give_pdf->Ln( 0.2 );

		$give_pdf->SetX( 61 );


		$fees = give_get_payment_fees( $give_pdf_payment->ID );
		if ( ! empty ( $fees ) ) {
			foreach ( $fees as $fee ) {
				$fee_amount = html_entity_decode( give_currency_filter( $fee['amount'] ) );

				$give_pdf->SetX( 61 );
				$give_pdf->Cell( 102, 8, $fee['label'], 'B', 0, 'L', false );
				$give_pdf->Cell( 38, 8, $fee_amount, 'B', 2, 'R', true );
			} // end foreach
		} // end if


		$give_pdf->SetX( 61 );
		$give_pdf->SetFont( $fontb, '', 11 );
		$give_pdf->Cell( 102, 10, __( 'Total Donation', 'give_pdf' ), 'BL', 0, 'C', false );
		$give_pdf->Cell( 38, 10, $total, 'BR', 2, 'C', false );

		$give_pdf->Ln( 10 );

		if ( isset ( $give_options['give_pdf_additional_notes'] ) && ! empty ( $give_options['give_pdf_additional_notes'] ) ) {
			$give_pdf->SetX( 60 );
			$give_pdf->SetFont( $font, '', 13 );
			$give_pdf->Cell( 0, 6, __( 'Additional Notes', 'give_pdf' ), 0, 2, 'L', false );
			$give_pdf->Ln( 2 );

			$give_pdf->SetX( 60 );
			$give_pdf->SetFont( $font, '', 10 );
			$give_pdf->SetTextColor( $colors['notes'][0], $colors['notes'][1], $colors['notes'][2] );
			$give_pdf->MultiCell( 0, 6, give_pdf_get_settings( $give_pdf, 'notes' ), 0, 'L', false );
		} // end if
	} // end if
}

add_action( 'give_pdf_template_blue', 'give_pdf_template_colours', 10, 10 );
add_action( 'give_pdf_template_green', 'give_pdf_template_colours', 10, 10 );
add_action( 'give_pdf_template_orange', 'give_pdf_template_colours', 10, 10 );
add_action( 'give_pdf_template_pink', 'give_pdf_template_colours', 10, 10 );
add_action( 'give_pdf_template_purple', 'give_pdf_template_colours', 10, 10 );
add_action( 'give_pdf_template_red', 'give_pdf_template_colours', 10, 10 );
add_action( 'give_pdf_template_yellow', 'give_pdf_template_colours', 10, 10 );
