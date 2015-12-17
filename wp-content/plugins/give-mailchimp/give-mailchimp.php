<?php
/**
 * Plugin Name: Give - MailChimp
 * Plugin URL: https://givewp.com/addons/mailchimp/
 * Description: Integrate MailChimp with your Give donation forms
 * Version: 1.1
 * Author: WordImpress
 * Author URI: http://wordimpress.com
 * Contributors: dlocc, webdevmattcrom, Pippin Williamson, Dave Kiss
 */

//Constants
define( 'GIVE_MAILCHIMP_VERSION', '1.1' );

if ( ! defined( 'GIVE_MAILCHIMP_STORE_API_URL' ) ) {
	define( 'GIVE_MAILCHIMP_STORE_API_URL', 'https://givewp.com' );
}

if ( ! defined( 'GIVE_MAILCHIMP_PRODUCT_NAME' ) ) {
	define( 'GIVE_MAILCHIMP_PRODUCT_NAME', 'MailChimp' );
}

if ( ! defined( 'GIVE_MAILCHIMP_PATH' ) ) {
	define( 'GIVE_MAILCHIMP_PATH', dirname( __FILE__ ) );
}
if ( ! defined( 'GIVE_MAILCHIMP_URL' ) ) {
	define( 'GIVE_MAILCHIMP_URL', plugin_dir_url( __FILE__ ) );
}


//Licensing
function give_add_mailchimp_licensing() {
	if ( class_exists( 'Give_License' ) && is_admin() ) {
		$give_mailchimp_license = new Give_License( __FILE__, GIVE_MAILCHIMP_PRODUCT_NAME, GIVE_MAILCHIMP_VERSION, 'Devin Walker' );
	}
}

add_action( 'plugins_loaded', 'give_add_mailchimp_licensing' );


/**
 * Give MailChimp Includes
 */
function give_mailchimp_includes() {
	//Includes
	if ( ! class_exists( 'Give_MailChimp_API' ) ) {
		include( dirname( __FILE__ ) . '/includes/class-give-mailchimp-api.php' );
	}

	if ( ! class_exists( 'Give_Newsletter' ) ) {
		include( dirname( __FILE__ ) . '/includes/class-give-newsletter.php' );
	}

	if ( ! class_exists( 'Give_MailChimp' ) ) {
		include( dirname( __FILE__ ) . '/includes/class-give-mailchimp.php' );
	}

	if ( ! class_exists( 'Give_MC_Ecommerce_360' ) ) {
		include( dirname( __FILE__ ) . '/includes/class-give-ecommerce360.php' );
	}


	$give_mc    = new Give_MailChimp( 'mailchimp', 'MailChimp' );
	$give_mc360 = new Give_MC_Ecommerce_360;

}

add_action( 'plugins_loaded', 'give_mailchimp_includes' );



