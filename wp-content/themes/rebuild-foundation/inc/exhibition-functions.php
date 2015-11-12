<?php

/**
 * Exhibition Functions
 *
 *
 * @package RebuildFoundation
 */

/**
 * Pre-get query filter
 * Sets events query based on selected filters
 * @return array
 */

if(! function_exists( 'rebuild_exhibition_pre_query_filter' ) ) {

    function rebuild_exhibition_pre_query_filter( $query ) {

        if( is_admin() || ! $query->is_main_query() ) {

            return;

        }

        if( is_post_type_archive( 'exhibition' ) ) {

            $exhibition_scope = get_query_var( 'exhibition_category' );

            if( $exhibition_scope ) {

                return $query;

            }

            $site_cat = get_query_var( 'site_category' );

            $scope = ( $site_cat ) ? rebuild_exhibition_scope( $site_cat ) : rebuild_exhibition_scope();

            $vars[] =  array(
                'taxonomy' => 'exhibition_category',
                'field' => 'slug',
                'terms' => $scope,
            );

            $query->set( 'tax_query', $vars );

        }

        return $query;
 
    }

    add_action( 'pre_get_posts', 'rebuild_exhibition_pre_query_filter', 10, 1 );

}


/**
 * Exhibition Query
 * Gets exhibition query
 * @return array
 */

if(! function_exists( 'rebuild_get_exhibition_query' ) ) {

  function rebuild_get_exhibition_query( $site_cat = null, $scope = null ) {

    // If $site_cat arg passed
    if( $site_cat ) {

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

    $post_type = 'exhibition';
    $taxonomy = 'exhibition_category';
    $scope = ( $scope ) ? $scope : rebuild_exhibition_scope( $site_cat );

    $trans_name = ( $site_cat ) ? 'exh_q_' . $site_name . '_' . $scope   : 'exh_q_' . $scope ;
    $cache_time = 240;

    $today = date( 'Ymd' );

    if( false === ( $rebuild_exhibition_query = get_transient( $trans_name ) ) ) {

      $exhibition_tax = array(
          array(
              'taxonomy' => $taxonomy,
              'field'    => 'slug',
              'terms'    => $scope
          ),
      );

      // If $site_cat arg passed
      if( $site_cat ) {

        $exhibition_tax[] = $site_tax_query;

      }

      $exhibitions_query = array( 
          'post_type'   => $post_type,
          'meta_key' => 'start_date',
          'tax_query' => $exhibition_tax,
          'orderby' => 'meta_value_num',
      );
     
     $rebuild_exhibition_query = new WP_Query( $exhibitions_query );

     set_transient( $trans_name, $rebuild_exhibition_query, 60 * $cache_time );

    }

    return $rebuild_exhibition_query;

  }

}

/**
 * Exhibition Scope
 * Gets first exhibition_category that has content
 * @input optional $site_category string
 * @return string
 */

if(! function_exists( 'rebuild_exhibition_scope' ) ) {

  function rebuild_exhibition_scope( $site_cat = null ) {

    $site_var = get_query_var( 'site_category' );
    $site_cat = isset( $site_cat ) ? $site_cat : $site_var;

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

    $post_type = 'exhibition';
    $taxonomy = 'exhibition_category';

    // Terms are hardcoded because they need to be checked in this order
    $tax_terms = array(
      'current',
      'future',
      'past'
    );

    $prefix = 'trans_name_';

    ${$prefix . $tax_terms[0]} = ( $site_cat ) ? 'exh_scope_' .$site_name . '_' . $tax_terms[0] : 'exh_scope-' . $tax_terms[0];
    ${$prefix . $tax_terms[1]} = ( $site_cat ) ? 'exh_scope_' . $site_name . '_' . $tax_terms[1] : 'exh_scope_' . $tax_terms[1];
    ${$prefix . $tax_terms[2]} = ( $site_cat ) ? 'exh_scope_' . $site_name . '_' . $tax_terms[2]: 'exh_scope_' . $tax_terms[2];

     // Time in minutes between updates
    $cache_time = 240;
    $today = date( 'Ymd' );

    for( $i = 0; $i < count( $tax_terms ); $i++ ) {

      if( false === ( $rebuild_exhibition_query = get_transient( ${$prefix . $tax_terms[$i]} ) ) ) {

        $exhibition_tax = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $tax_terms[$i]
            ),
        );

        if( $site_cat ) {
            $exhibition_tax[] = $site_tax_query;
        }

        $exhibition_query = array( 
            'post_type'   => $post_type,
            'meta_key' => 'start_date',
            'tax_query' => $exhibition_tax,
            'orderby' => 'meta_value_num',
        );

        $rebuild_exhibition_query = new WP_Query( $exhibition_query );

        set_transient( ${$prefix . $tax_terms[$i]}, $rebuild_exhibition_query, 60 * $cache_time );

        }

        $query = get_transient( ${$prefix . $tax_terms[$i]} );

        if( $query->have_posts() ) {

            return $tax_terms[$i];

        }

    }

    return;

  }

}



?>