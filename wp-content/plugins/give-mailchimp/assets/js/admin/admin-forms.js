/*!
 * Give MailChimp Admin Forms JS
 *
 * @description: The Give Admin Forms scripts. Only enqueued on the give_forms CPT; used to validate fields, show/hide, and other functions
 * @package:     Give
 * @subpackage:  Assets/JS
 * @copyright:   Copyright (c) 2015, WordImpress
 * @license:     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

jQuery.noConflict();
(function ( $ ) {

	/**
	 * Toggle Conditional Form Fields
	 *
	 *  @since: 1.0
	 */
	var toggle_mailchimp_fields = function () {


		var mc_enable_option = $( '.give-mc-enable' );
		var mc_disable_option = $( '.give-mc-disable' );

		mc_enable_option.on( 'change', function () {

			var mc_enable_option_val = $(this ).prop('checked');

			if ( mc_enable_option_val === false ) {
				$( '.give-mailchimp-field-wrap' ).slideUp('fast');
			} else {
				$( '.give-mailchimp-field-wrap' ).slideDown('fast');
			}

		} ).change();

		mc_disable_option.on( 'change', function () {

			var mc_disable_option_val = $(this ).prop('checked');

			if ( mc_disable_option_val === false ) {
				$( '.give-mailchimp-field-wrap' ).slideDown('fast');
			} else {
				$( '.give-mailchimp-field-wrap' ).slideUp('fast');
			}

		} ).change();

	};


	//On DOM Ready
	$( function () {

		toggle_mailchimp_fields();

	} );


})( jQuery );
