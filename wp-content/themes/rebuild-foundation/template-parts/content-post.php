<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<div class="featured-image">
	<?php if( has_post_thumbnail( ) ) :?>

        <figure class="entry-thumbnail">
            <?php the_post_thumbnail( ); ?>
            <?php $caption = rebuild_get_the_feature_caption(); ?>
            <?php if ( $caption ) : ?>
                <figcaption class="caption"><?php echo $caption; ?></figcaption>
            <?php endif; ?>
        </figure>

	<?php endif; ?>
</div>

<article id="post-<?php the_ID(); ?>" <?php post_class( get_post_type() ); ?>>

	<header class="entry-header">

		<div class="entry-meta site-name">			

			<?php rebuild_get_site_category_content(); ?>

		</div><!-- .entry-meta -->

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<time datetime="<?php echo get_the_date( 'Y-m-d' ); ?>"><?php echo get_the_date( 'M d' ); ?></time>
		</div><!-- .entry-meta -->
		
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'rebuild-foundation' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
		?>

		<?php get_template_part( 'template-parts/social-media' ); ?>

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
