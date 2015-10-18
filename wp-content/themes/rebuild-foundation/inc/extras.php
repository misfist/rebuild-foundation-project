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
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */

if( !function_exists( 'rebuild_foundation_body_classes' ) ) {
  function rebuild_foundation_body_classes( $classes ) {

    // Adds 'class-name' to the $classes array
    // global $post;
    
    // $post_slug_class = $post->post_name;
    // $post_type = $post->post_type;
    // $classes[] = 'type-' . $post_type;
    // $classes[] = $post_slug_class . ' post-' . $post_slug_class;

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

function unhide_kitchensink( $args ) {
    $args['wordpress_adv_hidden'] = false;
    return $args;
}

add_filter( 'tiny_mce_before_init', 'unhide_kitchensink' );


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
 * Site images - attach images for gallery to rebuild_sites content type
 */

// check for plugin using plugin name
if ( is_plugin_active( 'attachments/index.php' ) ) {
  //plugin is activated

  function rebuild_foundation_site_attachments( $attachments ) {
    $fields         = array(
      array(
        'name'      => 'title',                         // unique field name
        'type'      => 'text',                          // registered field type
        'label'     => __( 'Title', 'rebuild-foundation' ),    // label to display
        'default'   => 'title',                         // default value upon selection
      ),
      array(
        'name'      => 'caption',                       // unique field name
        'type'      => 'textarea',                      // registered field type
        'label'     => __( 'Caption', 'rebuild-foundation' ),  // label to display
        'default'   => 'caption',                       // default value upon selection
      ),
    );

    $args = array(

      // title of the meta box (string)
      'label'         => 'Gallery Images',

      // all post types to utilize (string|array)
      'post_type'     => array( 'rebuild_sites' ),

      // meta box position (string) (normal, side or advanced)
      'position'      => 'normal',

      // meta box priority (string) (high, default, low, core)
      'priority'      => 'high',

      // allowed file type(s) (array) (image|video|text|audio|application)
      'filetype'      => 'image',

      // include a note within the meta box (string)
      'note'          => 'Attach images',

      // by default new Attachments will be appended to the list
      // but you can have then prepend if you set this to false
      'append'        => true,

      // text for 'Attach' button in meta box (string)
      'button_text'   => __( 'Attach image', 'rebuild-foundation' ),

      // text for modal 'Attach' button (string)
      'modal_text'    => __( 'Attach', 'rebuild-foundation' ),

      // which tab should be the default in the modal (string) (browse|upload)
      'router'        => 'browse',

      // whether Attachments should set 'Uploaded to' (if not already set)
      'post_parent'   => false,

      // fields array
      'fields'        => $fields,

    );

    $attachments->register( 'rebuild_foundation_site_attachments', $args ); // unique instance name

    add_filter( 'attachments_settings_screen', '__return_false' ); // disable the Settings screen
  }

  add_action( 'attachments_register', 'rebuild_foundation_site_attachments' );

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

