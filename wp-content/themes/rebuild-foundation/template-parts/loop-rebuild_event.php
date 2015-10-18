<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php 
    $terms = get_the_terms( get_the_ID(), 'rebuild_site_category' );
    $start_date = get_field( 'start_date' );
    $end_date = get_field( 'end_date' );
    $featured_image = has_post_thumbnail( get_the_ID() ) ? wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' ) : '';

?>

<article id="event-<?php the_ID(); ?>" data-year="<?php echo date( 'Y', strtotime( $start_date ) ); ?>" data-month="<?php echo date( 'm', strtotime( $start_date ) ); ?>"  <?php post_class( 'event' ); ?>>

    <?php the_title( sprintf( '<h3 class="entry-title event-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>

    <div class="site-name"><?php rebuild_get_site_category_content(); ?></div>

    <div class="entry-meta event-date"  style="background-image: url( <?php echo $featured_image[0]; ?> )">
        <time datetime="<?php echo date( 'Y-m-d', strtotime( $start_date ) ); ?>"><?php echo date( 'M j', strtotime( $start_date ) ); ?></time>
    </div><!-- .entry-meta -->
    
</article><!-- #post-## -->
