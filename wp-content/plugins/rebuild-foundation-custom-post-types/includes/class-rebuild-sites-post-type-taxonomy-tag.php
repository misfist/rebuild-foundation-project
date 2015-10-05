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


class Rebuild_Sites_Post_Type_Taxonomy_Tag extends Gamajo_Taxonomy {
	/**
	 * Taxonomy ID.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $taxonomy = 'rebuild_sites_tag';

	/**
	 * Return taxonomy default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return array Taxonomy default arguments.
	 */
	protected function default_args() {
		$labels = array(
			'name'                       => __( 'Site Tags', 'rebuild-foundation-cpt' ),
			'singular_name'              => __( 'Site Tag', 'rebuild-foundation-cpt' ),
			'menu_name'                  => __( 'Site Tags', 'rebuild-foundation-cpt' ),
			'edit_item'                  => __( 'Edit Site Tag', 'rebuild-foundation-cpt' ),
			'update_item'                => __( 'Update Site Tag', 'rebuild-foundation-cpt' ),
			'add_new_item'               => __( 'Add New Site Tag', 'rebuild-foundation-cpt' ),
			'new_item_name'              => __( 'New Site Tag Name', 'rebuild-foundation-cpt' ),
			'parent_item'                => __( 'Parent Site Tag', 'rebuild-foundation-cpt' ),
			'parent_item_colon'          => __( 'Parent Site Tag:', 'rebuild-foundation-cpt' ),
			'all_items'                  => __( 'All Site Tags', 'rebuild-foundation-cpt' ),
			'search_items'               => __( 'Search Site Tags', 'rebuild-foundation-cpt' ),
			'popular_items'              => __( 'Popular Site Tags', 'rebuild-foundation-cpt' ),
			'separate_items_with_commas' => __( 'Separate site tags with commas', 'rebuild-foundation-cpt' ),
			'add_or_remove_items'        => __( 'Add or remove site tags', 'rebuild-foundation-cpt' ),
			'choose_from_most_used'      => __( 'Choose from the most used site tags', 'rebuild-foundation-cpt' ),
			'not_found'                  => __( 'No site tags found.', 'rebuild-foundation-cpt' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => 'rebuild_sites_tag' ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		// Uncomment to add custom tag taxonomy
		//return apply_filters( 'rebuild_sites_post_type_tag_args', $args );
	}
}