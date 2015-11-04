<?php 

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
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */

function rebuild_foundation_categorized_blog() {
    if ( false === ( $all_the_cool_cats = get_transient( 'rebuild_foundation_categories' ) ) ) {

        wp_get_post_terms( 'sites_category', array( 
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
 * Get site slug
 * Retrieves the slug of the site
 * @return string
 */

if(! function_exists( 'get_rebuild_site_slug' ) ) {

    function get_rebuild_site_slug() {

        if( 'site' == get_post_type() ) {

            $slug = basename( get_permalink( get_the_ID() ) );

            
        } else {

            $site_cats = get_the_terms( get_the_ID(), 'site_category' );

            // Get site category slug - it should match site post slug
            $site_cat_slug = ( !empty( $site_cats ) ) ? $site_cats[0]->slug : '';

            // Get site associated with that category
            // https://codex.wordpress.org/Function_Reference/get_page_by_path
            $site = get_page_by_path( $site_cat_slug, OBJECT, 'site' );

            $slug = ( !empty( $site ) ) ? $site->post_name : '';

        }

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

        $site_cats = get_the_terms( get_the_ID(), 'site_category' );

        if( !empty( $site_cats ) ) {

            return $site_cats[0]->slug;

        }

        return;

    }

}

/**
 * Get Page Type
 * Retrieves page type of current content
 * @return string
 */

if(! function_exists( 'rebuild_get_page_type' ) ) {

    function rebuild_get_page_type() {

        global $wp_query;
        
        $loop = 'notfound';

        if ( $wp_query->is_404 ) {
            $loop = 'notfound';
        } elseif ( $wp_query->is_page ) {
            $loop = is_front_page() ? 'front' : 'page';
        } elseif ( $wp_query->is_home ) {
            $loop = 'home';
        } elseif ( $wp_query->is_single ) {
            $loop = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
        } elseif ( $wp_query->is_category ) {
            $loop = 'category';
        } elseif ( $wp_query->is_tag ) {
            $loop = 'tag';
        } elseif ( $wp_query->is_tax ) {
            $loop = 'tax';
        } elseif ( $wp_query->is_archive ) {
            if ( $wp_query->is_day ) {
                $loop = 'day';
            } elseif ( $wp_query->is_month ) {
                $loop = 'month';
            } elseif ( $wp_query->is_year ) {
                $loop = 'year';
            } elseif ( $wp_query->is_author ) {
                $loop = 'author';
            } else {
                $loop = 'archive';
            }
        } elseif ( $wp_query->is_search ) {
            $loop = 'search';
        } 

        return $loop;
    }

}


?>