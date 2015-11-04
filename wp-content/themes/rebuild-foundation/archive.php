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

            <?php if( 'site' != get_post_type() ) : ?>

                <div class="filters">
                    <?php rebuild_site_category_filter(); ?>

                    <?php rebuild_taxonomy_filter(); ?>

                    <?php ( is_post_type_archive( 'event' ) ) ? rebuild_event_month_filter() : '' ?>
                </div>

            <?php endif; ?>

        </header><!-- .entry-header -->

        <?php ( is_post_type_archive( 'event' ) ) ? rebuild_event_year_filter() : ''; ?>

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
