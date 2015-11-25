<?php
function rsvp_pro_get_option_license($option_name) {
	if(rsvp_pro_is_network_activated()) {
		return get_site_option($option_name);
	} else {
		return get_option($option_name);
	}
}

function rsvp_pro_delete_option_license($option_name) {
	if(rsvp_pro_is_network_activated()) {
		return delete_site_option($option_name);
	} else {
		return delete_option($option_name);
	}
}

function rsvp_pro_update_option_license($option_name, $option_value) {
	if(rsvp_pro_is_network_activated()) {
		return update_site_option($option_name, $option_value);
	} else {
		return update_option($option_name, $option_value);
	}
}


function rsvp_pro_register_option() {
	// creates our settings in the options table
	register_setting('rsvppro-license', 'rsvp_pro_license_key', 'rsvp_pro_sanitize_license' );
}
add_action('admin_init', 'rsvp_pro_register_option');

function rsvp_pro_sanitize_license( $new ) {
	$old = rsvp_pro_get_option_license( 'rsvp_pro_license_key' );		
	if( $old && $old != $new ) {
		rsvp_pro_delete_option_license( 'rsvp_pro_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}

function rsvp_pro_activate_license() {
	// listen for our activate button to be clicked
	if( isset( $_POST['rsvp_pro_license_activate'] ) ) {
		// run a quick security check 
	 	if( ! check_admin_referer( 'rsvp_pro_license_nonce', 'rsvp_pro_license_nonce' ) ) 	
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( rsvp_pro_get_option_license( 'rsvp_pro_license_key' ) );
			

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( RSVP_PRO_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);
		
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, RSVP_PRO_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"
		rsvp_pro_update_option_license( 'rsvp_pro_license_status', $license_data->license );

	}
}
add_action('admin_init', 'rsvp_pro_activate_license');


/***********************************************
* Illustrates how to deactivate a license key.
* This will descrease the site count
***********************************************/

function rsvp_pro_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['rsvp_pro_license_deactivate'] ) ) {

		// run a quick security check 
	 	if( ! check_admin_referer( 'rsvp_pro_license_nonce', 'rsvp_pro_license_nonce' ) ) 	
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( rsvp_pro_get_option_license( 'rsvp_pro_license_key' ) );
			

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( RSVP_PRO_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);
		
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, RSVP_PRO_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			rsvp_pro_delete_option_license( 'rsvp_pro_license_status' );

	}
}
add_action('admin_init', 'rsvp_pro_deactivate_license');

function rsvp_pro_check_license() {

	global $wp_version;

	$license = trim( rsvp_pro_get_option_license( 'rsvp_pro_license_key' ) );

	$api_params = array( 
		'edd_action' => 'check_license', 
		'license' => $license, 
		'item_name' => urlencode( RSVP_PRO_ITEM_NAME ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, RSVP_PRO_STORE_URL ), array( 'timeout' => 15, 'sslverify' => true ) );

	if ( is_wp_error( $response ) )
		return "invalid";

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );
	
  	$isValid = false;
  	if( $license_data->license == 'valid' ) { 
  		$isValid = true;
  	} elseif((strtotime($license_data->expires) >= date(time())) && ($license_data->license_limit == 0) && rsvp_pro_is_network_activated()) {
  		$isValid = true;
	}


	if( $isValid ) {
		return 'valid';
    
		// this license is still valid
	} else {
		return 'invalid';
		// this license is no longer valid
	}
}
?>