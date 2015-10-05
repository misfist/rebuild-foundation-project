<?php
/**
 * Rebuild Exhibitions Taxonomy Category
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

class Rebuild_Exhibitions_Post_Type_Taxonomy_Category extends Gamajo_Taxonomy {
	/**
	 * Taxonomy ID.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $taxonomy = 'rebuild_exhibitions_category';

	/**
	 * Return taxonomy default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return array Taxonomy default arguments.
	 */
	protected function default_args() {
		$labels = array(
			'name'                       => __( 'Exhibition Categories', 'rebuild-foundation-cpt' ),
			'singular_name'              => __( 'Exhibition Category', 'rebuild-foundation-cpt' ),
			'menu_name'                  => __( 'Exhibitions Categories', 'rebuild-foundation-cpt' ),
			'edit_item'                  => __( 'Edit Exhibition Category', 'rebuild-foundation-cpt' ),
			'update_item'                => __( 'Update Exhibition Category', 'rebuild-foundation-cpt' ),
			'add_new_item'               => __( 'Add New Exhibition Category', 'rebuild-foundation-cpt' ),
			'new_item_name'              => __( 'New Exhibition Category Name', 'rebuild-foundation-cpt' ),
			'parent_item'                => __( 'Parent Exhibition Category', 'rebuild-foundation-cpt' ),
			'parent_item_colon'          => __( 'Parent Exhibition Category:', 'rebuild-foundation-cpt' ),
			'all_items'                  => __( 'All Exhibition Categories', 'rebuild-foundation-cpt' ),
			'search_items'               => __( 'Search Exhibition Categories', 'rebuild-foundation-cpt' ),
			'popular_items'              => __( 'Popular Exhibition Categories', 'rebuild-foundation-cpt' ),
			'separate_items_with_commas' => __( 'Separate exhibition categories with commas', 'rebuild-foundation-cpt' ),
			'add_or_remove_items'        => __( 'Add or remove exhibition categories', 'rebuild-foundation-cpt' ),
			'choose_from_most_used'      => __( 'Choose from the most used exhibition categories', 'rebuild-foundation-cpt' ),
			'not_found'                  => __( 'No site categories found.', 'rebuild-foundation-cpt' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'rebuild_exhibitions_category' ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		// Uncomment to add custom category taxonomy
		//return apply_filters( 'rebuild_exhibitions_post_type_category_args', $args );
	}
}