<?php

/**
 * Event Functions
 *
 *
 * @package RebuildFoundation
 */

if(! function_exists( 'rebuild_events_pre_query_filter' ) ) {

  function rebuild_events_pre_query_filter( $query ) {

    if( is_admin() || ! $query->is_main_query() ) {

      return;

    }

    if( 'rebuild_event' == get_query_var( 'post_type' ) ) {

      $query_year = get_query_var( 'event_year' );
      $query_month = get_query_var( 'event_month' );

      $year_var = ( is_numeric( $query_year ) && $query_year > 0 ) ? $query_year : '';
      $month_var = ( is_numeric( $query_month ) && $query_month > 0 ) ? sprintf( "%02d", $query_month ) : '';

      $query->set( 'orderby', 'meta_value_num' );
      $query->set( 'meta_key', 'start_date' );
      $query->set( 'order', 'ASC' );

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

              $query->set( 'meta_query', $vars );

              break;
          // Year queried
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

              $query->set( 'meta_query', $vars );

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

              $query->set( 'meta_query', $vars );


              break;
          // No date queried
          default:
            return $query;
      }

    }
 
  }

  add_action( 'pre_get_posts', 'rebuild_events_pre_query_filter', 10, 1 );

}

?>