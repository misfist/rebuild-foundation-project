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

      $vars[] =  array(
        'taxonomy' => 'exhibition_category',
        'field' => 'slug',
        'terms' => 'current',
      );

      $query->set( 'tax_query', $vars );

    }

    return $query;
 
  }

  add_action( 'pre_get_posts', 'rebuild_exhibition_pre_query_filter', 10, 1 );

}

?>