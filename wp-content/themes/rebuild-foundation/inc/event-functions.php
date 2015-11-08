<?php

/**
 * Event Functions
 *
 *
 * @package RebuildFoundation
 */

/**
 * Event Meta Query Vars
 * Builds the date query vars based on conditions
 * @return array
 */

if(! function_exists( 'rebuild_events_meta_query_vars' ) ) {

  function rebuild_events_meta_query_vars() {

    if( 'event' == get_query_var( 'post_type' ) ) {

      $query_year = get_query_var( 'event_year' );
      $query_month = get_query_var( 'event_month' );

      $year_var = ( is_numeric( $query_year ) && $query_year > 0 ) ? $query_year : '';
      $month_var = ( is_numeric( $query_month ) && $query_month > 0 ) ? sprintf( "%02d", $query_month ) : '';

      switch( true ) {

          // Year and month queried
          case ( ( 4 == strlen( $year_var ) && is_numeric( $year_var ) ) && is_numeric( $month_var ) ) :
              $days_in_month = cal_days_in_month( CAL_GREGORIAN, $month_var, $year_var );
              $vars[] = array(
                  'key'       => 'start_date',
                  'value'     => date( 'Ymd', strtotime( $year_var . $month_var . '01' ) ),
                  'compare'   => '>=',
                  'type'      => 'NUMERIC'
              );
              $vars[] = array(
                  'key'       => 'start_date',
                  'value'     => date( 'Ymd', strtotime( $year_var . $month_var . $days_in_month ) ),
                  'compare'   => '<',
                  'type'      => 'NUMERIC'
              );

              break;

          // Year queried & this year
          case ( 4 == strlen( $year_var ) && is_numeric( $year_var ) && date( 'Y' ) == $year_var ):
              $month = date( 'm' );
              $days_in_month = cal_days_in_month( CAL_GREGORIAN, $month, $year_var );
              $vars[] = array(
                  'key'       => 'start_date',
                  'value'     => date( 'Ymd', strtotime( $year_var . $month . '01' ) ),
                  'compare'   => '>=',
                  'type'      => 'NUMERIC'
              );
              $vars[] = array(
                  'key'       => 'start_date',
                  'value'     => date( 'Ymd', strtotime( $year_var . $month . $days_in_month ) ),
                  'compare'   => '<',
                  'type'      => 'NUMERIC'
              );

              break;

          // Year queried & not this year
          case ( 4 == strlen( $year_var ) && is_numeric( $year_var ) ):
              $vars[] = array(
                  'key'       => 'start_date',
                  'value'     => date( 'Ymd', strtotime( $year_var . '0101' ) ),
                  'compare'   => '>=',
                  'type'      => 'NUMERIC'
              );
              $vars[] = array(
                  'key'       => 'start_date',
                  'value'     => date( 'Ymd', strtotime( $year_var . '1231' ) ),
                  'compare'   => '<',
                  'type'      => 'NUMERIC'
              );

              break;

          // Month queried
          case ( is_numeric( $month_var ) && $month_var > 0 ):
              $year = date( 'Y' );
              $days_in_month = cal_days_in_month( CAL_GREGORIAN, $month_var, $year );
              $vars[] = array(
                  'key'       => 'start_date',
                  'value'     => date( 'Ymd', strtotime( $year. $month_var . '01' ) ),
                  'compare'   => '>=',
                  'type'      => 'NUMERIC'
              );
              $vars[] = array(
                  'key'       => 'start_date',
                  'value'     => date( 'Ymd', strtotime( $year . $month_var . $days_in_month ) ),
                  'compare'   => '<',
                  'type'      => 'NUMERIC'
              );

              break;

          // No date queried
          default:
            $year = date( 'Y' );
            $month = date( 'm' );
            $days_in_month = cal_days_in_month( CAL_GREGORIAN, $month, $year );
            $vars[] = array(
                'key'       => 'start_date',
                'value'     => date( 'Ymd', strtotime( $year. $month . '01' ) ),
                'compare'   => '>=',
                'type'      => 'NUMERIC'
            );
            $vars[] = array(
                'key'       => 'start_date',
                'value'     => date( 'Ymd', strtotime( $year . $month . $days_in_month ) ),
                'compare'   => '<',
                'type'      => 'NUMERIC'
            );

      }

      return $vars;

    }

  }

}

/**
 * Pre-get query filter
 * Sets events query based on selected filters
 * @return array
 */

if(! function_exists( 'rebuild_events_pre_query_filter' ) ) {

  function rebuild_events_pre_query_filter( $query ) {

    if( is_admin() || ! $query->is_main_query() ) {

      return;

    }

    if( is_post_type_archive( 'event' ) ) {

      $query->set( 'orderby', 'meta_value_num' );
      $query->set( 'meta_key', 'start_date' );
      $query->set( 'order', 'ASC' );

      $vars = rebuild_events_meta_query_vars();

      $query->set( 'meta_query', $vars );

    }

    return $query;
 
  }

  add_action( 'pre_get_posts', 'rebuild_events_pre_query_filter', 10, 1 );

}



/**
 * Get all the event start_dates
 * Based on current query, creates array of dates that have posts
 * This can be used to dynamically generate date filters
 * @return array of dates
 */

if(! function_exists( 'rebuild_get_dates' ) ) {

  function rebuild_get_dates() {

    global $wp_query;

    // Get the query
    $query = ( isset( $wp_query->query ) ) ? $wp_query->query : '';
    $post_type = ( isset( $query ) && array_key_exists( 'post_type' , $query ) ) ? $query['post_type'] : 'event';

    // Get the taxonomy
    switch ( true ) {

      case ( array_key_exists( 'site_category' , $query ) ) :
        $event_taxonomy = 'site_category';
        $event_term = $query['site_category'];
        break;

      case ( array_key_exists( 'event_category' , $query ) ) :
        $event_taxonomy = 'event_category';
        $event_term = $query['event_category'];
        break;

      case ( array_key_exists( 'event_tag' , $query ) ) :
        $event_taxonomy = 'event_tag';
        $event_term = $query['event_tag'];
        break;

      default :
        $event_taxonomy = null;
        $event_term = null;

    }

    // Base args
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1
    );

    // Tax args
    if( $event_taxonomy ) {

      $args['tax_query'] = array(
          array(
              'taxonomy' => $event_taxonomy,
              'field'    => 'slug',
              'terms'    => $event_term,
          ),
      );

    }

    // Get all the posts matching args
    $events = get_posts( $args );

    $dates = [];

    if( count( $events ) > 0 ) {

      // Populate array with dates that have posts
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
 * Get Event Post Count
 * Based on current query, returns number of matched events
 * @return integer
 */

if(! function_exists( 'rebuild_count_events' ) ) {

  function rebuild_count_events() {

    global $wp_query;

    // Get the query
    $query = ( isset( $wp_query->query ) ) ? $wp_query->query : '';
    $post_type = ( isset( $query ) && array_key_exists( 'post_type' , $query ) ) ? $query['post_type'] : 'event';
    $event_year = ( isset( $query ) && array_key_exists( 'event_year' , $query ) ) ? $query['event_year'] : '';
    $event_month = ( isset( $query ) && array_key_exists( 'event_month' , $query ) ) ? $query['event_month'] : '';

    // Get the taxonomy
    switch ( true ) {

      case ( array_key_exists( 'site_category' , $query ) ) :
        $event_taxonomy = 'site_category';
        $event_term = $query['site_category'];
        break;

      case ( array_key_exists( 'event_category' , $query ) ) :
        $event_taxonomy = 'event_category';
        $event_term = $query['event_category'];
        break;

      case ( array_key_exists( 'event_tag' , $query ) ) :
        $event_taxonomy = 'event_tag';
        $event_term = $query['event_tag'];
        break;

      default :
        $event_taxonomy = null;
        $event_term = null;

    }

    // Base args
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1
    );

    // Tax args
    if( $event_taxonomy ) {

      $args['tax_query'] = array(
          array(
              'taxonomy' => $event_taxonomy,
              'field'    => 'slug',
              'terms'    => $event_term,
          ),
      );

    }

    // If Year
    if( $event_year || $event_month ) {

      $args['meta_query'] = rebuild_events_meta_query_vars();

    }

    // Get all the posts matching args
    $events = get_posts( $args );

    return count( $events );

  }

}


/**
 * Get all the event years
 * Based on the current query (ala rebuild_get_dates()) get all the years that have posts
 * Use by rebuild_event_year_filter()
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
 * Used by rebuild_event_month_filter()
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

?>