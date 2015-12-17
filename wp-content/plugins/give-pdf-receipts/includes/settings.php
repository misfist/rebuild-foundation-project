<?php
/**
 * Settings
 *
 * @description Registers all the settings required for the plugin.
 *
 * @package     Give PDF Receipts
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'give_et_logo_settings' ) ) :
	/**
	 * Logo Settings
	 *
	 * Registers the settings to enable logo upload to be displayed on the receipt
	 *
	 * @since 1.0
	 *
	 * @param array $settings Array of pre-defined settings
	 *
	 * @return array Merged array with new settings
	 */
	function give_pdf_logo_settings( $settings ) {
		$logo_settings = array(
			array(
				'name' => __( 'Receipt Logo', 'give_pdf' ),
				'id'   => 'give_pdf_email_logo',
				'desc' => __( 'Upload or choose a logo to be displayed at the top of the email.', 'give_pdf' ),
				'type' => 'file'
			)
		);

		return array_merge( $logo_settings, $settings );
	}

	//	add_filter( 'give_settings_emails', 'give_pdf_logo_settings' );
endif;

/**
 * Add PDF Receipts Tab
 *
 * @param $tabs
 */
function give_pdf_receipts_tab( $tabs ) {

	$tabs['pdf_receipts'] = __( 'PDF Receipts', 'give' );

	return $tabs;
}

add_filter( 'give_settings_tabs', 'give_pdf_receipts_tab', 10, 1 );


/**
 * Add Settings
 *
 * Adds the new settings for the plugin
 *
 * @since 1.0
 *
 * @param array $settings Array of pre-defined setttings
 *
 * @return array Merged array with new settings
 */
function give_pdf_add_settings( $settings ) {

	if ( ! is_admin() || ! isset( $_GET['tab'] ) || $_GET['tab'] !== 'pdf_receipts' ) {
		return $settings;
	}

	$pdf_settings = array(
		/**
		 * PDF Settings
		 */
		'id'         => 'options_page',
		'give_title' => __( 'PDF Receipt Settings', 'give' ),
		'show_on'    => array( 'key' => 'options-page', 'value' => array( 'give_settings', ), ),
		'fields'     => apply_filters( 'give_settings_pdf_receipts', array(
				array(
					'name' => '<strong>' . __( 'PDF Receipt Settings', 'give_pdf' ) . '</strong>',
					'desc' => '<hr>',
					'id'   => 'give_pdf_settings',
					'type' => 'give_title'
				),
				array(
					'id'      => 'give_pdf_templates',
					'name'    => __( 'Receipt Template', 'give_pdf' ),
					'desc'    => __( 'Please select a template for your PDF Receipts. This template will be used for all Give PDF Receipts.', 'give_pdf' ),
					'type'    => 'select',
					'options' => apply_filters( 'give_pdf_templates_list', array(
						'default'     => __( 'Default', 'give_pdf' ),
						'blue_stripe' => __( 'Blue Stripe', 'give_pdf' ),
						'lines'       => __( 'Lines', 'give_pdf' ),
						'minimal'     => __( 'Minimal', 'give_pdf' ),
						'traditional' => __( 'Traditional', 'give_pdf' ),
						'blue'        => __( 'Blue', 'give_pdf' ),
						'green'       => __( 'Green', 'give_pdf' ),
						'orange'      => __( 'Orange', 'give_pdf' ),
						'pink'        => __( 'Pink', 'give_pdf' ),
						'purple'      => __( 'Purple', 'give_pdf' ),
						'red'         => __( 'Red', 'give_pdf' ),
						'yellow'      => __( 'Yellow', 'give_pdf' )
					) )
				),
				array(
					'id'   => 'give_pdf_enable_char_support',
					'name' => __( 'Characters not displaying correctly?', 'give_pdf' ),
					'desc' => __( 'Check to enable the Free Sans/Free Serif font replacing Open Sans/Helvetica/Times. Only do this if you have characters which do not display correctly (e.g. Greek characters)', 'give_pdf' ),
					'type' => 'checkbox',
				),
				array(
					'id'   => 'give_pdf_logo_upload',
					'name' => __( 'Logo Upload', 'give_pdf' ),
					'desc' => __( 'Upload your logo here which will show up on the receipt. If the logo is greater than 90px in height, it will not be shown. On the Traditional template, if the logo is greater than 80px in height, it will not be shown. Also note that the logo will be output at 96 dpi.', 'give_pdf' ),
					'type' => 'file'
				),
				array(
					'id'      => 'give_pdf_company_name',
					'name'    => __( 'Company Name', 'give_pdf' ),
					'desc'    => __( 'Enter the company name that will be shown on the receipt.', 'give_pdf' ),
					'type'    => 'text',
					'size'    => 'regular',
					'default' => ''
				),
				array(
					'id'      => 'give_pdf_name',
					'name'    => __( 'Name', 'give_pdf' ),
					'desc'    => __( 'Enter the name that will be shown on the receipt.', 'give_pdf' ),
					'type'    => 'text',
					'default' => ''
				),
				array(
					'id'      => 'give_pdf_address_line1',
					'name'    => __( 'Address Line 1', 'give_pdf' ),
					'desc'    => __( 'Enter the first address line that will appear on the receipt.', 'give_pdf' ),
					'type'    => 'text',
					'default' => ''
				),
				array(
					'id'      => 'give_pdf_address_line2',
					'name'    => __( 'Address Line 2', 'give_pdf' ),
					'desc'    => __( 'Enter the second address line that will appear on the receipt.', 'give_pdf' ),
					'type'    => 'text',
					'default' => ''
				),
				array(
					'id'      => 'give_pdf_address_city_state_zip',
					'name'    => __( 'City, State and Zip Code', 'give_pdf' ),
					'desc'    => __( 'Enter the city, state and zip code that will appear on the receipt.', 'give_pdf' ),
					'type'    => 'text',
					'default' => ''
				),
				array(
					'id'      => 'give_pdf_email_address',
					'name'    => __( 'Email Address', 'give_pdf' ),
					'desc'    => __( 'Enter the email address that will appear on the receipt.', 'give_pdf' ),
					'type'    => 'text',
					'default' => get_option( 'admin_email' )
				),
				array(
					'id'   => 'give_pdf_url',
					'name' => __( 'Show website address?', 'give_pdf' ),
					'desc' => __( 'Check this box if you would like your website address to be shown.', 'give_pdf' ),
					'type' => 'checkbox'
				),
				array(
					'id'   => 'give_pdf_header_message',
					'name' => __( 'Header Message', 'give_pdf' ),
					'desc' => __( 'Enter the message you would like to be shown on the header of the receipt. Please note that the header will not show up on the Blue Stripe and Traditional template.', 'give_pdf' ),
					'type' => 'text',
				),
				array(
					'id'   => 'give_pdf_footer_message',
					'name' => __( 'Footer Message', 'give_pdf' ),
					'desc' => __( 'Enter the message you would like to be shown on the footer of the receipt.', 'give_pdf' ),
					'type' => 'text',
				),
				array(
					'id'   => 'give_pdf_additional_notes',
					'name' => __( 'Additional Notes', 'give_pdf' ),
					'desc' => __( 'Enter any messages you would to be displayed at the end of the receipt. Only plain text is currently supported. Any HTML will not be shown on the receipt.', 'give_pdf' ) . __( 'The following template tags will work for the Header and Footer message as well as the Additional Notes:', 'give_pdf' ) . '<br />' . __( '{page} - Page Number', 'give_pdf' ) . '<br />' . __( '{sitename} - Site Name', 'give_pdf' ) . '<br />' . __( '{today} - Date of Receipt Generation', 'give_pdf' ) . '<br />' . __( '{date} - Receipt Date', 'give_pdf' ) . '<br />' . __( '{receipt_id} - Receipt ID', 'give_pdf' ),
					'type' => 'textarea'
				)
			)
		)
	);

	return $pdf_settings;
	//	return $pdf_receipts;
}

//add_filter( 'give_settings_pdf_receipts', 'give_pdf_add_settings' );

add_filter( 'give_registered_settings', 'give_pdf_add_settings' );


/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since       1.0
 */

function give_pdfi_plain_text_callback( $args ) {
	echo $args['desc'];
}
