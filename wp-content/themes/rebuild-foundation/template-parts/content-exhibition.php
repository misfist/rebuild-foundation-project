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

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'exhibition' ); ?>>

    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

        <div class="entry-meta date">
            <?php
            $start_date = get_field( 'start_date' );
            $end_date = get_field( 'end_date' )
            ?>

            <?php
            if( get_field( 'ad_hoc_date' ) ) : ?>

                <?php the_field( 'ad_hoc_date' ); ?>

            <?php else: ?>

                <span class="start-date"><?php echo ( $start_date ) ? date( 'F j, Y', strtotime( $start_date ) )  : ''; ?></span>
                <span class="end-date"><?php echo ( $end_date ) ? date( 'F j, Y', strtotime( $end_date ) )  : ''; ?></span>

            <?php endif; ?>

        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <div class="entry-content">

        <div class="description">
            <?php the_content(); ?>
        </div>

        <div class="content-side">
            <!-- //// Gallery -->
            <?php 
            $images = get_field( 'post_gallery' );

            if( $images ): ?>
                <div class="gallery">
                    <?php foreach( $images as $image ): ?>
                        <div class="site-slide">
                            <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" />
                            <p><?php echo $image['caption']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif( has_post_thumbnail( ) ) :?>

                <figure class="entry-thumbnail">
                    <?php the_post_thumbnail( ); ?>
                    <?php $caption = rebuild_get_the_feature_caption(); ?>
                    <?php if ( $caption ) : ?>
                        <figcaption class="caption"><?php echo $caption; ?></figcaption>
                    <?php endif; ?>
                </figure>

            <?php endif; ?>

            <div id="details">

                <h4><?php _e( 'On View At', 'rebuild-foundation' ); ?></h4>

                <div class="entry-meta location">
                    <?php echo ( function_exists( 'rebuild_get_site_link' ) ) ? rebuild_get_site_link() : ''; ?>
                </div>

                <h4><?php _e( 'Hours & Location', 'rebuild-foundation' ); ?></h4>

                <div class="entry-meta address">
                    <?php ( function_exists( 'rebuild_formatted_address' ) ) ? rebuild_formatted_address() : ''; ?>
                </div>

                <div class="entry-meta hours">
                    <?php the_field( 'hours' ); ?>
                </div>
                
            </div>

            <?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rebuild-foundation' ),
                    'after'  => '</div>',
                ) );
            ?>
        </div>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php rebuild_foundation_entry_footer(); ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-## -->

