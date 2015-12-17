<?php
/**
 * PDF Receipt Class
 *
 * Extends the TCPDF class to add the extra functionality for the PDF Receipts
 *
 * @since   1.0
 * @package Give - PDF Receipts
 */

/**
 * Give_PDF_Receipt Class
 */
class Give_PDF_Receipt extends TCPDF {

	/**
	 * Header
	 *
	 * Outputs the header message configured in the Settings on all the receipts
	 * as well as display the background images on certain templates
	 *
	 * @since 2.0
	 */
	public function Header() {

		global $give_options;

		if ( isset( $give_options['give_pdf_templates'] ) && $give_options['give_pdf_templates'] == 'blue_stripe' ) {
			$this->Rect( 0, 0, 30, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 149, 210, 236 ) );
		} else if ( isset( $give_options['give_pdf_templates'] ) && $give_options['give_pdf_templates'] == 'lines' ) {
			$this->Rect( 1, 0, 0.5, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 192, 55, 26 ) );
			$this->Rect( 3, 0, 1, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 169, 169, 169 ) );
			$this->Rect( 8, 0, 0.5, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 228, 190, 172 ) );
			$this->Rect( 10, 0, 1, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 199, 60, 37 ) );
			$this->Rect( 17, 0, 0.5, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array(
				218,
				180,
				167
			) );
			$this->Rect( 20, 0, 5, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 240, 230, 220 ) );
			$this->Rect( 206, 0, 0.8, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array(
				240,
				230,
				220
			) );
		} // end if

		if (
			$give_options['give_pdf_templates'] == 'blue' ||
			$give_options['give_pdf_templates'] == 'green' ||
			$give_options['give_pdf_templates'] == 'orange' ||
			$give_options['give_pdf_templates'] == 'pink' ||
			$give_options['give_pdf_templates'] == 'purple' ||
			$give_options['give_pdf_templates'] == 'red' ||
			$give_options['give_pdf_templates'] == 'yellow'
		) {
			$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freesans' : 'opensans';
			$this->AddFont( 'opensansi', '' );
			$this->SetFont( $font, 'I', 8 );
		} else if ( $give_options['give_pdf_templates'] == 'lines' || $give_options['give_pdf_templates'] == 'blue_stripe' ) {
			$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freeserif' : 'droidserif';
			$this->AddFont( 'droidserifi', '' );
			$this->SetFont( $font, 'I', 8 );
		} else if ( $give_options['give_pdf_templates'] == 'traditional' ) {
			$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freeserif' : 'times';
			$this->AddFont( 'times', 'I' );
			$this->SetFont( $font, 'I', 8 );
			$this->SetTextColor( 50, 50, 50 );
		} else {
			$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freesans' : 'helvetica';
			$this->SetFont( $font, 'I', 8 );
		} // end if

		if ( isset( $give_options['give_pdf_header_message'] ) ) {
			$give_pdf_payment = get_post( $_GET['transaction_id'] );

			$give_pdf_header = isset( $give_options['give_pdf_header_message'] ) ? $give_options['give_pdf_header_message'] : '';
			$give_pdf_header = str_replace( '{page}', 'Page ' . $this->PageNo(), $give_pdf_header );
			$give_pdf_header = str_replace( '{sitename}', get_bloginfo( 'name' ), $give_pdf_header );
			$give_pdf_header = str_replace( '{today}', date_i18n( get_option( 'date_format' ), time() ), $give_pdf_header );
			$give_pdf_header = str_replace( '{date}', date_i18n( get_option( 'date_format' ), strtotime( $give_pdf_payment->post_date ) ), $give_pdf_header );
			$give_pdf_header = str_replace( '{receipt_id}', $give_pdf_payment->ID, $give_pdf_header );

			$this->Cell( 0, 10, stripslashes_deep( html_entity_decode( $give_pdf_header, ENT_COMPAT, 'UTF-8' ) ), 0, 0, 'C' );
		} // end if

	} // end Header

	/**
	 * Footer
	 *
	 * Outputs the footer message configured in the Settings on all the receipts
	 *
	 * @since 2.0
	 */
	public function Footer() {

		global $give_options;

		$this->SetY( - 15 );

		if (
			$give_options['give_pdf_templates'] == 'blue' ||
			$give_options['give_pdf_templates'] == 'green' ||
			$give_options['give_pdf_templates'] == 'orange' ||
			$give_options['give_pdf_templates'] == 'pink' ||
			$give_options['give_pdf_templates'] == 'purple' ||
			$give_options['give_pdf_templates'] == 'red' ||
			$give_options['give_pdf_templates'] == 'yellow'
		) {
			$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freesans' : 'opensans';
			$this->AddFont( 'opensansi', '' );
			$this->SetFont( $font, 'I', 8 );
		} else if ( $give_options['give_pdf_templates'] == 'lines' || $give_options['give_pdf_templates'] == 'blue_stripe' ) {
			$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freeserif' : 'droidserif';
			$this->AddFont( 'droidserifi', '' );
			$this->SetFont( $font, 'I', 8 );
		} else if ( $give_options['give_pdf_templates'] == 'traditional' ) {
			$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freeserif' : 'times';
			$this->AddFont( 'times', 'I' );
			$this->SetFont( $font, 'I', 8 );
			$this->SetTextColor( 50, 50, 50 );
		} else {
			$font = isset( $give_options['give_pdf_enable_char_support'] ) ? 'freesans' : 'helvetica';
			$this->SetFont( $font, 'I', 8 );
		} // end if

		if ( isset( $give_options['give_pdf_footer_message'] ) ) {
			$give_pdf_payment = get_post( $_GET['transaction_id'] );

			$give_pdf_footer = isset( $give_options['give_pdf_footer_message'] ) ? $give_options['give_pdf_footer_message'] : '';
			$give_pdf_footer = str_replace( '{page}', 'Page ' . $this->PageNo(), $give_pdf_footer );
			$give_pdf_footer = str_replace( '{sitename}', get_bloginfo( 'name' ), $give_pdf_footer );
			$give_pdf_footer = str_replace( '{today}', date( get_option( 'date_format' ), time() ), $give_pdf_footer );
			$give_pdf_footer = str_replace( '{date}', date( get_option( 'date_format' ), strtotime( $give_pdf_payment->post_date ) ), $give_pdf_footer );
			$give_pdf_footer = str_replace( '{receipt_id}', $give_pdf_payment->ID, $give_pdf_footer );

			$this->Cell( 0, 10, stripslashes_deep( html_entity_decode( $give_pdf_footer, ENT_COMPAT, 'UTF-8' ) ), 0, 0, 'C' );
		} // end if

	} // end Footer

} // end class
