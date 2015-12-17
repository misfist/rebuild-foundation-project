<?php
/**
 * Email Templates
 *
 * Creates email templates to match each of the receipt templates.
 *
 * @package Give - PDF Receipts
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Templates
 *
 * Registers the email templates bundled with the plugin
 *
 * @since 1.0
 *
 * @param array $give_templates An array of the pre-existing EDD email templates
 *
 * @return array Merged array containing the new and pre-existing EDD email
 *          templates
 */
function give_pdf_register_templates( $give_templates ) {
	$give_pdf_email_templates = array(
		'receipt_default' => __( 'Receipt Default', 'give_pdf' ),
		'blue_stripe'     => __( 'Blue Stripe', 'give_pdf' ),
		'lines'           => __( 'Lines', 'give_pdf' ),
		'minimal'         => __( 'Minimal', 'give_pdf' ),
		'traditional'     => __( 'Traditional', 'give_pdf' ),
		'receipt_blue'    => __( 'Receipt Blue', 'give_pdf' ),
		'receipt_green'   => __( 'Receipt Green', 'give_pdf' ),
		'receipt_orange'  => __( 'Receipt Orange', 'give_pdf' ),
		'receipt_pink'    => __( 'Receipt Pink', 'give_pdf' ),
		'receipt_purple'  => __( 'Receipt Purple', 'give_pdf' ),
		'receipt_red'     => __( 'Receipt Red', 'give_pdf' ),
		'receipt_yellow'  => __( 'Receipt Yellow', 'give_pdf' )
	);

	return array_merge( $give_templates, $give_pdf_email_templates );
}
add_filter( 'give_email_templates', 'give_pdf_register_templates' );


/**
 * Default Receipt Email Template
 *
 * @since		1.0
*/
function give_pdf_receipt_default() {
	global $give_options;

	echo '<div style="width: 550px; background: #ececec; border: 1px solid #c9c9c9; margin: 0 auto; padding: 4px; outline: none;">';
		echo '<div style="padding: 1px; background: #fff; border: 1px solid #fff;">';
			echo '<div id="give-email-content" style="padding: 10px; background: #fff; border: 1px solid #aaa;">';
				if ( isset( $give_options['email_logo'] ) ) {
					echo '<img src="' . $give_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
				} else if ( isset( $give_options['give_pdf_email_logo'] ) ) {
					echo '<img src="' . $give_options['give_pdf_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
				}
				echo '<h1 style="color: #323232; font-size: 24px; font-weight: normal;">' . __( 'Receipt', 'give_pdf' ) .'</h1>';
				echo '{email}';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
add_filter( 'give_email_template_receipt_default', 'give_pdf_receipt_default' );

/**
 * Default Email Template Extra Styling
 *
 * Overrides the default receipt template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function give_pdf_receipt_default_extra_styling( $email_body ) {
	$email_body = str_replace( '<h1>', '<h1 style="color: #323232; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="color: #323232; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="color: #323232; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<li>', '<li style="color: #323232;">', $email_body );
	$email_body = str_replace( '<p>', '<p style="color: #323232;">', $email_body );

	return $email_body;
}
add_filter( 'give_purchase_receipt_receipt_default', 'give_pdf_receipt_default_extra_styling' );


/**
 * Blue Stripe Email Template
 *
 * @since 1.0
*/
function give_pdf_blue_stripe() {
	global $give_options;

	echo '<div style="width: 600px; background: #fff; border-left: 10px solid #97d3eb; margin: 0 auto; padding: 10px; outline: none;">';
		echo '<div id="give-email-content" style="padding: 10px; background: #fff;">';
			if ( isset( $give_options['email_logo'] ) ) {
				echo '<img src="' . $give_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			} else if ( isset( $give_options['give_pdf_email_logo'] ) ) {
				echo '<img src="' . $give_options['give_pdf_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			}
			echo '<h1 style="color: #97d3eb; font-size: 24px; font-weight: normal;">' . __( 'Receipt', 'give_pdf' ) .'</h1>';
			echo '{email}';
		echo '</div>';
	echo '</div>';
}
add_filter( 'give_email_template_blue_stripe', 'give_pdf_blue_stripe' );

/**
 * Blue Stripe Email Template Extra Styling
 *
 * Overrides the default receipt template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function give_pdf_blue_stripe_extra_styling( $email_body ) {
	$email_body = str_replace( '<h1>', '<h1 style="color: #97d3eb; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="color: #97d3eb; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="color: #97d3eb; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<ul>', '<ul style="margin: 0 0 0 20px; padding: 0;">', $email_body );
	$email_body = str_replace( '<li>', '<li style="list-style: square;">', $email_body );

	return $email_body;
}
add_filter( 'give_purchase_receipt_blue_stripe','give_pdf_blue_stripe_extra_styling' );


/**
 * Lines Email Template
 *
 * @since 1.0
*/
function give_pdf_lines() {
	global $give_options;

	echo '<div style="width: 700px; margin: 0 auto; border: none; background: #fff url(\'' . GIVE_PDF_PLUGIN_URL . 'templates/lines/lines.jpg\') repeat-y;">';
		echo '<div id="give-email-content" style="padding: 10px 10px 10px 130px;">';
			if ( isset( $give_options['email_logo'] ) ) {
				echo '<img src="' . $give_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			} else if ( isset( $give_options['give_pdf_email_logo'] ) ) {
				echo '<img src="' . $give_options['give_pdf_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			}
			echo '<h1 style="margin-top: 0; color: #de3b1e; font-size: 28px; line-height: 32px; font-weight: normal;">' . __( 'Receipt', 'give_pdf' ) .'</h1>';
			echo '{email}';
		echo '</div>';
	echo '</div>';
}
add_filter( 'give_email_template_lines', 'give_pdf_lines' );

/**
 * Lines Email Template Extra Styling
 *
 * Overrides the default receipt template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function give_pdf_lines_extra_styling( $email_body ) {
	$email_body = str_replace( '<h1>', '<h1 style="color: #de3b1e; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="color: #de3b1e; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="color: #de3b1e; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<ul>', '<ul style="margin: 0 0 0 20px; padding: 0;">', $email_body );
	$email_body = str_replace( '<li>', '<li style="border-left: 2px solid #f0e6dc; padding-left: 5px; line-height: 21px;">', $email_body );

	return $email_body;
}
add_filter( 'give_purchase_receipt_lines', 'give_pdf_lines_extra_styling' );


/**
 * Minimal Email Template
 *
 * @since 1.0
 */
function give_pdf_minimal() {
	global $give_options;

	echo '<div style="width: 550px; margin: 0 auto; border: none; background: #fff; border-left: 2px solid #f0e6dc;">';
		echo '<div id="give-email-content" style="padding: 10px;">';
			if ( isset( $give_options['email_logo'] ) ) {
				echo '<img src="' . $give_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			} else if ( isset( $give_options['give_pdf_email_logo'] ) ) {
				echo '<img src="' . $give_options['give_pdf_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			}
			echo '<h1 style="margin-top: 0; color: #de3b1e; font-size: 28px; line-height: 32px; font-weight: normal;">' . __( 'Receipt', 'give_pdf' ) .'</h1>';
			echo '{email}';
		echo '</div>';
	echo '</div>';
}
add_filter( 'give_email_template_minimal', 'give_pdf_minimal' );

/**
 * Minimal Email Template Extra Styling
 *
 * Overrides the default receipt template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function give_pdf_minimal_extra_styling( $email_body ) {
	return $email_body;
}
add_filter('give_purchase_receipt_minimal', 'give_pdf_minimal_extra_styling');


/**
 * Traditional Email Template
 *
 * @since 1.0
*/
function give_pdf_traditional() {
	global $give_options;

	echo '<div style="width: 660px; margin: 0 auto; border: none; background: #fff url(\''. GIVE_PDF_PLUGIN_URL .'templates/traditional/header_background.jpg\') repeat-x;">';
		echo '<div id="give-email-content" style="padding: 10px;">';
			if ( isset( $give_options['email_logo'] ) ) {
				echo '<img src="' . $give_options['email_logo'] . '" style="margin:10px 0 0 2px;position:relative;z-index:2;"/>';
			} else if ( isset( $give_options['give_pdf_email_logo'] ) ) {
				echo '<img src="' . $give_options['give_pdf_email_logo'] . '" style="margin:10px 0 0 2px;position:relative;z-index:2;"/>';
			}
			echo '<h1 style="margin-top: 12px; color: #fff; text-transform: uppercase; font-family: Times News Roman, Times, serif; font-size: 28px; line-height: 32px; font-weight: normal;">' . __( 'Receipt', 'give_pdf' ) .'</h1>';
			echo '{email}';
		echo '</div>';
	echo '</div>';
}
add_filter( 'give_email_template_traditional', 'give_pdf_traditional' );

/**
 * Traditional Email Template Extra Styling
 *
 * Overrides the default receipt template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function give_pdf_traditional_extra_styling( $email_body ) {
	$email_body = str_replace( '<h1>', '<h1 style="font-family: Times New Roman, Times, serif; color: #323232; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="font-family: Times New Roman, Times, serif; color: #323232; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="font-family: Times New Roman, Times, serif; color: #323232; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="font-size: 14px; line-height: 21px; font-family: Times New Roman, Times, serif; color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<ul>', '<ul style="font-size: 14px; font-family: Times New Roman, Times, serif; margin: 0 0 0 20px; padding: 0;">', $email_body );
	$email_body = str_replace( '<li>', '<li style="font-size: 14px; line-height: 21px; font-family: Times New Roman, Times, serif; list-style: square;">', $email_body );
	$email_body = str_replace( '<p>', '<p style="font-size: 14px; line-height: 21px; font-family: Times New Roman, Times, serif;">', $email_body );

	return $email_body;
}
add_filter( 'give_purchase_receipt_traditional', 'give_pdf_traditional_extra_styling' );



/**
 * Different Colored Email Templates
 *
 * @since 1.0
*/
function give_pdf_colors() {
	global $give_options;

	switch ( $give_options['email_template'] ) {

		case 'receipt_blue':
			$colors = array(
				'emphasis' => '479bc6',
				'title' => '479bc6',
				'header' => 'cae2ee',
				'border' => 'a6cde2'
			);
		break;

		case 'receipt_red':
			$colors = array(
				'emphasis' => 'c64747',
				'title' => 'c00000',
				'header' => 'eecaca',
				'border' => 'e2a6a6'
			);
		break;

		case 'receipt_green':
			$colors = array(
				'emphasis' => '47c662',
				'title' => '00c044',
				'header' => 'caeed4',
				'border' => 'a6e2b3'
			);
		break;

		case 'receipt_orange':
			$colors = array(
				'emphasis' => 'c68647',
				'title' => 'c05100',
				'header' => 'eedbca',
				'border' => 'e2cba6'
			);
		break;

		case 'receipt_yellow':
			$colors = array(
				'emphasis' => 'c5c647',
				'title' => 'eae80b',
				'header' => 'eeeeca',
				'border' => 'e2c1a6'
			);
		break;

		case 'receipt_purple':
			$colors = array(
				'emphasis' => '8947c6',
				'title' => '4800c0',
				'header' => 'd0caee',
				'border' => 'bda6e2'
			);
		break;

		case 'receipt_pink':
			$colors = array(
				'emphasis' => 'c64798',
				'title' => '5c0041',
				'header' => 'eecae8',
				'border' => 'e2a6d5'
			);
		break;

	}

	echo '<div style="width: 550px; background: #'.$colors['header'].'; border: 1px solid #'. $colors['emphasis'] .'; margin: 0 auto; padding: 4px; outline: none;">';
		echo '<div style="padding: 1px; background: #fff; border: 1px solid #fff;">';
			echo '<div id="give-email-content" style="padding: 10px; background: #fff; border: 1px solid #'. $colors['border'] .';">';
				if ( isset( $give_options['email_logo'] ) ) {
					echo '<img src="' . $give_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
				} else if ( isset( $give_options['give_pdf_email_logo'] ) ) {
					echo '<img src="' . $give_options['give_pdf_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
				}
				echo '<h1 style="color: #'. $colors['title'] .'; font-size: 24px; font-weight: normal;">' . __( 'Receipt', 'give_pdf' ) .'</h1>';
				echo '{email}';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
add_filter( 'give_email_template_receipt_blue', 'give_pdf_colors' );
add_filter( 'give_email_template_receipt_red', 'give_pdf_colors' );
add_filter( 'give_email_template_receipt_green', 'give_pdf_colors' );
add_filter( 'give_email_template_receipt_orange', 'give_pdf_colors' );
add_filter( 'give_email_template_receipt_yellow', 'give_pdf_colors' );
add_filter( 'give_email_template_receipt_purple', 'give_pdf_colors' );
add_filter( 'give_email_template_receipt_pink', 'give_pdf_colors' );

/**
 * Different Colored Email Template Extra Styling
 *
 * Overrides the default receipt template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function give_pdf_colors_extra_styling( $email_body ) {
	global $give_options;

	switch ( $give_options['email_template'] ) {

		case 'receipt_blue':
			$colors = array('title' => '479bc6');
		break;

		case 'receipt_red':
			$colors = array('title' => 'c00000');
		break;

		case 'receipt_green':
			$colors = array('title' => '00c044');
		break;

		case 'receipt_orange':
			$colors = array('title' => 'c05100');
		break;

		case 'receipt_yellow':
			$colors = array('title' => 'eae80b');
		break;

		case 'receipt_purple':
			$colors = array('title' => '4800c0');
		break;

		case 'receipt_pink':
			$colors = array('title' => '5c0041');
		break;

	}

	$email_body = str_replace( '<h1>', '<h1 style="color: #'. $colors['title'] .'; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="color: #'. $colors['title'] .'; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="color: #'. $colors['title'] .'; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="line-height: 21px; color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<ul>', '<ul style="margin: 0 0 0 20px; padding: 0;">', $email_body );
	$email_body = str_replace( '<li>', '<li style="line-height: 21px;  list-style: square;">', $email_body );

	return $email_body;
}
add_filter( 'give_purchase_receipt_receipt_blue', 'give_pdf_colors_extra_styling' );
add_filter( 'give_purchase_receipt_receipt_red', 'give_pdf_colors_extra_styling' );
add_filter( 'give_purchase_receipt_receipt_green', 'give_pdf_colors_extra_styling' );
add_filter( 'give_purchase_receipt_receipt_orange', 'give_pdf_colors_extra_styling' );
add_filter( 'give_purchase_receipt_receipt_yellow', 'give_pdf_colors_extra_styling' );
add_filter( 'give_purchase_receipt_receipt_purple', 'give_pdf_colors_extra_styling' );
add_filter( 'give_purchase_receipt_receipt_pink', 'give_pdf_colors_extra_styling' );
