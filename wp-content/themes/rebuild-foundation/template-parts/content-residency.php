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

<article id="post-<?php the_ID(); ?>" <?php post_class( get_post_type() ); ?>>

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

                <span class="start-date"><?php echo ( $start_date ) ? date( 'F j, Y', strtotime( $start_date ) )  : ''; ?></span> –
                <span class="end-date"><?php echo ( $end_date ) ? date( 'F j, Y', strtotime( $end_date ) )  : ''; ?></span>

            <?php endif; ?>

        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <div class="entry-content">

        

        <div class="description">
            <?php the_content(); ?>

            <?php get_template_part( 'template-parts/gallery' ); ?>

            <?php get_template_part( 'template-parts/social-media' ); ?>
            <footer class="entry-footer">
                <?php rebuild_foundation_entry_footer(); ?>
            </footer><!-- .entry-footer -->
        </div>



        <div class="content-side">
            
            <?php if( has_post_thumbnail() ): ?>

                 <div class="single-image">

                    <figure class="entry-thumbnail">

                        <?php the_post_thumbnail( 'full' ); ?>

                        <?php $caption = rebuild_get_the_feature_caption(); ?>

                        <?php echo ( $caption ) ? '<figcaption>' . $caption . '</figcaption>' : ''; ?>

                    </figure>

                </div>

            <?php endif; ?>

            <div id="details">

                <h4><?php _e( '', 'rebuild-foundation' ); ?></h4>

                <div class="entry-meta location">
                    <?php echo ( function_exists( 'rebuild_get_site_link' ) ) ? rebuild_get_site_link() : ''; ?>
                </div>

                <?php if( get_field( 'location' ) || get_field( 'hours' ) ) : ?>

                    <h4 class="hours-location"><?php _e( 'Hours & Location', 'rebuild-foundation' ); ?></h4>

                    <?php if( get_field( 'location' ) ) : ?>

                        <div class="entry-meta location">
                            <?php ( function_exists( 'rebuild_formatted_address' ) ) ? rebuild_formatted_address() : ''; ?>
                        </div>

                        <?php rebuild_google_map_link(); ?>

                    <?php endif; ?>

                    <?php if( get_field( 'hours' ) ) : ?>

                        <div class="entry-meta hours">
                            <?php the_field( 'hours' ); ?>
                        </div>

                    <?php endif; ?>

                <?php endif; ?>

                <?php if( get_field( 'residencybio' ) ) : ?>

                <div class="bio">

                    <h3 class="section-title"><?php _e( 'Biography', 'rebuild-foundation' ); ?></h3>

                    <?php the_field( 'residencybio' ); ?>

                </div>

                <?php endif; ?>

            </div>

            <?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rebuild-foundation' ),
                    'after'  => '</div>',
                ) );
            ?>
        </div>
    </div><!-- .entry-content -->

</article><!-- #post-## -->

