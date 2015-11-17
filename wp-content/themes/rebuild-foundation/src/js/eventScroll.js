/*jslint white: true */

jQuery( document ).ready( function( $ ) {

    //console.log( 'eventScroll.js loaded' );

    var offsetVal = $( '#scrollable' ).offset().top;
    var current = $( '.current' );

    if( typeof current !== 'undefined' ) {

        $( 'html, body' ).animate(
            { 
                scrollTop: $( '.current').offset().top - offsetVal
            }, 
            1000);

    }
    
} );