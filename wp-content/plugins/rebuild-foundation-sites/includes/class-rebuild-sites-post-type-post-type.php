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

class Rebuild_Sites_Post_Type_Post_Type extends Gamajo_Post_Type {
	/**
	 * Post type ID.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	protected $post_type = 'rebuild_sites';

	/**
	 * Return post type default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return array Post type default arguments.
	 */
	protected function default_args() {
		$labels = array(
			'name'               => __( 'Site', 'rebuild-foundation-sites' ),
			'singular_name'      => __( 'Site Item', 'rebuild-foundation-sites' ),
			'menu_name'          => _x( 'Sites', 'admin menu', 'rebuild-foundation-sites' ),
			'name_admin_bar'     => _x( 'Site Item', 'add new on admin bar', 'rebuild-foundation-sites' ),
			'add_new'            => __( 'Add New Item', 'rebuild-foundation-sites' ),
			'add_new_item'       => __( 'Add New Site Item', 'rebuild-foundation-sites' ),
			'new_item'           => __( 'Add New Site Item', 'rebuild-foundation-sites' ),
			'edit_item'          => __( 'Edit Site Item', 'rebuild-foundation-sites' ),
			'view_item'          => __( 'View Item', 'rebuild-foundation-sites' ),
			'all_items'          => __( 'All Site Items', 'rebuild-foundation-sites' ),
			'search_items'       => __( 'Search Site', 'rebuild-foundation-sites' ),
			'parent_item_colon'  => __( 'Parent Site Item:', 'rebuild-foundation-sites' ),
			'not_found'          => __( 'No site items found', 'rebuild-foundation-sites' ),
			'not_found_in_trash' => __( 'No site items found in trash', 'rebuild-foundation-sites' ),
		);

		$supports = array(
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'comments',
			'author',
			'custom-fields',
			'revisions',
		);

		$args = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'capability_type' => 'post',
			'rewrite'         => array( 'slug' => 'rebuild_sites', ), // Permalinks format
			'menu_position'   => 5,
			'menu_icon'       => ( version_compare( $GLOBALS['wp_version'], '4.3.1', '>=' ) ) ? 'dashicons-building' : false ,
			'has_archive'     => true,
		);

		return apply_filters( 'rebuild_sites_post_type_args', $args );
	}

	/**
	 * Return post type updated messages.
	 *
	 * @since 1.0.0
	 *
	 * @return array Post type updated messages.
	 */
	public function messages() {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Site item updated.', 'rebuild-foundation-sites' ),
			2  => __( 'Custom field updated.', 'rebuild-foundation-sites' ),
			3  => __( 'Custom field deleted.', 'rebuild-foundation-sites' ),
			4  => __( 'Site item updated.', 'rebuild-foundation-sites' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Site item restored to revision from %s', 'rebuild-foundation-sites' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Site item published.', 'rebuild-foundation-sites' ),
			7  => __( 'Site item saved.', 'rebuild-foundation-sites' ),
			8  => __( 'Site item submitted.', 'rebuild-foundation-sites' ),
			9  => sprintf(
				__( 'Site item scheduled for: <strong>%1$s</strong>.', 'rebuild-foundation-sites' ),
				/* translators: Publish box date format, see http://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'rebuild-foundation-sites' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Site item draft updated.', 'rebuild-foundation-sites' ),
		);

		if ( $post_type_object->publicly_queryable ) {
			$permalink         = get_permalink( $post->ID );
			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );

			$view_link    = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View site item', 'rebuild-foundation-sites' ) );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview site item', 'rebuild-foundation-sites' ) );

			$messages[1]  .= $view_link;
			$messages[6]  .= $view_link;
			$messages[9]  .= $view_link;
			$messages[8]  .= $preview_link;
			$messages[10] .= $preview_link;
		}

		return $messages;
	}
}
