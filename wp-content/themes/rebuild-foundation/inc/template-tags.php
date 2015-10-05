<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package RebuildFoundation
 */

if ( ! function_exists( 'rebuild_foundation_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function rebuild_foundation_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( 'Posted on %s', 'post date', 'rebuild-foundation' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'rebuild-foundation' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	if ( 'post' === get_post_type() ) {
		echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
	}

}
endif;

if ( ! function_exists( 'rebuild_foundation_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function rebuild_foundation_entry_footer() {

    /* translators: used between list items, there is a space after the comma */
    // $site_list =  get_the_term_list( get_the_ID(), 'rebuild_sites_category', '', ', ' );
    // if ( $site_list ) {
    //     printf( '<span class="meta cat-links">' . esc_html__( 'Site %1$s', 'rebuild-foundation' ) . '</span>', $site_list ); // WPCS: XSS OK.
    // }

    /* translators: used between list items, there is a space after the comma */
	$site_term = wp_get_post_terms( get_the_ID(), 'rebuild_sites_category', array( 'fields' => 'slugs' ) );

	if( $site_term ) {

		$sites_args = array (
			'post_type' => 'rebuild_sites',
			'posts_per_page' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => 'rebuild_sites_category',
					'field'    => 'slug',
					'terms'    => $site_term[0]
				),
			),
		);

		$sites = get_posts( $sites_args );

		if ( $sites ) {

			$site_link = '<a href="' . post_permalink( $sites[0]->ID ) . '">' . $sites[0]->post_title . '</a>';
			
			printf( '<span class="meta site-links">' . esc_html__( 'Site %1$s', 'rebuild-foundation' ) . '</span>', $site_link ); // WPCS: XSS OK.

		}

	}

    /* translators: used between list items, there is a space after the comma */
	$categories_list = get_the_category_list( esc_html__( ', ', 'rebuild-foundation' ) );
	if ( $categories_list ) {
		printf( '<span class="meta site-links">' . esc_html__( 'Category %1$s', 'rebuild-foundation' ) . '</span>', $categories_list ); // WPCS: XSS OK.
	}

    /* translators: used between list items, there is a space after the comma */
    $tags_list = get_the_tag_list( '', esc_html__( ', ', 'rebuild-foundation' ) );
    if ( $tags_list ) {
        printf( '<span class="meta tags-links">' . esc_html__( 'Tagged %1$s', 'rebuild-foundation' ) . '</span>', $tags_list ); // WPCS: XSS OK.
    }

    if ( 'events' === get_post_type() ) { 
	    /* translators: used between list items, there is a space after the comma */
	    $eventcat_list =  get_the_term_list( get_the_ID(), 'event-categories', '', ', ' );
	    if ( $eventcat_list ) {
	        printf( '<span class="meta eventcat-links">' . esc_html__( 'Event Category %1$s', 'rebuild-foundation' ) . '</span>', $eventcat_list ); // WPCS: XSS OK.
	    }
    }
	// Hide category and tag text for pages.
	// if ( 'post' === get_post_type() ) {
	// 	/* translators: used between list items, there is a space after the comma */
	// 	$categories_list = get_the_category_list( esc_html__( ', ', 'rebuild-foundation' ) );
	// 	if ( $categories_list && rebuild_foundation_categorized_blog() ) {
	// 		printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'rebuild-foundation' ) . '</span>', $categories_list ); // WPCS: XSS OK.
	// 	}

	// 	/* translators: used between list items, there is a space after the comma */
	// 	$tags_list = get_the_tag_list( '', esc_html__( ', ', 'rebuild-foundation' ) );
	// 	if ( $tags_list ) {
	// 		printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'rebuild-foundation' ) . '</span>', $tags_list ); // WPCS: XSS OK.
	// 	}
	// }

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'rebuild-foundation' ), esc_html__( '1 Comment', 'rebuild-foundation' ), esc_html__( '% Comments', 'rebuild-foundation' ) );
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'rebuild-foundation' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */

function rebuild_foundation_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'rebuild_foundation_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		// $all_the_cool_cats = get_categories( array(
		// 	'fields'     => 'ids',
		// 	'hide_empty' => 1,

		// 	// We only need to know if there is more than one category.
		// 	'number'     => 2,
		// ) );

		wp_get_post_terms( 'rebuild_sites_category', array( 
			'fields' => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
			)
		);

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'rebuild_foundation_categories', $all_the_cool_cats );
	}

	// if ( $all_the_cool_cats > 1 ) {
	// 	// This blog has more than 1 category so rebuild_foundation_categorized_blog should return true.
	// 	return true;
	// } else {
	// 	// This blog has only 1 category so rebuild_foundation_categorized_blog should return false.
	// 	return false;
	// }
	return true;
}

/**
 * Flush out the transients used in rebuild_foundation_categorized_blog.
 */
function rebuild_foundation_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'rebuild_foundation_categories' );
}
add_action( 'edit_category', 'rebuild_foundation_category_transient_flusher' );
add_action( 'save_post',     'rebuild_foundation_category_transient_flusher' );


/**
 * Get site link
 * Retrieves first 'rebuild_sites' post type associated with current 'rebuild_sites_category'
 */

function rebuild_foundation_get_site_link() {

	$site_cat = wp_get_post_terms( $post_id, 'rebuild_sites_category', array( "fields" => "slugs" ) )[0];

	

}

