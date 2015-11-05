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
                    $sort = array(
                      'orderby' => 'id',
                      'order' => 'DESC'
                    );
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

            // Add sorting if $sort is set
            $terms = ( isset( $sort ) ) ? get_terms( $taxonomy, $sort ) : get_terms( $taxonomy );

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
                    $date_query = rebuild_events_meta_query_vars();

                    if( $site_category ) {
                        
                        $term_args['tax_query'][][1] = array(
                            'taxonomy' => 'site_category',
                            'field' => 'slug',
                            'terms' => $site_category
                        );

                    }

                    if( $date_query ) {

                       $term_args['meta_query'] = $date_query;

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
              echo '<a href="' . esc_url( add_query_arg( $url, home_url( '/events' ) ) ) . '">';
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

            $post_type = rebuild_get_page_type();

            // If not an archive page or single page, bail
            if( 'archive' != $post_type && 'single' != $post_type && 'tax' != $post_type  ) {
              return;
            }

            // If this is an archive, year is query_var or current year
            if( 'archive' == $post_type ) {

              $event_year = get_query_var( 'event_year' );
              $year = ( $event_year ) ? absint( $event_year ) : date( 'Y' );

            } else { // Otherwise, this is a single event, so year is the year of the current event

              $fields = get_fields( get_the_ID() );
              $year = ( isset( $fields['start_date'] ) ) ? date( 'Y', strtotime( $fields['start_date'] ) ) : date( 'Y' );

            } 

            if( array_key_exists( $year, $dates ) ) {

              asort( $dates[$year] );

              $months = $dates[$year];

              echo '<ul class="event-month-filter">';

              foreach( $months as $month ) {

                if( events_for_months( $year, $month ) ) {

                  $query_arg = ( 'single' == $post_type ) ?  add_query_arg( 'event_month', $month, home_url( 'events' ) ) : add_query_arg( 'event_month', $month );

                  echo '<li data-target-month="' . $month . '" data-event_month="' . $month . '">';
                  echo '<a href="' . esc_url( $query_arg ) . '">';
                  echo date( 'M', mktime( 0, 0, 0, $month, 10 ) );
                  echo '</a>';
                  echo '</li>';

                }


              }

              echo '</ul>';

            }


        }

        return;

    }

}


