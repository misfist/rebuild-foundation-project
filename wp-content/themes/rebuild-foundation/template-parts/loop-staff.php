<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<li id="post-<?php the_ID(); ?>" <?php post_class( get_post_type() ); ?>>
    
    <div class="featured-image">
        <?php if( has_post_thumbnail() ) :?>

            <figure class="entry-thumbnail">
                <?php the_post_thumbnail( 'thumbnail' ); ?>
            </figure>

        <?php endif; ?>
    </div>

    <?php the_title( '<h3 class="entry-title">', '</h3>' ); ?>

    <?php if( get_field( 'staff_title' ) ) :?>

        <div class="entry-meta staff-title">
            <?php the_field( 'staff_title' ); ?>
        </div><!-- .entry-meta -->

    <?php endif; ?>

    <div class="entry-content">
        <?php
            the_excerpt( sprintf(
                /* translators: %s: Name of current post. */
                wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'rebuild-foundation' ), array( 'span' => array( 'class' => array() ) ) ),
                the_title( '<span class="screen-reader-text">"', '"</span>', false )
            ) );
        ?>

    </div><!-- .entry-content -->

</li><!-- #post-## -->
