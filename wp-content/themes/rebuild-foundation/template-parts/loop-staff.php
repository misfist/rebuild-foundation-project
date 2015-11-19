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
    
    <?php if( has_post_thumbnail() ) :?>
        <div class="staff-image">

            <figure class="entry-thumbnail">
                <?php the_post_thumbnail( 'thumbnail' ); ?>
            </figure>

        </div>
    <?php endif; ?>
    <div class="staff-main">
        <h3 class="entry-title">

            <?php if( get_field( 'staff_email' ) ) :?>

            <a href="<?php echo 'mailto:' . get_field( 'staff_email' ) ;?>">

            <?php endif; ?>

            <?php the_title( ); ?>

            <?php if( get_field( 'staff_email' ) ) : ?>

            </a>

            <?php endif; ?>

        </h3>

        <?php $term = get_the_terms( get_the_id(), 'staff_category' ); ?>

        <?php if( get_field( 'staff_title' ) && 'board-of-directors' !== $term[0]->slug ) :?>

            <div class="entry-meta staff-title">
                <?php the_field( 'staff_title' ); ?>
            </div><!-- .entry-meta -->

        <?php endif; ?>

        <?php if( get_the_content() ) : ?>

            <div class="entry-content">
                <?php
                    the_excerpt( sprintf(
                        /* translators: %s: Name of current post. */
                        wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'rebuild-foundation' ), array( 'span' => array( 'class' => array() ) ) ),
                        the_title( '<span class="screen-reader-text">"', '"</span>', false )
                    ) );
                ?>

            </div><!-- .entry-content -->

        <?php endif; ?>
    </div>
</li><!-- #post-## -->
