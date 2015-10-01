<?php
/**
 * Rebuild Sites Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Sites_Post_Type
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
			'name'                       => __( 'Site Tags', 'rebuild-foundation-sites' ),
			'singular_name'              => __( 'Site Tag', 'rebuild-foundation-sites' ),
			'menu_name'                  => __( 'Site Tags', 'rebuild-foundation-sites' ),
			'edit_item'                  => __( 'Edit Site Tag', 'rebuild-foundation-sites' ),
			'update_item'                => __( 'Update Site Tag', 'rebuild-foundation-sites' ),
			'add_new_item'               => __( 'Add New Site Tag', 'rebuild-foundation-sites' ),
			'new_item_name'              => __( 'New Site Tag Name', 'rebuild-foundation-sites' ),
			'parent_item'                => __( 'Parent Site Tag', 'rebuild-foundation-sites' ),
			'parent_item_colon'          => __( 'Parent Site Tag:', 'rebuild-foundation-sites' ),
			'all_items'                  => __( 'All Site Tags', 'rebuild-foundation-sites' ),
			'search_items'               => __( 'Search Site Tags', 'rebuild-foundation-sites' ),
			'popular_items'              => __( 'Popular Site Tags', 'rebuild-foundation-sites' ),
			'separate_items_with_commas' => __( 'Separate site tags with commas', 'rebuild-foundation-sites' ),
			'add_or_remove_items'        => __( 'Add or remove site tags', 'rebuild-foundation-sites' ),
			'choose_from_most_used'      => __( 'Choose from the most used site tags', 'rebuild-foundation-sites' ),
			'not_found'                  => __( 'No site tags found.', 'rebuild-foundation-sites' ),
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

		return apply_filters( 'rebuild_sites_post_type_tag_args', $args );
	}
}