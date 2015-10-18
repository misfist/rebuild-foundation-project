<?php
/**
 * Content filters
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package RebuildFoundation
 */


// Site Filters

if(! function_exists( 'rebuild_site_category_filter' ) ) {

    function rebuild_site_category_filter() {

        $sites = get_terms( 'rebuild_site_category' );
    
        if( !empty( $sites ) ) {

            $post_type = get_post_type( get_the_ID() );
            $post_type_obj = get_post_type_object( $post_type );

            echo '<ul class="site-filter">';

            foreach( $sites as $site ) {

                echo '<li>';

                if( $post_type && $post_type_obj->has_archive ) {

                    echo '<a href="' . esc_url( add_query_arg( 'site_category', $site->slug, site_url( $post_type_obj->has_archive ) ) ) . '">' . $site->name . '</a>';

                } else {

                    echo '<a href="' . esc_url( add_query_arg( 'site_category', $site->slug ) ) . '">' . $site->name . '</a>';
                }

                echo '</li>';

            }

            echo '</ul>';      

        }

        return;

    } 

}

// Exhibition Scope Filters

// Event Year Filters

// Event Month Filters
