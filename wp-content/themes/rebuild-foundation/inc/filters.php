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

/**
 * Year and Month Query Vars
 * Add `year` and `month` query_var for events
 * @return
 */

if(! function_exists( 'rebuild_register_query_vars' ) ) {

    function rebuild_register_query_vars() {

        global $wp;

        $wp->add_query_var( 'event_year' );
        $wp->add_query_var( 'event_month' );

    }

    add_filter( 'init', 'rebuild_register_query_vars' );

}

// Event Year Filters

/**
 * Year Filter
 * Render years for which there are events, based on current query results?
 * @return echo string
 */

if(! function_exists( 'rebuild_event_year_filter' ) ) {

    function rebuild_event_year_filter() {

        if( function_exists( 'rebuild_get_event_years' ) ) {

            $years = rebuild_get_event_years();

            // var_dump( $years );

            echo '<ul class="event-year-filter">';

            foreach( $years as $year ) {


                echo '<li data-event_year="' . $year . '">';
                echo '<a href="' . esc_url( add_query_arg( 'event_year', $year ) ) . '">';
                echo $year;
                echo '</a>';
                echo '</li>';

            }

            echo '</ul>';

        }

        return;

    }

}

// Event Month Filters

/**
 * Month Filter
 * Render years for which there are events, based on current query results?
 * @return echo string
 */


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
 * @return array of years
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
 * Get all the event months
 * TODO: Figure the best way to do this
 * @return array of months
 */

if(! function_exists( 'rebuild_get_event_months' ) ) {

  function rebuild_get_event_months() {

    if( function_exists( 'rebuild_get_dates' ) ) {

      $dates = rebuild_get_dates();
      $months = [];

      // Associative array - key => (int), value => (string)

      $unique_dates = array_unique( $dates );

      

      // return array_unique( array_map( 'convert_date_to_month', $dates ) );

    }

  }

}

/**
 * Convert date to year
 * @return integer
 */

function convert_date_to_year( $date ) {

  return date( 'Y', strtotime( $date ) );

}

/**
 * Convert date to month
 * @return integer
 */

function convert_date_to_month( $date ) {

  return date( 'm', strtotime( $date ) );

}

/**
 * Convert date to month
 * @return string
 */

function convert_date_to_month_string( $date ) {

  return date( 'M', strtotime( $date ) );

}

