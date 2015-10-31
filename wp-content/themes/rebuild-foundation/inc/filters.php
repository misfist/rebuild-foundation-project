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

        $taxonomy = 'site_category';

        $sites = get_terms( $taxonomy );
    
        if( count( $sites ) > 0 ) {

            $post_type = get_post_type( get_the_ID() );
            $post_type_obj = get_post_type_object( $post_type );

            echo '<ul class="site-filter">';

            foreach( $sites as $site ) {

                echo '<li data-' . $taxonomy . '="' . $site->slug . '" data-target-site="' . $site->slug . '">';

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

                case 'exhibition':
                    $taxonomy = 'exhibition_category';
                    $query_var = get_taxonomy( $taxonomy )->query_var;
                    break;
                case 'event':
                    $taxonomy = 'event_category';
                    $query_var = get_taxonomy( $taxonomy )->query_var;
                    break;
                case 'site':
                    $taxonomy = 'site_category';
                    $query_var = get_taxonomy( $taxonomy )->query_var;
                    break;
                default: 
                    $taxonomy = 'category';
                    $query_var = get_taxonomy( $taxonomy )->query_var;
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

                    // If query vars for site_category set, add to tax query

                    $site_category = get_query_var( 'site_category' );

                    if( $site_category ) {
                        
                        $term_args['tax_query'][][1] = array(
                            'taxonomy' => 'site_category',
                            'field' => 'slug',
                            'terms' => $site_category
                        );
                    }

                    $terms_with_posts = get_posts( $term_args );

                    if( $terms_with_posts ) {

                        echo '<li data-' . $taxonomy . '="' . $term->slug . '" data-target-term="' . $term->slug . '">';

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


/**
 * Year Filter
 * Render years for which there are events, based on current query results?
 * @return echo string
 */

if(! function_exists( 'rebuild_event_year_filter' ) ) {

    function rebuild_event_year_filter() {

        if( function_exists( 'rebuild_get_event_years' ) ) {

            $years = rebuild_get_event_years();

            rsort( $years );

            echo '<ul class="event-year-filter">';

            foreach( $years as $year ) {

              $url = array(
                'event_year' => $year,
                'event_month' => null
              );

              echo '<li data-target-year="' . $year . '" data-event_year="' . $year . '">';
              echo '<a href="' . esc_url( add_query_arg( $url ) ) . '">';
              echo $year;
              echo '</a>';
              echo '</li>';

            }

            echo '</ul>';

        }

        return;

    }

}


/**
 * Month Filter
 * Render years for which there are events, based on current query results?
 * @return echo string
 */

if(! function_exists( 'rebuild_event_month_filter' ) ) {

    function rebuild_event_month_filter() {

        if( function_exists( 'get_dates_by_year' ) ) {

            $dates = get_dates_by_year();

            $long_months = array();

            $event_year = get_query_var( 'event_year' );

            $year = ( $event_year ) ? absint( $event_year ) : date( 'Y' );

            // echo $year;

            if( array_key_exists( $year, $dates ) ) {

              asort( $dates[$year] );

              $months = $dates[$year];

              //var_dump( $months );

              echo '<ul class="event-month-filter">';

              foreach( $months as $month ) {

                if( events_for_months( $year, $month ) ) {

                  echo '<li data-target-month="' . $month . '" data-event_month="' . $month . '">';
                  echo '<a href="' . esc_url( add_query_arg( 'event_month', $month ) ) . '">';
                  echo date( 'M', mktime( 0, 0, 0, $month, 10 ) );
                  echo '</li>';

                }


              }

              echo '</ul>';

            }


        }

        return;

    }

}


