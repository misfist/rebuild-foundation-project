jQuery( document ).ready( function( $ ) {

    $( 'article' ).click( function( event ) {

        $( this ).toggleClass( 'expanded' );
        $( this ).siblings().removeClass( 'expanded' );

    } );

} );