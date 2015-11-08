<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <header class="entry-header">

            <?php if( is_archive() && !is_post_type_archive( 'site' )  ) : ?>

                <?php rebuild_site_context_nav(); ?>

            <?php endif; ?>

            <?php if( is_post_type_archive( 'event' ) ) : ?>

                <?php rebuild_event_year_filter(); ?>

            <?php endif; ?>

            <div class="filters">
                
                <?php if( is_post_type_archive( 'exhibition' ) ) : ?>

                    <?php rebuild_exhibition_filter(); ?>

                <?php endif; ?>

                <?php if( is_post_type_archive( 'event' ) ) : ?>

                    <?php rebuild_event_month_filter(); ?>

                <?php endif; ?>
            </div>

        </header><!-- .entry-header -->

        

        <main id="main" class="site-main" role="main">

        <?php global $wp_query; ?>

            <div class="posts-list">

            <?php if ( have_posts() ) : ?>

                <?php while ( have_posts() ) : the_post(); ?>

                <?php

                    /*
                     * Include the Post-Format-specific template for the content.
                     * If you want to override this in a child theme, then include a file
                     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                     */
                    get_template_part( 'template-parts/loop', get_post_type() );
                ?>

                <?php endwhile; ?>

            <?php the_posts_navigation(); ?>

            <?php else : ?>

                <?php get_template_part( 'template-parts/loop', 'none' ); ?>

            <?php endif; ?>

            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
