<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php 
    if( function_exists( 'rebuild_exhibition_fields' ) ) {
        $custom_fields = rebuild_exhibition_fields();
        echo '<pre>';
		var_dump( $custom_fields );
		echo '</pre>';
    }
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<?php rebuild_foundation_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
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

