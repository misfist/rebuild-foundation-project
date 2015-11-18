/*jslint white: true */

jQuery( document ).ready( function( $ ) {

    //console.log( 'eventScroll.js loaded' );

    var offsetVal = $( '#scrollable' ).offset().top;
    var current = $( '#scrollable .current' )[0];
    var upcoming = $( '#scrollable .upcoming' )[0];

    if( typeof current !== 'undefined' ) {

        $( 'html, body' ).animate(
            { 
                scrollTop: $( '#scrollable .current' ).offset().top - offsetVal
            }, 
            1000);

    } else if( typeof upcoming !== 'undefined' ){

        $( 'html, body' ).animate(
            { 
                scrollTop: $( '#scrollable .upcoming' ).offset().top - offsetVal
            }, 
            1000);
        
    }
    
} );