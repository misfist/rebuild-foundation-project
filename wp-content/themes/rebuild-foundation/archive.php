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
        <main id="main" class="site-main" role="main">

        <?php

        global $wp_query;

        ?>

        <?php rebuild_site_category_filter(); ?>

        <?php rebuild_taxonomy_filter(); ?>

        <?php ( 'rebuild_event' == get_post_type() ) ?rebuild_event_year_filter() : ''; ?>

        <?php ( 'rebuild_event' == get_post_type() ) ? rebuild_event_month_filter() : '' ?>

        <?php if ( have_posts() ) : ?>

            <div class="posts-list">

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

                <?php get_template_part( 'template-parts/content', 'none' ); ?>

            <?php endif; ?>

            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
