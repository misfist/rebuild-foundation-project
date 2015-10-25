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

/**
 * Get the pretty link 
 * @return string
 */

if(! function_exists( 'rebuild_get_pretty_link' ) ) {

    function rebuild_get_pretty_link( $post_type ) {

        $post_type_obj = get_post_type_object( $post_type );

        $post_type_slug = ( 'post' == $post_type ) ? 'blog' : $post_type_obj->rewrite['slug'] . 's' ;

        return '/' . $post_type_slug;

    } 

}


/**
 * Get site link
 * Returns link to site page
 * @return string
 */

if(! function_exists( 'rebuild_get_site_link' ) ) {

    function rebuild_get_site_link() {

        $site_name = get_rebuild_site_name();
        $site_slug = get_rebuild_site_slug();

        if( $site_name && $site_slug ) {

            return '<a href="' . esc_url( home_url( '/site/' . $site_slug ) ) . '">' . $site_name . '</a>';

        }

        return;

    } 

}


/**
 * Get site category link
 * Renders link to site_category content relative to current content
 * Uses `add_query_arg` to render link
 * @return string
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

                $link = '<div class="meta site-cat-link"><label>All</label> <a href="' . esc_url( add_query_arg( 'site_category', $site_slug, site_url( $post_type_obj->has_archive ) ) ) . '">' . $site_name . ' ' . $post_type_name . '</a></div>';

            } else {

                $link = '<div class="meta site-cat-link"><label>All</label> <a href="' . esc_url( add_query_arg( 'site_category', $site_slug, $pretty_link ) ) . '">' . $site_name . ' ' . $post_type_name . '</a></div>';

            }

            echo $link;

        }

        return;

    } 

}


/**
 * Prints HTML with site category link for current content item
 */

if(! function_exists( 'rebuild_foundation_site_cat_link' ) ) {

    function rebuild_foundation_site_cat_link() {
        
        global $wp;

        $site_category =  wp_get_post_terms( get_the_ID(), 'rebuild_site_category', array( 'fields' => 'all' ) );

        if ( is_wp_error( $site_category ) ) {
            continue;
        }

        $term_link = get_term_link( $site_category[0]->term_id, 'rebuild_site_category' );

        $post_type_obj = get_post_type_object( get_post_type( get_the_ID() ) );

        $post_type_slug = rebuild_get_pretty_link( get_post_type( get_the_ID() ) );

        // Tip on how to get current url
        // https://kovshenin.com/2012/current-url-in-wordpress/
        printf( '<div class="meta site-cat-link"><label>Other</label> <a href="' . esc_url( home_url( $post_type_slug . '/site/' . $site_category[0]->slug ) ) . '">' . $site_category[0]->name . ' ' . $post_type_obj->labels->name . '</a></div>' );

    }

}


/**
 * Prints HTML with site link associated with current category (via 'rebuild_site_category')
 */

if(! function_exists( 'rebuild_foundation_site_link' ) ) {

    function rebuild_foundation_site_link() {

        // Get the current site category
        $site_category =  wp_get_post_terms( get_the_ID(), 'rebuild_site_category', array( 'fields' => 'slugs' ) );

        if ( is_wp_error( $site_category ) ) {
            continue;
        }

        wp_reset_postdata();

        $the_site_args = array(
            'post_type' => 'rebuild_site',
            'numberposts' => 1,
            'tax_query' => array(
                'taxonomy' => 'rebuild_site_category',
                'field'    => 'slug',
                'terms'    => $site_category[0],
            ),
        );

        $the_site = get_posts( $the_site_args );

        if( count( $the_site ) > 0 ) {

            $name = get_field( 'short_name', $the_site[0]->ID );

            $site_name = ( $name ) ? $name : $the_site[0]->post_title;

            printf( '<div class="meta site-link">' . __( '<label>View</label> ', 'rebuild-foundation' ) . '<a href="' . esc_url( home_url( '/site/' . $the_site[0]->post_name ) ) . '">' . $site_name . '</a></div>' );

        }

    }

}


if ( ! function_exists( 'rebuild_foundation_entry_footer' ) ) :
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function rebuild_foundation_entry_footer() {

        // Posts, Events, Exhibitions, Sites

        // Archives
        // Posts, Events, Exhibitions
        if( is_post_type_archive( array( 'post', 'rebuild_event', 'rebuild_exhibition' ) ) ) {

            if( function_exists( 'rebuild_get_site_category_content' ) ) {

                rebuild_get_site_category_content();

            }

            if( function_exists( 'rebuild_get_site_link' ) ) {

                rebuild_get_site_link();

            }

        }

        // Posts/Blog
        // category, post_tag
        if( is_post_type_archive( 'post' ) ) {

            $tags_list = get_the_tag_list( '', esc_html__( ', ', 'rebuild-foundation' ) );
            if ( $tags_list ) {
                printf( '<span class="meta tags-links">' . esc_html__( 'Tagged %1$s', 'rebuild-foundation' ) . '</span>', $tags_list ); // WPCS: XSS OK.
            }

        }

        // Sites
        // No links

        // Single

        // Events
        if ( is_singular( 'rebuild_event' ) ) { 

            $eventcat_list =  get_the_term_list( get_the_ID(), 'rebuild_event_category', '', ', ' );

            $eventtag_list =  get_the_term_list( get_the_ID(), 'rebuild_event_tag', '', ', ' );

            if ( $eventcat_list ) {
                printf( '<div class="meta eventcat-links">' . esc_html__( 'Posted in %1$s', 'rebuild-foundation' ) . '</div>', $eventcat_list ); // WPCS: XSS OK.
            }

            if ( $eventtag_list ) {
                printf( '<div class="meta eventtag-links">' . esc_html__( 'Tagged %1$s', 'rebuild-foundation' ) . '</div>', $eventtag_list ); // WPCS: XSS OK.
            }

        }


        // Posts, Events, Exhibitions
        if( is_singular( array( 'post', 'rebuild_event', 'rebuild_exhibition' ) ) ) {

             if( function_exists( 'rebuild_get_site_category_content' ) ) {

                rebuild_get_site_category_content();

            }

            if( function_exists( 'rebuild_get_site_link' ) ) {

                rebuild_get_site_link();

            }


        }

        // Sites
        // site_category for 'post_type' = 'rebuild_site'
        if( is_singular( 'rebuild_site' ) ) {

             if( function_exists( 'rebuild_get_site_category_content' ) ) {

                rebuild_get_site_category_content();

            }

        }
        

        // Posts/Blog
        // category, post_tag, site_category for 'post_type' = 'post'

        // Events
        // event_category, event_tag, site_category for 'post_type' = 'rebuild_event'

        // Exhibitions
        // exhibition_category, site_category for 'post_type' = 'rebuild_exhibition'

        // Sites
        // site_category for 'post_type' = 'rebuild_site'


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
 * Get slug
 * Retrieves the slug of current content
 * @return string or @echo string
 */

if(! function_exists( 'the_slug' ) ) {

    function the_slug( $echo = true ) {

      $slug = basename( get_permalink() );

      do_action( 'before_slug', $slug );

      $slug = apply_filters( 'slug_filter', $slug );

      echo ( $echo ) ? $slug : '';

      do_action('after_slug', $slug);

      return $slug;

    }

}

/**
 * Get site category
 * Retrieves the rebuild_site_category for current content
 * @return string
 */

if(! function_exists( 'rebuild_get_site_category' ) ) {

    function rebuild_get_site_category() {

        $site_cats = get_the_terms( get_the_ID(), 'rebuild_site_category' );

        if( !empty( $site_cats ) ) {

            return $site_cats[0]->slug;

        }

        return;

    }

}


/**
 * Get site slug
 * Retrieves the slug of the site
 * @return string
 */

if(! function_exists( 'get_rebuild_site_slug' ) ) {

    function get_rebuild_site_slug() {

        if( 'rebuild_site' == get_post_type() ) {

            $slug = basename( get_permalink( get_the_ID() ) );

            
        } else {

            $site_cats = get_the_terms( get_the_ID(), 'rebuild_site_category' );

            // Get site category slug - it should match site post slug
            $site_cat_slug = ( !empty( $site_cats ) ) ? $site_cats[0]->slug : '';

            // Get site associated with that category
            // https://codex.wordpress.org/Function_Reference/get_page_by_path
            $site = get_page_by_path( $site_cat_slug, OBJECT, 'rebuild_site' );

            $slug = ( !empty( $site ) ) ? $site->post_name : '';

        }

        return $slug;

    }

}


/**
 * Get site name
 * Retrieves the short name `short_name` or `post_title` of the site
 * @return string
 */

if(! function_exists( 'get_rebuild_site_name' ) ) {

    function get_rebuild_site_name() {

        if( 'rebuild_site' == get_post_type() ) {

            $site_short_name = get_post_meta( get_the_ID(), 'short_name', true );
            return ( $site_short_name ) ? $site_short_name : get_the_title( get_the_ID() );

        } else {

            $site_cats = get_the_terms( get_the_ID(), 'rebuild_site_category' );

            // If there is a site category associated with content
            if( !empty( $site_cats ) ) {

                // Get site category slug - matches site post slug
                $site_slug = $site_cats[0]->slug;

                // Get site associated with that category
                // https://codex.wordpress.org/Function_Reference/get_page_by_path
                $site = get_page_by_path( $site_slug, OBJECT, 'rebuild_site' );

                $site_short_name = get_post_meta( $site->ID, 'short_name', true );

                // If it has short name field return that
                // Else return the_title
                return ( $site_short_name ) ? $site_short_name : get_the_title( $site->ID );

            }

            return;

        }

    }

}

/**
 * Get site link
 * Returns link to site page
 * @return string
 */

if(! function_exists( 'rebuild_get_site_link' ) ) {

    function rebuild_get_site_link() {

        $site_name = get_rebuild_site_name();
        $site_slug = get_rebuild_site_slug();

        if( $site_name && $site_slug ) {

            return '<a href="/site/' . $site_slug . '">' . $site_name . '</a>';

        }

        return;

    } 

}


/**
 * Get site category link
 * Renders link to site_category content relative to current content
 * @return string
 */

if(! function_exists( 'rebuild_get_site_category_content' ) ) {

    function rebuild_get_site_category_content() {

        $site_name = get_rebuild_site_name();
        $site_slug = get_rebuild_site_slug();

        if( $site_name && $site_slug ) {

            $post_type = get_post_type();
            $post_type_obj = get_post_type_object( $post_type );

            if( $post_type_obj->has_archive ) {
                
                // https://developer.wordpress.org/reference/functions/add_query_arg/

                $link = '<a href="' . esc_url( add_query_arg( 'site_category', $site_slug, site_url( $post_type_obj->has_archive ) ) ) . '">' . $site_name . '</a>';

            } else {

                $link = '<a href="' . esc_url( add_query_arg( 'site_category', $site_slug ) ) . '">' . $site_name . '</a>';

            }

            echo $link;

        }

        return;

    } 

}


/**
 * Get location information
 * Retrieves the address associated with the content
 * @return array
 */

if(! function_exists( 'rebuild_get_location_fields' ) ) {

    function rebuild_get_location_fields() {

        if( function_exists( 'get_field' ) ) {

            $location_id = get_field( 'location' );

            $location_address = get_field( 'location_address', $location_id );

            if( $location_address ) {
                // make an array
                $location = [];
                
                $address = $location_address['address'];
                $address_fields = explode( ', ' , $address );
                $location['address1'] = $address_fields[0];
                $location['address2'] = $address_fields[1] . ', ' . $address_fields[2];

                return $location;  

            } 

            return;

        }
    }
}

if(! function_exists( 'rebuild_formatted_address' ) ) {

    function rebuild_formatted_address() {

        $location = rebuild_get_location_fields();

        if( $location && is_array( $location ) ) {

            echo '<span class="address street">' . $location['address1'] . '</span> <span class="address city-state">' . $location['address2'] . '</span>';

        }

        return;

    }

}


