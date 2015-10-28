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

if( !function_exists( 'rebuild_foundation_enqueue_scripts' ) ) {

  function rebuild_foundation_enqueue_scripts() {

    if( is_admin() ) {
      return;
    }

    if( 'event' == get_post_type() || 'exhibition' == get_post_type() ) {

      wp_enqueue_script( 'rebuild-foundation-filters', get_stylesheet_directory_uri() . '/assets/js/filters.js', array( 'jquery' ), '', true );

    }

  }

  add_action( 'wp_enqueue_scripts', 'rebuild_foundation_enqueue_scripts' );
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
    $classes[] = 'type-' . $post_type;
    $classes[] = $post_slug_class . ' post-' . $post_slug_class;

  	// Adds a class of group-blog to blogs with more than 1 published author.
  	if ( is_multi_author() ) {
  		$classes[] = 'group-blog';
  	}

  	return $classes;
  }
  add_filter( 'body_class', 'rebuild_foundation_body_classes' );
  
}


/**
 * Media - set default image link location to 'None' 
 */

update_option('image_default_link_type','none');


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
  return ' <a class="read-more" href="' . get_permalink( get_the_ID() ) . '">' . __( 'Learn More', 'rebuild-foundation' ) . '</a>';
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


