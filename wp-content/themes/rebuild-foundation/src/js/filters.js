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

        var defaultYear = new Date().getFullYear();
        var defaultMonth = new Date().getMonth() + 1;

        switch ( true ) {

            // Year and month
            case ( typeof queryVars.event_year != 'undefined' ) && ( typeof queryVars.event_month != 'undefined' ):

                $( 'li[data-event_year=' + queryVars.event_year + ']' ).addClass( 'active' );
                $( 'li[data-event_month=' + queryVars.event_month + ']' ).addClass( 'active' );
                
                break;

            //Year, but no month
            case ( ( typeof queryVars.event_year != 'undefined' ) && ( typeof queryVars.event_month == 'undefined' ) ):
                console.log( 'Year, but no month' );

                if( queryVars.event_year != defaultYear ) {
                    // year = year & month = null
                    $( 'li[data-event_year=' + queryVars.event_year + ']' ).addClass( 'active' );
                } else { 
                    // year = year & month = defaultMonth
                    $( 'li[data-event_year=' + defaultYear + ']' ).addClass( 'active' );
                    $( 'li[data-event_month=' + defaultMonth + ']' ).addClass( 'active' );
                }

                break;

            // No year, but month
            case ( typeof queryVars.event_year == 'undefined' ) && ( typeof queryVars.event_month != 'undefined' ):

                // year = defaultYear & month = month
                $( 'li[data-event_year=' + defaultYear + ']' ).addClass( 'active' );
                $( 'li[data-event_month=' + queryVars.event_month + ']' ).addClass( 'active' );

                break;

            // Default: No year, no month
            default :

                // Default: No year, no month
                $( 'li[data-event_year=' + defaultYear + ']' ).addClass( 'active' );
                $( 'li[data-event_month=' + defaultMonth + ']' ).addClass( 'active' );

        }


        // If now query_var is set, set current to active
        if( typeof queryVars.exhibition_category != 'undefined' ) {

            $( 'li[data-exhibition_category=' + queryVars.exhibition_category + ']' ).addClass( 'active' );            

        // Exhibitions default to current scope. 
        } else if( 'exhibition' == pageInfo.postType ) {

            $( 'li[data-exhibition_category="current"]' ).addClass( 'active' );

        }

        $( 'li[data-site_category=' + queryVars.site_category + ']' ).addClass( 'active' );

        $( 'li[data-event_category=' + queryVars.event_category + ']' ).addClass( 'active' );

    } else if( 'single' == pageInfo.pageType ){

        var date = new Date( pageInfo.startDate );
        var eventYear = date.getFullYear();
        var eventMonth = ( ( date.getMonth() + 1 ) < 10 ? '0' : '' ) + ( date.getMonth() + 1 );

        if ( typeof pageInfo.exhibitionScope != 'undefined' && pageInfo.exhibitionScope != '' ) {

            $( 'li[data-exhibition_category=' + pageInfo.exhibitionScope + ']' ).addClass( 'active' );
        }

        if ( typeof pageInfo.startDate!= 'undefined' && pageInfo.startDate != '' ) {

            var date = pageInfo.startDate.split( '-' );
            
        }

        $( 'li[data-event_year=' + eventYear + ']' ).addClass( 'active' );
        $( 'li[data-event_month=' + eventMonth + ']' ).addClass( 'active' );

    }

} );