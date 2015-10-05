<?php
/**
 * Rebuild Sites Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

class Rebuild_Sites_Post_Type_Registrations {

	public $post_type;

	public $taxonomies;

	public function init() {
		// Add the portfolio post type and taxonomies
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Initiate registrations of post type and taxonomies.
	 */
	public function register() {
		global $rebuild_sites_post_type_post_type, $rebuild_sites_post_type_taxonomy_category, $rebuild_sites_post_type_taxonomy_tag;

		$rebuild_sites_post_type_post_type = new Rebuild_Sites_Post_Type_Post_Type;
		$rebuild_sites_post_type_post_type->register();
		$this->post_type = $rebuild_sites_post_type_post_type->get_post_type();

		$rebuild_sites_post_type_taxonomy_category = new Rebuild_Sites_Post_Type_Taxonomy_Category;
		$rebuild_sites_post_type_taxonomy_category->register();
		$this->taxonomies[] = $rebuild_sites_post_type_taxonomy_category->get_taxonomy();
		register_taxonomy_for_object_type(
			$rebuild_sites_post_type_taxonomy_category->get_taxonomy(),
			$rebuild_sites_post_type_post_type->get_post_type()
		);

		// Add to Posts & Pages
		register_taxonomy_for_object_type(
			$rebuild_sites_post_type_taxonomy_category->get_taxonomy(),
			'post'
		);
		register_taxonomy_for_object_type(
			$rebuild_sites_post_type_taxonomy_category->get_taxonomy(),
			'page'
		);

		// Add to Events and Event Locations
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'events-manager/events-manager.php' ) ) {
			register_taxonomy_for_object_type( $rebuild_sites_post_type_taxonomy_category->get_taxonomy(), EM_POST_TYPE_EVENT );
		}

		$rebuild_sites_post_type_taxonomy_tag = new Rebuild_Sites_Post_Type_Taxonomy_Tag;
		$rebuild_sites_post_type_taxonomy_tag->register();
		$this->taxonomies[] = $rebuild_sites_post_type_taxonomy_tag->get_taxonomy();
		register_taxonomy_for_object_type(
			$rebuild_sites_post_type_taxonomy_tag->get_taxonomy(),
			$rebuild_sites_post_type_post_type->get_post_type()
		);

	}

	/**
	 * Unregister post type and taxonomies registrations.
	 */
	public function unregister() {
		global $rebuild_sites_post_type_post_type, $rebuild_sites_post_type_taxonomy_category, $rebuild_sites_post_type_taxonomy_tag;
		$rebuild_sites_post_type_post_type->unregister();
		$this->post_type = null;

		$rebuild_sites_post_type_taxonomy_category->unregister();
		unset( $this->taxonomies[ $rebuild_sites_post_type_taxonomy_category->get_taxonomy() ] );

		$rebuild_sites_post_type_taxonomy_tag->unregister();
		unset( $this->taxonomies[ $rebuild_sites_post_type_taxonomy_tag->get_taxonomy() ] );
	}
}
