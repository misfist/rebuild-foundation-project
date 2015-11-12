<?php

/**
 * Blog Functions
 *
 *
 * @package RebuildFoundation
 */

/**
 * Posts Query
 * @input optional
 * @return array
 */

if(! function_exists( 'rebuild_posts_query' ) ) {

  function rebuild_posts_query( $site_cat = null, $limit = null ) {

      $blog_post_type = 'post';

      if( isset( $site_cat ) ) {

        $site_tax = 'site_category';

        $site_name = ( strlen( $site_cat ) < 20 ) ? $site_cat : substr( $site_cat, 0, 19 );

        $tax_query = array(
          array(
            'taxonomy' => $site_tax,
            'field' => 'slug',
            'terms' => $site_cat
          )
        ); 

      }

      $trans_name = ( $site_cat ) ? 'blog_q_' . $site_name : 'blog_q_';
      $cache_time = 60; // Time in minutes between updates.

      if( false === ( $site_blog_query = get_transient( $trans_name ) ) ) {

          $blog_args = array (
              'post_type' => $blog_post_type,
              'tax_query' => $site_tax,
          );

          if( isset( $limit ) ) {

            $blog_args['posts_per_page'] = $limit;

          }

          if( isset( $site_cat ) ) {

            $blog_args['tax_query'] = $tax_query;

          }
             
         $site_blog_query = new WP_Query( $blog_args );

         set_transient( $trans_name, $site_blog_query, 60 * $cache_time );

      }

      if( isset( $site_blog_query ) && $site_blog_query->have_posts() ) {

        return $site_blog_query;

      }

  }

}



?>