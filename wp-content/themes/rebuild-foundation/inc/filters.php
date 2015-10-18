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
    
        if( count( $sites ) > 0 ) {

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

/**
 * Taxonomy filter
 * Renders taxonomy links based on post_type, excludes empty
 * @return echo string
 */

if(! function_exists( 'rebuild_taxonomy_filter' ) ) {

    function rebuild_taxonomy_filter() {

        $post_type = get_post_type( get_the_ID() );
        $post_type_obj = get_post_type_object( $post_type );

        if( $post_type ) {

            switch( $post_type ) {

                case 'rebuild_exhibition':
                    $taxonomy = 'rebuild_exhibition_category';
                    $query_var = get_taxonomy( $taxonomy )->query_var;
                    break;
                case 'rebuild_event':
                    $taxonomy = 'rebuild_event_category';
                    $query_var = get_taxonomy( $taxonomy )->query_var;
                    break;
                case 'rebuild_site':
                    $taxonomy = 'rebuild_site_category';
                    $query_var = get_taxonomy( $taxonomy )->query_var;
                    break;
                default: 
                    $taxonomy = 'category';
                    $slug = 'blog';

            }

            $terms = get_terms( $taxonomy );

            if( count( $terms ) > 0 ) {

                echo '<ul class="' . $post_type . '-filter">';

                foreach( $terms as $term ) {

                    // If terms have posts of type currently in, show them
                    $term_args = array(
                        'post_type' => $post_type,
                        'tax_query' => array(
                                array(
                                    'taxonomy' => $taxonomy,
                                    'field' => 'slug',
                                    'terms' => $term->slug
                                )
                            )
                        );

                    // If query vars for rebuild_site_category set, add to tax query

                    $site_category = get_query_var( 'site_category' );

                    if( $site_category ) {
                        
                        $term_args['tax_query'][][1] = array(
                            'taxonomy' => 'rebuild_site_category',
                            'field' => 'slug',
                            'terms' => $site_category
                        );
                    }

                    $terms_with_posts = get_posts( $term_args );

                    if( $terms_with_posts ) {

                        echo '<li>';

                        echo '<a href="' . esc_url( add_query_arg( $query_var, $term->slug ) ) . '">' . $term->name . '</a>';

                        echo '</li>';

                    }

                }

                echo '</ul>';

            }

        }

        return;

    } 

}

// Event Year Filters

// Event Month Filters
