<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<main id="main" class="site-main" role="main">
	<section class="no-results not-found">
		<!-- <header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'rebuild-foundation' ); ?></h1>
		</header>.page-header -->

		<div class="page-content">
			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

				<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'rebuild-foundation' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

			<?php elseif ( is_search() ) : ?>

				<h2><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'rebuild-foundation' ); ?></h2>

			<?php else : ?>

				<h2><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'rebuild-foundation' ); ?></h2>

			<?php endif; ?>
		</div><!-- .page-content -->
	</section><!-- .no-results -->
</main>
