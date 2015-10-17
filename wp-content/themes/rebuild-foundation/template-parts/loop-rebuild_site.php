<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'site' ); ?> style="background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>)">
	<header class="entry-header">

        <div class="entry-meta location">
            <?php echo ( function_exists( 'rebuild_get_location' ) ) ? rebuild_get_location() : ''; ?>
        </div><!-- .entry-meta -->

        <h2 class="entry-title">
        	<a href="<?php echo esc_url( get_permalink( get_the_ID() ) ) ?>" rel="bookmark">
        		<?php
                $short_name = get_field( 'short_name' );

                echo ( !empty( $short_name ) ) ? $short_name : the_title(); ?>
        	</a>
        </h2>

	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_excerpt( sprintf(
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
		
		<a href="<?php esc_url( get_permalink() ); ?>" class="button btn">View</a>

		<?php rebuild_foundation_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
