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
$start_date = get_field( 'start_date' );
$end_date = get_field( 'end_date' )
?>


<article id="exhibition-<?php the_ID(); ?>" data-start-date="<?php echo isset( $start_date ) ? date( 'Y-m-d', strtotime( $start_date ) )  : ''; ?>" data-end-date="<?php echo isset( $end_date ) ? date( 'Y-m-d', strtotime( $end_date ) )  : ''; ?>" <?php post_class( 'exhibition' ); ?>>

    <div class="col-1">
        <div id="details">

            <?php if( has_post_thumbnail() ) { ?>

            <div class="single-image featured-image">

                <div class="exhibition-image"><?php the_post_thumbnail( 'large' ); ?></div>
                
            </div>

            <?php }?>

            <h4 class="site-info"><?php _e( 'On View At', 'rebuild-foundation' ); ?></h4>

            <div class="entry-meta location">
                <?php echo ( function_exists( 'rebuild_get_site_link' ) ) ? rebuild_get_site_link() : ''; ?>
            </div>
            
        </div>
    </div>

    <div class="col-2">
        <header class="entry-header">

            <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

            <div class="entry-meta scope">
                
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

            <?php
                the_excerpt( sprintf(
                    /* translators: %s: Name of current post. */
                    wp_kses( __( 'Learn more %s <span class="meta-nav">&rarr;</span>', 'rebuild-foundation' ), array( 'span' => array( 'class' => array() ) ) ),
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
            
            <a href="<?php esc_url( get_permalink() ); ?>" class="button btn">View</a>

        </footer><!-- .entry-footer -->
    </div>
</article><!-- #post-## -->
