<?php

/**
 * Rebuild Foundation Custom Columns
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
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

                echo '<li data-target-site="' . $site->slug . '">';

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

                        echo '<li data-target-term="' . $term->slug . '">';

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

            $year = ( isset( $event_year ) ) ? absint( $event_year ) : date( 'Y' );

            if( $dates[$year] ) {

              asort( $dates[$year] );

              $months = $dates[$year];

              echo '<ul class="event-month-filter">';

              foreach( $months as $month ) {

                if( events_for_months( $year, $month ) ) {

                  echo '<li data-target-month="' . $month . '" data-event_month="' . $month . '">';
                  echo '<a href="' . esc_url( add_query_arg( 'event_month', $month ) ) . '">';
                  echo strftime( '%b', mktime( 0, 0, 0, $month ) );
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


/**
 * Helpers
 */

/**
 * Get all the event start_dates
 * @return array of dates
 */

if(! function_exists( 'rebuild_get_dates' ) ) {

  function rebuild_get_dates() {

    $post_type = 'rebuild_event';
    $args = array(
        'post_type' => $post_type,
    );

    $events = get_posts( $args );
    $dates = [];

    if( count( $events ) > 0 ) {

      foreach( $events as $event ) {

        if( get_post_meta( $event->ID, 'start_date', true ) ) {

          array_push( $dates , get_post_meta( $event->ID, 'start_date', true ) );

        }

      }

    }

    return $dates;

  }

}


/**
 * Get all the event years
 * @return array of year integers
 */

if(! function_exists( 'rebuild_get_event_years' ) ) {

  function rebuild_get_event_years() {

    if( function_exists( 'rebuild_get_dates' ) ) {

      $dates = rebuild_get_dates();

      return array_unique( array_map( 'convert_date_to_year', $dates ) );

    }

  }

}


/**
 * Get dates by year
 * @return array
 */

if(! function_exists( 'get_dates_by_year' ) ) {

  function get_dates_by_year() {

    $years = get_unique_years();
    $dates = rebuild_get_dates();
    $date_array = [];

    rsort( $date_array );

    for ( $i = 0; $i < count( $years ); $i++ ) {

      for ( $j = 0; $j < count( $dates ); $j++ ) {
        
        if ( is_date_in_year( $dates[$j], $years[$i] ) ) {

          $date_array[$years[$i]][] =  date( 'm', strtotime( $dates[$j] ) );

        }

      }

      $date_array[$years[$i]] = array_unique( $date_array[$years[$i]] );
    
    }

    return $date_array;

  }

}


/**
 * Get unique years
 * @return array
 */

if(! function_exists( 'get_unique_years' ) ) {

  function get_unique_years() {

    $dates = rebuild_get_dates();

    $years = array_unique( array_map( 'convert_date_to_year', $dates ) );

    rsort( $years );

    return $years;

  }

}


/**
 * Convert date to year
 * @return integer
 */

if(! function_exists( 'convert_date_to_year' ) ) {

  function convert_date_to_year( $date ) {

    return date( 'Y', strtotime( $date ) );

  }

}


/**
 * Convert date to month
 * @return integer
 */

if(! function_exists( 'convert_date_to_month' ) ) {

  function convert_date_to_month( $date ) {

    return date( 'm', strtotime( $date ) );

  }

}


/**
 * Convert date to month
 * @return string
 */

if(! function_exists( 'convert_date_to_month_string' ) ) {

  function convert_date_to_month_string( $date ) {

    return date( 'M', strtotime( $date ) );

  }

}


/**
 * Date in year
 * @return boolean
 */

if(! function_exists( 'is_date_in_year' ) ) {

  function is_date_in_year( $date, $year ) {

    return $date >= date( 'Ymd', strtotime( $year . '01' . '01' ) ) && $date <= date( 'Ymd', strtotime( $year . '12' . '31' ) );

  }

}


/**
 * Posts Exist for Month
 * @return boolean
 */

if(! function_exists( 'events_for_months' ) ) {

  function events_for_months( $year, $month ) {

    $meta_query = array(
        'relation' => 'AND',
        array(
            'key'     => 'start_date',
            'compare' => '>=',
            'value'   => "{$year}{$month}01",
            'type'    => 'NUMERIC',
        ),
        array(
            'key'     => 'end_date',
            'compare' => '<=',
            'value'   => "{$year}{$month}31", // Doesn't matter if there aren't 31 days in this month, will still work,
            'type'    => 'NUMERIC',
        )
    );

    $args = array(
      'post_type' => 'rebuild_event',
      'meta_query' => $meta_query
    );

    $posts = get_posts( $args );

    // echo '<pre>';
    // var_dump( $args );
    // echo '</pre>';


    // echo '<pre>';
    // var_dump( $posts );
    // echo '</pre>';

    return ( count( $posts ) > 0 );

  }

}


?>