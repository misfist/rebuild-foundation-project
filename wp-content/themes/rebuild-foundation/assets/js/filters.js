/*jslint white: true */

jQuery( document ).ready( function( $ ) {

    //console.log( 'filters.js loaded' );

    var queryVars = getUrlVars();

    function getUrlVars() {

        var url = window.location.href;
        var vars = {};
        var hashes = url.split( '?' )[1];

        if( hashes ) {
            var hash = hashes.split( '&' );
        } else {
            return false;
        }
        
        for ( var i = 0; i < hash.length; i++ ) {
            params=hash[i].split( '=' );
            vars[params[0]] = params[1];
        }

        return vars;
    }

    if( queryVars.event_year ) {
        $( 'li[data-event_year=' + queryVars.event_year + ']' ).addClass( 'active' );
    } else {
        var defaultYear = new Date().getFullYear();
        $( 'li[data-event_year=' + defaultYear + ']' ).addClass( 'active' );
    }

    $( 'li[data-event_month=' + queryVars.event_month + ']' ).addClass( 'active' );

    $( 'li[data-site_category=' + queryVars.site_category + ']' ).addClass( 'active' );

    $( 'li[data-event_category=' + queryVars.event_category + ']' ).addClass( 'active' );

    $( 'li[data-exhibition_category=' + queryVars.exhibition_category + ']' ).addClass( 'active' );


} );