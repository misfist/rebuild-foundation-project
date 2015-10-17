<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package RebuildFoundation
 */

if ( ! function_exists( 'rebuild_foundation_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
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

// ?post_type=rebuild_exhibitions

if ( ! function_exists( 'rebuild_foundation_entry_footer' ) ) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function rebuild_foundation_entry_footer() {

        if( function_exists( 'rebuild_foundation_get_site_link' ) ) {
            $site_link = rebuild_foundation_get_site_link();
        }

        if( isset( $site_link ) ) {

            printf( '<span class="meta site-links">' . esc_html__( 'Site %1$s', 'rebuild-foundation' ) . '</span>', $site_link ); // WPCS: XSS OK

        }

        if ( 'event' === get_post_type() ) { 
            /* translators: used between list items, there is a space after the comma */
            $eventcat_list =  get_the_term_list( get_the_ID(), 'event-categories', '', ', ' );
            if ( $eventcat_list ) {
                printf( '<span class="meta eventcat-links">' . esc_html__( 'Tagged %1$s', 'rebuild-foundation' ) . '</span>', $eventcat_list ); // WPCS: XSS OK.
            }

         } else {

            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list( esc_html__( ', ', 'rebuild-foundation' ) );
            if ( has_category() ) {

                if( 'rebuild_exhibitions' === get_post_type() ) {
                    printf( '<span class="meta category-links">' . esc_html__( 'Category %1$s', 'rebuild-foundation' ) . '</span>', $categories_list ); // WPCS: XSS OK.
                } else {
                    printf( '<span class="meta category-links">' . esc_html__( 'Category %1$s', 'rebuild-foundation' ) . '</span>', $categories_list ); // WPCS: XSS OK.
                }
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list( '', esc_html__( ', ', 'rebuild-foundation' ) );
            if ( $tags_list ) {
                printf( '<span class="meta tags-links">' . esc_html__( 'Tagged %1$s', 'rebuild-foundation' ) . '</span>', $tags_list ); // WPCS: XSS OK.
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
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */

function rebuild_foundation_categorized_blog() {
    if ( false === ( $all_the_cool_cats = get_transient( 'rebuild_foundation_categories' ) ) ) {

        wp_get_post_terms( 'rebuild_sites_category', array( 
            'fields' => 'ids',
            'hide_empty' => 1,
            // We only need to know if there is more than one category.
            'number'     => 2,
            )
        );

        // Count the number of categories that are attached to the posts.
        $all_the_cool_cats = count( $all_the_cool_cats );

        set_transient( 'rebuild_foundation_categories', $all_the_cool_cats );
    }

    return true;
}

/**
 * Flush out the transients used in rebuild_foundation_categorized_blog.
 */
function rebuild_foundation_category_transient_flusher() {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // Like, beat it. Dig?
    delete_transient( 'rebuild_foundation_categories' );
}
add_action( 'edit_category', 'rebuild_foundation_category_transient_flusher' );
add_action( 'save_post',     'rebuild_foundation_category_transient_flusher' );


/**
 * Get site full name
 * Retrieves the `post_title` of the site
 * @return string
 */

if(! function_exists( 'get_rebuild_site_full_name' ) ) {

    function get_rebuild_site_full_name() {

        if( 'rebuild_sites' == get_post_type() ) {

            return get_the_title( get_the_ID() );

        } else {

            $site_id = get_post_meta( get_the_ID(), '_rebuild_site', true );

            if( isset( $site_id ) && !empty( $site_id ) && is_numeric( $site_id ) ) {
                if( is_array( $site_id ) ) {
                    $site_id = implode( '', $site_id );
                } 
                $site_info = get_post( $site_id );

                var_dump( $site_id );

                return $site_info->post_title;

            }

        }

    }

}

/**
 * Get site name
 * Retrieves the short name `_rebuild_site_short_name` or `post_title` of the site
 * @return string
 */

if(! function_exists( 'get_rebuild_site_name' ) ) {

    function get_rebuild_site_name() {

        if( 'rebuild_sites' == get_post_type() ) {
            $site_short_name = get_post_meta( get_the_ID(), '_rebuild_site_short_name', true );
            return ( $site_short_name ) ? $site_short_name : get_the_title( get_the_ID() );
        } else {
            $site_id = get_post_meta( get_the_ID(), '_rebuild_site', true );
            
            if( isset( $site_id ) && !empty( $site_id ) && is_numeric( $site_id ) ) {
                if( is_array( $site_id ) ) {
                    $site_id = implode( '', $site_id );
                } 
                $site_info = get_post( $site_id );
                $site_short_name = get_post_meta( $site_id, '_rebuild_site_short_name', true );

                return ( $site_short_name ) ? $site_short_name : $site_info->post_title;

            }

        }

    }

}

/**
 * Get location information
 * Retrieves the address associated with the content
 * @return array
 */

if(! function_exists( 'get_location_fields' ) ) {
    function get_location_fields() {

        if( function_exists( 'get_field' ) ) {

            $location = [];
            $location_address = get_field( 'location_address' );

            if( isset( $location_address ) && is_array( $location_address ) ) {

                $address = $location_address['address'];
                $address_fields = explode( ', ' , $address );
                $location['address1'] = $address_fields[0];
                $location['address2'] = $address_fields[1] . ', ' . $address_fields[2];

                return $location;  

            } else {

                // Error, no location assigned

            }
        }
    }
}

if(! function_exists( 'rebuild_get_site_link' ) ) {

    function rebuild_get_site_link() {

        $sites = get_the_terms( get_the_ID(), 'rebuild_sites' );
        $permalink = ( $sites && ! is_wp_error( $sites ) )  ? get_the_permalink( $sites[0]->term_id ) : '';
        $site_name = ( $sites && ! is_wp_error( $sites ) )  ? $sites[0]->name : '';
        $site_link = '<a href="' . $permalink . '">' . $site_name . '</a>';
        return ( $sites && ! is_wp_error( $sites ) ) ? $site_link : '';

    } 

}



if(! function_exists( 'rebuild_get_location' ) ) {

    function rebuild_get_location() {

        $locations = get_posts( array(
          'connected_type' => 'location_to_all',
          'connected_items' => get_post(),
          'nopaging' => true,
          'suppress_filters' => false,
          'posts_per_page' => 1
        ) );

        $location_id = ( !empty( $locations ) ) ? $locations[0]->ID : '' ;

        $address = get_field( 'location_address', $location_id );

        return ( !empty( $address ) ) ? get_field( 'location_address', $location_id )['address'] : '';

    }

}

if(! function_exists( 'rebuild_get_location_name' ) ) {

    function rebuild_get_location_name() {

        $locations = get_posts( array(
          'connected_type' => 'location_to_all',
          'connected_items' => get_post(),
          'nopaging' => true,
          'suppress_filters' => false,
          'posts_per_page' => 1
        ) );

        $location_id = ( !empty( $locations ) ) ? $locations[0]->ID : '' ;

        $location_name = ( '' != $location_id ) ? get_the_title( $location_id ) : '';

        return $location_name;

    }

}