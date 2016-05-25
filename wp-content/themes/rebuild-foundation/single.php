<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package RebuildFoundation
 */

get_header(); ?>

    <div id="primary" class="content-area">
        
        <header class="page-header">

            <?php if( is_singular( array( 'event', 'exhibition', 'residency', 'post' ) ) ) : ?>

                <?php rebuild_site_context_nav(); ?>

            <?php endif; ?>

            <?php if( is_singular( array( 'event' ) ) ) : ?>

                <?php rebuild_event_year_filter(); ?>

                <div class="filters">

                    <?php rebuild_event_month_filter(); ?>
                    
                </div>

            <?php endif; ?>

            <?php if( is_singular( array( 'exhibition' ) ) ) : ?>

                <div class="filters">

                    <?php rebuild_exhibition_filter(); ?>

                </div>

            <?php endif; ?>

            <?php if( is_singular( array( 'residency' ) ) ) : ?>

                <div class="filters">

                    <?php rebuild_residency_filter(); ?>

                </div>

            <?php endif; ?>

        </header>

        <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php get_template_part( 'template-parts/content', get_post_type() ); ?>

            <?php the_post_navigation(); ?>

            <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
            ?>

        <?php endwhile; // End of the loop. ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
