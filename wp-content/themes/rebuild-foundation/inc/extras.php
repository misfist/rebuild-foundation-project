<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Starter Theme
 */

/**
 * Detect plugin.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Enqueue and localize filter script
 */

if( !function_exists( 'rebuild_foundation_enqueue_filter_script' ) ) {

  function rebuild_foundation_enqueue_filter_script() {

    if( is_admin() ) {
      return;
    }

    global $post;

    wp_enqueue_script( 'rebuild-foundation-filters', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/filters.min.js', array( 'jquery' ), '', true );

    $post_type = get_post_type();
    $page_type = rebuild_get_page_type();
    $site = rebuild_get_site_category();

    // Localize the script with new data
    $post_info = array(
      'postType' => ( $post_type ) ? $post_type : '',
      'pageType' => $page_type,
    );

    // Localize start and end dates if single event or exhibiton
    if( is_singular( array( 'event', 'exhibition' ) ) ) {

      $fields = get_fields( $post->ID );
      $post_info['startDate'] = ( $fields['start_date'] ) ? date( 'Y-m-d', strtotime( $fields['start_date'] ) ) : '';
      
    }

    if( is_singular( array( 'exhibition' ) ) ) {
      $scope = wp_get_post_terms( $post->ID, 'exhibition_category', array( 'fields' => 'slugs' ) );
      $post_info['exhibitionScope'] = ( count( $scope ) > 0 ) ? $scope[0] : '' ;
    }

    $site_var = get_query_var( 'site_category' );

    // Localize site name if single or site_category `query_var` is set
    if( is_singular() || $site_var ) {

      $post_info['site'] = $site;

    }

    // Localize scope if exhibition_category `query_var` is set
    $exhibition_scope = get_query_var( 'exhibition_category' );

    if( $exhibition_scope ) {

      $post_info['exhibitionScope'] = $exhibition_scope;

    }

    wp_localize_script( 'rebuild-foundation-filters', 'pageInfo', $post_info );

  }

  add_action( 'wp_enqueue_scripts', 'rebuild_foundation_enqueue_filter_script' );
}


/**
 * Enqueue slider scripts
 */

if( !function_exists( 'rebuild_foundation_enqueue_slider_scripts' ) ) {

  function rebuild_foundation_enqueue_slider_scripts() {

    if( is_admin() ) {
      return;
    }

    if( is_singular( array( 'site', 'event', 'exhibition', 'post', 'page' ) ) ) {

      wp_enqueue_script( 'slick-carousel', trailingslashit( get_template_directory_uri() ) . 'assets/vendor/slick/slick.min.js' , array( 'jquery' ), false, true );

      wp_enqueue_style( 'slick-carousel', trailingslashit( get_template_directory_uri() ) . 'assets/vendor/slick/slick.css' );

      wp_enqueue_style( 'slick-carousel-theme', trailingslashit( get_template_directory_uri() ) . 'assets/vendor/slick/slick-theme.css' );

      // To make changes, edit source and compile
      wp_enqueue_script( 'rebuild-foundation-slider', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/sliderInit.min.js', array( 'jquery' ), '', true );

    }

  }

  add_action( 'wp_enqueue_scripts', 'rebuild_foundation_enqueue_slider_scripts' );
}

/**
 * Adds custom classes to the array of body classes.
 *  
 * @param array $classes Classes for the body element.
 * @return array
 */

if( !function_exists( 'rebuild_foundation_body_classes' ) ) {

  function rebuild_foundation_body_classes( $classes ) {

    // Adds 'class-name' to the $classes array
    global $post;
    
    $post_slug_class = ( isset( $post->post_name ) ) ? $post->post_name : '';
    $post_type = ( isset( $post->post_type ) ) ? $post->post_type : '';

    // Don't show on archive pages
    if( !is_post_type_archive() && !is_tax() ) {
      $site = rebuild_get_site_category();
      $classes[] = ( $site ) ? 'site-' . $site : '';
      $classes[] = $post_slug_class . ' post-' . $post_slug_class;
    }

    // If the site_category query_var is set, add the site_category
    $site_category = get_query_var( 'site_category' );
    $classes[] = ( $site_category ) ? 'site-' . $site_category : '';

    $classes[] = 'type-' . $post_type;

  	return $classes;
  }

  add_filter( 'body_class', 'rebuild_foundation_body_classes' );
  
}


/**
 * Media - set default image link location to 'None' 
 */

update_option('image_default_link_type','none');


/**
 * Custom Image - add custom image size and display in media browser
 * https://developer.wordpress.org/reference/functions/add_image_size/
 */

add_image_size( 'exhibition-thumbnail', 325, 325, array( 'center', 'top' ) );

if(! function_exists( 'rebuild_foundation_image_sizes' ) ) {

  function rebuild_foundation_image_sizes( $sizes ) {
      return array_merge( $sizes, array(
          'exhibition-thumbnail' => __( 'Exhibition Thumbnail' ),
      ) );
  }

  add_filter( 'image_size_names_choose', 'rebuild_foundation_image_sizes' );

}



/**
 * Always Show Kitchen Sink in WYSIWYG Editor
 */

if(! function_exists( 'unhide_kitchensink' ) ) {

  function unhide_kitchensink( $args ) {
      $args['wordpress_adv_hidden'] = false;
      return $args;
  }

  add_filter( 'tiny_mce_before_init', 'unhide_kitchensink' );

}


/**
 * Sites menu - adds theme location for sites list menu
 * The menu must be populated in the admin section in order to appear
 * wp-admin/nav-menus.php
 */

if( !function_exists( 'rebuild_foundation_register_menus' ) ) {
  function rebuild_foundation_register_menus() {

    register_nav_menus( array(
          'sites_menu' => esc_html__( 'Sites Menu', 'rebuild-foundation' ),
    ) );

  }

  add_action( 'init', 'rebuild_foundation_register_menus' );
}

/**
 * Footer widgets - adds footer widget area
 */

if ( ! function_exists( 'rebuild_foundation_register_widget_area' ) ) {

  // Register Sidebars
  function rebuild_foundation_register_widget_area() {

    $submenu_args = array(
      'id'            => 'submenu-widget',
      'class'         => 'submenu',
      'name'          => __( 'Submenu', 'rebuild-foundation' ),
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
    );

    $footer_args = array(
      'id'            => 'footer-widget',
      'class'         => 'site-info',
      'name'          => __( 'Footer', 'rebuild-foundation' ),
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
    );
    register_sidebar( $submenu_args );
    register_sidebar( $footer_args );

  }

  add_action( 'widgets_init', 'rebuild_foundation_register_widget_area' );

}


/**
 * Flush rewrite rules on theme switch
 */

add_action( 'after_switch_theme', 'flush_rewrite_rules' );


/**
 * Remove archive title prefix - e.g. Archive, Category, etc from archive heading
 */

function rebuild_foundation_remove_archive_title_prefix( $title ) {

  if ( is_category() || is_tag() || is_tax() ) {

    $title = single_cat_title( '', false );

  } elseif ( is_post_type_archive() ) {

    $title = post_type_archive_title( '', false );

  } 

  return $title;

}

add_filter( 'get_the_archive_title', 'rebuild_foundation_remove_archive_title_prefix' );


/**
 * Change excerpt
 * Add Learn More link to excerpts
 */

function rebuild_custom_excerpt_more( $more ) {
  return '... <a class="read-more" href="' . get_permalink( get_the_ID() ) . '">' . __( 'Learn More', 'rebuild-foundation' ) . '</a>';
}

add_filter( 'excerpt_more', 'rebuild_custom_excerpt_more' );


/**
 * Change share title
 * Modify the text uses in the share title
 */

if(! function_exists( 'jetpack_developer_custom_sharing_title' ) ) {

  function jetpack_developer_custom_sharing_title( $title, $this, $id, $args ) {

    if( $id ) {
      $id = explode( '-', $id );
      $id = $id[0] . '-' . $id[1];
    }

    switch ( true ) {
      case ( 'sharing-twitter' == $id ):
        $title = 'Share on Twitter';
        break;
      case ( 'sharing-facebook' == $id ):
        $title = 'Share on Facebook';
        break;
      case ( 'sharing-linkedin' == $id ):
        $text = 'Share on LinkedIn';
        break;
      case ( 'sharing-pinterest' == $id ):
        $text = 'Share on Pinterest';
        break;
      case ( 'sharing-linkedin' == $id ):
        $text = 'Share on LinkedIn';
        break;
      default:
        $title = $title;
    }
    return $title;
  }

  add_filter( 'jetpack_sharing_display_title', 'jetpack_developer_custom_sharing_title', 20, 4 );

}

/**
 * Change share link text
 * Modify the text uses in share links
 */

if(! function_exists( 'jetpack_developer_custom_sharing_text' ) ) {

  function jetpack_developer_custom_sharing_text( $text, $this, $id, $args ) {

    if( $id ) {
      $id = explode( '-', $id );
      $id = $id[0] . '-' . $id[1];
    }

    switch ( true ) {
      case ( 'sharing-twitter' == $id ):
        $text = '<i class="twitter"></i>Twitter';
        break;
      case ( 'sharing-facebook' == $id ):
        $text = '<i class="facebook"></i>Facebook';
        break;
      case ( 'sharing-linkedin' == $id ):
        $text = '<i class="linkedin"></i>LinkedIn';
        break;
      case ( 'sharing-pinterest' == $id ):
        $text = '<i class="pinterest"></i>Pinterest';
        break;
      case ( 'sharing-linkedin' == $id ):
        $text = '<i class="linkedin"></i>LinkedIn';
        break;
      default:
        $text = $text;
    }
    return $text;
  }
  
  add_filter( 'jetpack_sharing_display_text', 'jetpack_developer_custom_sharing_text', 20, 4 );

}