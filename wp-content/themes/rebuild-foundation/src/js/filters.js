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

    if( 'archive' == pageInfo.pageType || 'tax' == pageInfo.pageType ) {

        // console.log( 'Here is an archive' );

        var defaultYear = new Date().getFullYear();
        var defaultMonth = new Date().getMonth() + 1;

        // console.log( typeof queryVars.event_year );
        // console.log( typeof queryVars.event_month );

        switch ( true ) {

            // Year and month
            case ( typeof queryVars.event_year != 'undefined' ) && ( typeof queryVars.event_month != 'undefined' ):

                // year = year & month = month
                // console.log( 'Year and month' );
                $( 'li[data-event_year=' + queryVars.event_year + ']' ).addClass( 'active' );
                $( 'li[data-event_month=' + queryVars.event_month + ']' ).addClass( 'active' );
                
                break;

            //Year, but no month
            case ( ( typeof queryVars.event_year != 'undefined' ) && ( typeof queryVars.event_month == 'undefined' ) ):
                console.log( 'Year, but no month' );

                if( queryVars.event_year != defaultYear ) {
                    // year = year & month = null
                    // console.log( 'Different year' );
                    $( 'li[data-event_year=' + queryVars.event_year + ']' ).addClass( 'active' );
                    // $( 'li[data-event_year=' + defaultYear + ']' ).addClass( 'active' );
                    // $( 'li[data-event_month=' + defaultMonth + ']' ).addClass( 'active' );
                } else { 
                    // year = year & month = defaultMonth
                    // console.log( 'Current year' );
                    $( 'li[data-event_year=' + defaultYear + ']' ).addClass( 'active' );
                    $( 'li[data-event_month=' + defaultMonth + ']' ).addClass( 'active' );
                }

                break;

            // No year, but month
            case ( typeof queryVars.event_year == 'undefined' ) && ( typeof queryVars.event_month != 'undefined' ):

                // year = defaultYear & month = month
                // console.log( 'No year, but month' );
                $( 'li[data-event_year=' + defaultYear + ']' ).addClass( 'active' );
                $( 'li[data-event_month=' + queryVars.event_month + ']' ).addClass( 'active' );

                break;

            // Default: No year, no month
            default :

                // Default: No year, no month
                console.log( 'Default: No year, no month' );
                $( 'li[data-event_year=' + defaultYear + ']' ).addClass( 'active' );
                $( 'li[data-event_month=' + defaultMonth + ']' ).addClass( 'active' );

        }

        $( 'li[data-site_category=' + queryVars.site_category + ']' ).addClass( 'active' );

        $( 'li[data-event_category=' + queryVars.event_category + ']' ).addClass( 'active' );

        $( 'li[data-exhibition_category=' + queryVars.exhibition_category + ']' ).addClass( 'active' );

    } else if( 'single' == pageInfo.pageType ){

        // console.log( 'Here is a single page' );

        var date = new Date( pageInfo.startDate );
        var eventYear = date.getFullYear();
        var eventMonth = ( ( date.getMonth() + 1 ) < 10 ? '0' : '' ) + ( date.getMonth() + 1 );

        // console.log( eventMonth );

        $( 'li[data-event_year=' + eventYear + ']' ).addClass( 'active' );
        $( 'li[data-event_month=' + eventMonth + ']' ).addClass( 'active' );

    }

} );