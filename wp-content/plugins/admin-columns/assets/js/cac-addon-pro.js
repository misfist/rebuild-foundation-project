jQuery( document ).ready( function( $ ) {

	// @todo: does did need to run?
	if ( $( '#toggle_ssl').length > 0 ) {

		jQuery.post( ajaxurl, {
			action: 'cpac_check_connection',
		}, function( data ) {
			if ( '1' !== jQuery.trim( data ) ) {
				$( '#toggle_ssl' ).show();
				$( '#licence_activation' ).hide();
			}
		});
	}
} );
