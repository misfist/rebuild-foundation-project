<?php

/**
 * Residency Functions
 *
 *
 * @package RebuildFoundation
 */

/**
 * Pre-get query filter
 * Sets events query based on selected filters
 * @return array
 */

if(! function_exists( 'rebuild_residency_pre_query_filter' ) ) {

    function rebuild_residency_pre_query_filter( $query ) {

        if( is_admin() || ! $query->is_main_query() ) {

            return;

        }

        if( is_post_type_archive( 'residency' ) ) {

            $residency_scope = get_query_var( 'residency_category' );

            if( $residency_scope ) {

                return $query;

            }

            $site_cat = get_query_var( 'site_category' );

            $scope = ( $site_cat ) ? rebuild_residency_scope( $site_cat ) : rebuild_residency_scope();

            $vars[] =  array(
                'taxonomy' => 'residency_category',
                'field' => 'slug',
                'terms' => $scope,
            );

            $query->set( 'tax_query', $vars );

        }

        return $query;
 
    }

    add_action( 'pre_get_posts', 'rebuild_residency_pre_query_filter', 10, 1 );

}


/**
 * residency Query
 * Gets residency query
 * @return array
 */

if(! function_exists( 'rebuild_get_residency_query' ) ) {

  function rebuild_get_residency_query( $site_cat = null, $scope = null, $limit = null ) {

    // If $site_cat arg passed
    if( isset( $site_cat ) ) {

      $site_tax = 'site_category';

      $term_exists = term_exists( $site_cat, $site_tax );

      if ( $term_exists !== 0 && $term_exists !== null ) {

        $site_name = ( strlen( $site_cat ) < 20 ) ? $site_cat : substr( $site_cat, 0, 19 );

        $site_tax_query = array(
          array(
                'taxonomy' => $site_tax,
                'field'    => 'slug',
                'terms'    => $site_cat
              ),
        );
        
      }

    }

    $post_type = 'residency';
    $taxonomy = 'residency_category';
    $scope = ( isset( $scope ) ) ? $scope : rebuild_residency_scope( $site_cat );

    $trans_name = ( isset( $site_cat ) ) ? 'res_q_' . $site_name . '_' . $scope   : 'res_q_' . $scope ;
    $cache_time = 240;

    $today = date( 'Ymd' );

    if( false === ( $rebuild_residency_query = get_transient( $trans_name ) ) ) {

      $residency_tax = array(
          array(
              'taxonomy' => $taxonomy,
              'field'    => 'slug',
              'terms'    => $scope
          ),
      );

      // If $site_cat arg passed
      if( isset( $site_cat ) ) {

        $residency_tax[] = $site_tax_query;

      }

      $residency_query = array( 
          'post_type'   => $post_type,
          'meta_key' => 'start_date',
          'tax_query' => $residency_tax,
          'orderby' => 'meta_value_num',
      );
     
      // If limit arg passed
      if( isset( $limit ) && is_int( $limit ) ) {

        $residency_query['posts_per_page'] = $limit;

      }

     $rebuild_residency_query = new WP_Query( $residency_query );

     set_transient( $trans_name, $rebuild_residency_query, 60 * $cache_time );

    }

    return $rebuild_residency_query;

  }

}

/**
 * residency Scope
 * Gets first residency_category that has content
 * @input optional $site_category string
 * @return string
 */

if(! function_exists( 'rebuild_residency_scope' ) ) {

  function rebuild_residency_scope( $site_cat = null ) {

    $site_var = get_query_var( 'site_category' );

    switch ( true ) {

      case ( isset( $site_cat ) && !empty( $site_cat ) ) :
        $site_cat = $site_cat;
        break;

      case ( isset( $site_var ) && !empty( $site_var ) ) :
        $site_cat = $site_var;
        break;

      default :
        $site_cat = null;

    }

    // If $site_cat arg passed
    if( isset( $site_cat ) ) {

      $site_tax = 'site_category';

      $term_exists = term_exists( $site_cat, $site_tax );

      if ( $term_exists !== 0 && $term_exists !== null ) {

        $site_name = ( strlen( $site_cat ) < 20 ) ? $site_cat : substr( $site_cat, 0, 19 );

        $site_tax_query = array(
          array(
                'taxonomy' => $site_tax,
                'field'    => 'slug',
                'terms'    => $site_cat
              ),
        );
        
      }

    }

    $post_type = 'residency';
    $taxonomy = 'residency_category';

    // Terms are hardcoded because they need to be checked in this order
    $tax_terms = array(
      'current',
      'future',
      'past'
    );

    $prefix = 'trans_name_';

    ${$prefix . $tax_terms[0]} = ( isset( $site_cat ) ) ? 'res_scope_' .$site_name . '_' . $tax_terms[0] : 'res_scope-' . $tax_terms[0];
    ${$prefix . $tax_terms[1]} = ( isset( $site_cat ) ) ? 'res_scope_' . $site_name . '_' . $tax_terms[1] : 'res_scope_' . $tax_terms[1];
    ${$prefix . $tax_terms[2]} = ( isset( $site_cat ) ) ? 'res_scope_' . $site_name . '_' . $tax_terms[2]: 'res_scope_' . $tax_terms[2];

     // Time in minutes between updates
    $cache_time = 240;
    $today = date( 'Ymd' );

    for( $i = 0; $i < count( $tax_terms ); $i++ ) {

      $query = &${$tax_terms[$i] . '_query'};

      if( false === ( $query = get_transient( ${$prefix . $tax_terms[$i]} ) ) ) {

        $residency_tax = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $tax_terms[$i]
            ),
        );

        if( isset( $site_cat ) ) {
            $residency_tax[] = $site_tax_query;
        }

        $residency_query = array( 
            'post_type'   => $post_type,
            'meta_key' => 'start_date',
            'tax_query' => $residency_tax,
            'orderby' => 'meta_value_num',
        );


        $query = new WP_Query( $residency_query );

        set_transient( ${$prefix . $tax_terms[$i]}, $query, 60 * $cache_time );

        }

        if( $query->have_posts() ) {

            return $tax_terms[$i];

        }

    }

    return;

  }

}



?>