<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package RebuildFoundation
 */

/**
 * Content Footer
 * Prints HTML with meta information for the categories, tags and comments.
 */

if ( ! function_exists( 'rebuild_foundation_entry_footer' ) ) :
    function rebuild_foundation_entry_footer() {

        // Archives
        if( is_post_type_archive( array( 'post', 'event', 'exhibition' ) ) ) {

            if( function_exists( 'rebuild_get_site_category_content' ) ) {

                rebuild_get_site_category_content();

            }

            if( function_exists( 'rebuild_get_site_link' ) ) {

                rebuild_get_site_link();

            }

        }

        // category, post_tag
        if( is_post_type_archive( 'post' ) ) {

            $tags_list = get_the_tag_list( '', esc_html__( ', ', 'rebuild-foundation' ) );
            if ( $tags_list ) {
                printf( '<span class="meta tags-links">' . esc_html__( 'Tagged %1$s', 'rebuild-foundation' ) . '</span>', $tags_list ); // WPCS: XSS OK.
            }

        }

        // Event
        if ( is_singular( 'event' ) ) { 

            $eventcat_list =  get_the_term_list( get_the_ID(), 'event_category', '', ', ' );

            $eventtag_list =  get_the_term_list( get_the_ID(), 'event_tag', '', ', ' );

            if ( $eventcat_list ) {
                printf( '<div class="meta eventcat-links">' . esc_html__( 'Posted in %1$s', 'rebuild-foundation' ) . '</div>', $eventcat_list ); // WPCS: XSS OK.
            }

            if ( $eventtag_list ) {
                printf( '<div class="meta eventtag-links">' . esc_html__( 'Tagged %1$s', 'rebuild-foundation' ) . '</div>', $eventtag_list ); // WPCS: XSS OK.
            }

        }

        // Posts, Events, Exhibitions
        if( is_singular( array( 'post', 'event', 'exhibition' ) ) ) {

             if( function_exists( 'rebuild_get_site_category_content' ) ) {

                rebuild_get_site_category_content();

            }

            if( function_exists( 'rebuild_get_site_link' ) ) {

                rebuild_get_site_link();

            }

        }

        
        if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
            echo '<span class="comments-link">';
            comments_popup_link( esc_html__( 'Leave a comment', 'rebuild-foundation' ), esc_html__( '1 Comment', 'rebuild-foundation' ), esc_html__( '% Comments', 'rebuild-foundation' ) );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                /* translators: %s: Name of current post */
                esc_html__( 'Edit %s', 'rebuild-foundation' ),
                the_title( '<span class="screen-reader-text">"', '"</span>', false )
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
endif;

/**
 * Byline
 * Prints HTML with meta information for the current post-date/time and author.
 */

if ( ! function_exists( 'rebuild_foundation_posted_on' ) ) :

    function rebuild_foundation_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( 'c' ) ),
            esc_html( get_the_modified_date() )
        );

        $posted_on = sprintf(
            esc_html_x( 'Posted on %s', 'post date', 'rebuild-foundation' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

        $byline = sprintf(
            esc_html_x( 'by %s', 'post author', 'rebuild-foundation' ),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
        );

        if ( 'post' === get_post_type() ) {
            echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
        }

    }
endif;


/**
 * Print site category link
 * Renders link to site_category content relative to current content
 * Uses `add_query_arg` to render link
 * @return print string
 */

if(! function_exists( 'rebuild_get_site_category_content' ) ) {

    function rebuild_get_site_category_content() {

        $site_name = get_rebuild_site_name();
        $site_slug = get_rebuild_site_slug();

        if( $site_name && $site_slug ) {

            $post_type = get_post_type();
            $post_type_obj = get_post_type_object( $post_type );
            $post_type_name = $post_type_obj->labels->name;

            $pretty_link = esc_url( rebuild_get_pretty_link( $post_type ) );

            if( $post_type_obj->has_archive ) {
                
                // https://developer.wordpress.org/reference/functions/add_query_arg/

                $link = '<div class="meta site-cat-link"><a href="' . esc_url( add_query_arg( 'site_category', $site_slug, site_url( $post_type_obj->has_archive ) ) ) . '"><label>all</label> ' . $site_name . ' <label>' . $post_type_name . '</label></a></div>';

            } else {

                $link = '<div class="meta site-cat-link"><a href="' . esc_url( add_query_arg( 'site_category', $site_slug, $pretty_link ) ) . '"><label>all</label> ' . $site_name . ' <label>' . $post_type_name . '</label></a></div>';

            }

            echo $link;

        }

        return;

    } 

}


/**
 * Display Formatted Address
 * Renders formatted address
 * @return print string
 */

if(! function_exists( 'rebuild_formatted_address' ) ) {

    function rebuild_formatted_address() {

        $post_id = get_the_id();

        $location = rebuild_get_location_fields( $post_id );

        if( $location && is_array( $location ) ) {

            echo '<span class="address street">' . $location['address1'] . '</span> <span class="address city-state">' . $location['address2'] . '</span>';

        }

        return;

    }

}

/**
 * Image Caption
 * If caption exists, returns caption
 * @return string
 */

if(! function_exists( 'rebuild_get_the_feature_caption' ) ) {

    function rebuild_get_the_feature_caption() {

      global $post;

      $thumbnail_id    = get_post_thumbnail_id( $post->ID );
      $thumbnail_image = get_posts( array( 
        'p' => $thumbnail_id, 
        'post_type' => 'attachment' ) );

      if ( $thumbnail_image && isset( $thumbnail_image[0] ) ) {
        return $thumbnail_image[0]->post_excerpt;
      }

      return;

    }
}


/**
 * Google Map Link
 * If location exists make google map link
 * e.g. https://maps.google.com?q=760+West+Genesee+Street+Syracuse+NY+13204
 * @return echo url string
 */

if(! function_exists( 'rebuild_google_map_link' ) ) {

    function rebuild_google_map_link() {

        $address = rebuild_urlencode_location( get_the_ID() );

        $google_url = 'https://maps.google.com?q=';

        echo '<div class="google-map-link">';

        echo '<a href="' . esc_url( 'https://maps.google.com?q=' . $address ) . '" target="_blank">';

        echo __( 'map', 'rebuild-foundation' );

        echo '</a>';

        echo '</div>';

    }

}




