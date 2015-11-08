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

    function rebuild_get_location_name( ) {

        if( function_exists( 'get_field' ) ) {

            $post_id = get_the_ID();

            $location_id = get_field( 'location', $post_id, false );

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

    if( is_array( $address_array ) && count( $address_array ) > 0 ) {

      $location_id = get_field( 'location', $id, false );

      $location_name = get_the_title( $location_id );

      $name_array = array(
        'name' => $location_name
      );

      $address_array = array_merge( $name_array , $address_array );

      $address_string = implode( ',', $address_array );

      return urlencode( $address_string );
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