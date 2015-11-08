<?php

/**
 * Rebuild Foundation Helpers
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */


/**
 * Get Location Name
 * Retrieves name of the location
 * @return array
 */

if(! function_exists( 'rebuild_get_location_name' ) ) {

    function rebuild_get_location_name( $location_id = null ) {

        if( function_exists( 'get_field' ) ) {

            $location_id = ( $location_id ) ? $location_id : get_field( 'location' );

            $location_name = get_field( 'location_name', $location_id );

            if( $location_name ) {
              
                return $location_name;
                 
            } 

            return;

        }
    }
}


/**
 * Get Location Address
 * Retrieves the address associated with the content
 * @return array
 */

if(! function_exists( 'rebuild_get_location_fields' ) ) {

    function rebuild_get_location_fields( ) {

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


/**
 * Convert Location to String
 * Retrieves the address associated with the content and converts to string
 * @return string
 */


if(! function_exists( 'rebuild_convert_location_to_string' ) ) {

  function rebuild_convert_location_to_string( $id ) {

    $address_array = rebuild_get_location_fields( $id );

    return ( is_array( $address_array ) ) ? implode( ', ', $address_array ) : '';

  }

}


/**
 * Convert Location to URL Encoded String
 * Retrieves the address associated with the content and converts to urlencoded string
 * @return urlencoded string
 */


if(! function_exists( 'rebuild_urlencode_location' ) ) {

  function rebuild_urlencode_location( $id ) {

    $address_array = rebuild_get_location_fields( $id );

    if( isset( $address_array ) ) {

      $location_name = rebuild_get_location_name();

      $address_string = implode( ',', $address_array );

      return urlencode( $location_name . ',' . $address_string );
    }
    
    return;

  }

}



/**
 * Get site name
 * Retrieves the short name `short_name` or `post_title` of the site
 * @return string
 */

if(! function_exists( 'get_rebuild_site_name' ) ) {

    function get_rebuild_site_name() {

        if( 'site' == get_post_type() ) {

            $site_short_name = get_post_meta( get_the_ID(), 'short_name', true );
            return ( $site_short_name ) ? $site_short_name : get_the_title( get_the_ID() );

        } else {

            $site_cats = get_the_terms( get_the_ID(), 'site_category' );

            // If there is a site category associated with content
            if( !empty( $site_cats ) ) {

                // Get site category slug - matches site post slug
                $site_slug = $site_cats[0]->slug;

                // Get site associated with that category
                // https://codex.wordpress.org/Function_Reference/get_page_by_path
                $site = get_page_by_path( $site_slug, OBJECT, 'site' );

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
 * Helpers
 */

/**
 * Get all the event start_dates
 * @return array of dates
 */

if(! function_exists( 'rebuild_get_dates' ) ) {

  function rebuild_get_dates() {

    $post_type = 'event';
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1
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
            'key'     => 'start_date',
            'compare' => '<=',
            'value'   => "{$year}{$month}31",
            'type'    => 'NUMERIC',
        )
    );

    $args = array(
      'post_type' => 'event',
      'meta_query' => $meta_query,
      'posts_per_page' => -1
    );

    $posts = get_posts( $args );

    return ( count( $posts ) > 0 );

  }

}


?>