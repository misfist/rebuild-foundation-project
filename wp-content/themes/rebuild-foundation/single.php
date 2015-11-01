<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package RebuildFoundation
 */

get_header(); ?>

	<div id="primary" class="content-area">
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
		
		<main id="main" class="site-main" role="main">
		
		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', get_post_type() ); ?>

			<?php the_post_navigation(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
