<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">

		<div class="entry-meta location"><?php echo get_field( 'site_location' )['address']; ?></div><!-- .entry-meta -->

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

	</header><!-- .entry-header -->

	<div class="entry-content">

		<section class="main-content">

			<div class="gallery"></div>

			<div class="description">
				<?php the_content(); ?>
			</div>

			<div id="details">

				<h4 class="hours-location"><?php _e( 'Hours & Location', 'rebuild-foundation' ); ?></h4>

				<div class="entry-meta location"><?php echo get_field( 'site_location' )['address']; ?></div>

				<div class="entry-meta hours"><?php the_field( 'site_hours' ); ?></div>
				
			</div>
			
		</section>

		<section class="exhibitions">
			
			<h3><?php _e( 'Exhibitions', 'rebuild-foundation' ); ?></h3>

		</section>

		<section class="events">
			
			<div class="upcoming-events">
				<h3><?php _e( 'Upcoming Events', 'rebuild-foundation' ); ?></h3>

			</div>

			<div class="past-events">
				<h3><?php _e( 'Past Events', 'rebuild-foundation' ); ?></h3>
			</div>

		</section>

		
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

