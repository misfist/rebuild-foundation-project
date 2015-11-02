<?php
/**
 * RebuildFoundation functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package RebuildFoundation
 */

if ( ! function_exists( 'rebuild_foundation_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function rebuild_foundation_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on RebuildFoundation, use a find and replace
	 * to change 'rebuild-foundation' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'rebuild-foundation', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'rebuild-foundation' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'rebuild_foundation_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // rebuild_foundation_setup
add_action( 'after_setup_theme', 'rebuild_foundation_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function rebuild_foundation_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'rebuild_foundation_content_width', 640 );
}
add_action( 'after_setup_theme', 'rebuild_foundation_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function rebuild_foundation_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'rebuild-foundation' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'rebuild_foundation_widgets_init' );

/**
 * Allow custom placement of share icons
 */
function jptweak_remove_share() {
    remove_filter( 'the_content', 'sharing_display',19 );
    remove_filter( 'the_excerpt', 'sharing_display',19 );
    if ( class_exists( 'Jetpack_Likes' ) ) {
        remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
    }
}
 
add_action( 'loop_start', 'jptweak_remove_share' );

/**
 * Enqueue scripts and styles.
 */
function rebuild_foundation_scripts() {
	wp_enqueue_style( 'rebuild-foundation-style', get_stylesheet_uri() );

	wp_enqueue_script( 'rebuild-foundation-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'rebuild-foundation-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), '20130115', true );

	wp_deregister_script( 'jquery' );

	wp_enqueue_script( 'jquery', '//code.jquery.com/jquery-1.11.3.min.js', array(), false, true );

	wp_enqueue_script( 'slick-carousel', trailingslashit( get_template_directory_uri() ) . 'assets/vendor/slick/slick.min.js' , array( 'jquery' ), false, true );

	wp_enqueue_style( 'slick-carousel', trailingslashit( get_template_directory_uri() ) . 'assets/vendor/slick/slick.css' );

	wp_enqueue_script( 'rebuild-foundation-toggle-elements', get_template_directory_uri() . '/assets/js/toggle.js', array(), false, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'rebuild_foundation_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load helpers.
 */
require_once get_template_directory() . '/inc/helpers.php';

/**
 * Load filters.
 */
require_once get_template_directory() . '/inc/filters.php';

/**
 * Load filters.
 */
require_once get_template_directory() . '/inc/event-functions.php';

