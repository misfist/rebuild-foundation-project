/*jslint white: true */

jQuery( document ).ready( function( $ ) {

    console.log( 'ajax-filter-events.js loaded' );

    $( '.event-cat-filter' ).click( function( event ) {

        // Prevent default action - opening tag page
        if (event.preventDefault) {
            event.preventDefault();
        } else {
            event.returnValue = false;
        }

        var selectedCategory = $( this ).attr( 'title' );

        console.log( selectedCategory );

        // After user click on tag, fade out list of posts
        $('.tagged-posts').fadeOut();
 
        data = {
            action: 'filter_posts', // function to execute
            afe_nonce: afe_vars.afe_nonce, // wp_nonce
            taxonomy: selectedCategory, // selected tag
        };
 
        $.post( afe_vars.afe_ajax_url, data, function( response ) {

            console.log( response );
 
            if( response ) {
                // Display posts on page
                $('.tagged-events').html( response );
                // Restore div visibility
                $('.tagged-events').fadeIn();
            };
        });

    } );

} );