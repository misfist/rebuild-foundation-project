<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( get_post_type() ); ?>>
    <header class="entry-header">

        <?php the_title(); ?>

        <div class="entry-meta staff-title">
            <?php the_field( 'staff_title' ); ?>
        </div><!-- .entry-meta -->

    </header><!-- .entry-header -->

    <div class="featured-image">
        <?php if( has_post_thumbnail( ) ) :?>

            <figure class="entry-thumbnail">
                <?php the_post_thumbnail( 'exhibition-thumbnail'); ?>
                <?php $caption = rebuild_get_the_feature_caption(); ?>
                <?php if ( $caption ) : ?>
                    <figcaption class="caption"><?php echo $caption; ?></figcaption>
                <?php endif; ?>
            </figure>

        <?php endif; ?>
    </div>

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
        
    </footer><!-- .entry-footer -->
</article><!-- #post-## -->
