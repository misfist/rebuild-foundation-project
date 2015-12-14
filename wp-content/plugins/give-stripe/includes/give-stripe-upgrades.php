<?php
/**
 * Stripe Payment Upgrades
 *
 * @package     Give
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */

class Give_Stripe_Upgrades {


	public function __construct() {

		//Activation
		register_activation_hook( GIVE_STRIPE_PLUGIN_FILE, array( $this, 'version_check' ) );

	}

	/**
	 * Version check
	 */
	public function version_check() {

		$previous_version = get_option( 'give_stripe_version' );

		//No version option saved
		if ( version_compare( '1.2', $previous_version, '>' ) || empty( $previous_version ) ) {

			$this->update_v12_preapproval_metakey();

		}

		//Update the version # saved in DB after version checks above
		update_option( 'give_stripe_version', GIVE_STRIPE_VERSION );

	}


	/**
	 * Update 1.2 Preapproval Metakey
	 *
	 * @description: Updates a required metakey value due to a typo causing a bug
	 * @see        : https://github.com/WordImpress/Give-Stripe/pull/1 and https://github.com/WordImpress/Give-Stripe/pull/2
	 */
	private function update_v12_preapproval_metakey() {

		global $wpdb;
		$sql = "UPDATE $wpdb->postmeta SET `meta_key` = '_give_stripe_customer_id' WHERE `meta_key` LIKE '_give_stripe_stripe_customer_id'";
		$wpdb->query( $sql );

	}

}

new Give_Stripe_Upgrades();
