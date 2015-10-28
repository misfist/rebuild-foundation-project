<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'page' ); ?>>

	<div class="entry-content">
		<?php
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'rebuild-foundation' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
		?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rebuild-foundation' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php rebuild_foundation_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<div class="featured-image">
	<?php if( has_post_thumbnail( ) ) :?>

		<figure class="entry-thumbnail">
			<?php the_post_thumbnail(''); ?>
			<?php if ( $caption = get_post( get_post_thumbnail_id() )->post_excerpt ) : ?>
			    <figcaption class="caption"><?php echo $caption; ?></figcaption>
			<?php endif; ?>
		</figure>

	<?php endif; ?>
</div>

