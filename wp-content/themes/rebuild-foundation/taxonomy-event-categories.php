<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

get_header(); ?>

	<?php //get the taxonomy object and convert it to EM_Category for output
	$taxonomy = get_queried_object();
	$EM_Category = em_get_category( $taxonomy->term_id );
	?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<header class="page-header">

				<h1 class="page-title"><?php echo $EM_Category->output( get_option( 'dbem_category_page_title_format' ) ); ?></h1>

			</header><!-- .page-header -->

			<?php echo $EM_Category->output_single(); ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
