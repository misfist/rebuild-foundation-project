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
  //   if( function_exists( 'rebuild_exhibition_fields' ) ) {
  //       $custom_fields = rebuild_exhibition_fields();
  //       echo '<pre>';
		// var_dump( $custom_fields );
		// echo '</pre>';
  //   }
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta dat">
			<span class="start-date"><?php echo isset( $custom_fields['start_date'] ) ? date( 'F j, Y', $custom_fields['start_date'] )  : ''; ?></span>
			<span class="end-date"><?php echo isset( $custom_fields['end_date'] ) ? date( 'F j, Y', $custom_fields['end_date'] )  : ''; ?></span>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">

		<!-- Gallery -->
		<?php if( !empty( $custom_fields['gallery'] ) ) { ?>
		<div class="gallery">
		<?php
		foreach( $custom_fields['gallery'] as $image ) { ?>

			<div class="site-slide"><img src="<?php echo $image; ?>" alt=""></div>

		<?php }?>
		</div>
		<?php } elseif( has_post_thumbnail() ) { ?>

		<div class="gallery single-image">

			<div class="site-image"><?php the_post_thumbnail( 'full' ); ?></div>
			
		</div>

		<?php }?>

		<div class="description">
			<?php the_content(); ?>
		</div>

		<div id="details">

			<h4 class="hours-location"><?php _e( 'On View At', 'rebuild-foundation' ); ?></h4>

			<div class="entry-meta location">
				<span class="location-name"><?php echo isset( $custom_fields['location_name'] ) ? $custom_fields['location_name']  : ''; ?></span>
			</div>

			<h4 class="hours-location"><?php _e( 'Hours & Location', 'rebuild-foundation' ); ?></h4>

			<div class="entry-meta hours">
				<span class="street"><?php echo isset( $custom_fields['street'] ) ? $custom_fields['street']  : ''; ?></span>
				<span class="street"><?php echo isset( $custom_fields['streets'] ) ? $custom_fields['streets']  : ''; ?></span>
				<span class="city"><?php echo isset( $custom_fields['city'] ) ? $custom_fields['city']  : ''; ?></span>
				<span class="state"><?php echo isset( $custom_fields['state'] ) ? $custom_fields['state']  : ''; ?></span>
				<span class="zip"><?php echo isset( $custom_fields['zip'] ) ? $custom_fields['zip']  : ''; ?></span>
				<span class="hours"><?php echo isset( $custom_fields['hours'] ) ? $custom_fields['hours']  : ''; ?></span>
			</div>
			
		</div>

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

