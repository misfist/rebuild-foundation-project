<?php
/**
 * Rebuild Exhibitions Taxonomy Tag
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

class Rebuild_Exhibitions_Post_Type_Taxonomy_Tag extends Gamajo_Taxonomy {
	/**
	 * Taxonomy ID.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $taxonomy = 'rebuild_exhibitions_tag';

	/**
	 * Return taxonomy default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return array Taxonomy default arguments.
	 */
	protected function default_args() {
		$labels = array(
			'name'                       => __( 'Exhibition Tags', 'rebuild-foundation-cpt' ),
			'singular_name'              => __( 'Exhibition Tag', 'rebuild-foundation-cpt' ),
			'menu_name'                  => __( 'Exhibitions Tags', 'rebuild-foundation-cpt' ),
			'edit_item'                  => __( 'Edit Exhibition Tag', 'rebuild-foundation-cpt' ),
			'update_item'                => __( 'Update Exhibition Tag', 'rebuild-foundation-cpt' ),
			'add_new_item'               => __( 'Add New Exhibition Tag', 'rebuild-foundation-cpt' ),
			'new_item_name'              => __( 'New Exhibition Tag Name', 'rebuild-foundation-cpt' ),
			'parent_item'                => __( 'Parent Exhibition Tag', 'rebuild-foundation-cpt' ),
			'parent_item_colon'          => __( 'Parent Exhibition Tag:', 'rebuild-foundation-cpt' ),
			'all_items'                  => __( 'All Exhibition Tags', 'rebuild-foundation-cpt' ),
			'search_items'               => __( 'Search Exhibition Tags', 'rebuild-foundation-cpt' ),
			'popular_items'              => __( 'Popular Exhibition Tags', 'rebuild-foundation-cpt' ),
			'separate_items_with_commas' => __( 'Separate exhibition tags with commas', 'rebuild-foundation-cpt' ),
			'add_or_remove_items'        => __( 'Add or remove exhibition tags', 'rebuild-foundation-cpt' ),
			'choose_from_most_used'      => __( 'Choose from the most used exhibition tags', 'rebuild-foundation-cpt' ),
			'not_found'                  => __( 'No exhibition tags found.', 'rebuild-foundation-cpt' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => 'rebuild_exhibitions_tag' ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		// Uncomment to add custom tag taxonomy
		//return apply_filters( 'rebuild_exhibitions_post_type_tag_args', $args );
	}
}