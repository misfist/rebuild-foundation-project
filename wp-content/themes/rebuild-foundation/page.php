<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <header class="entry-header">

            <h1 class="entry-title page-title"><?php the_title( ); ?></h1>

        </header><!-- .entry-header -->

        <div class="page-sub-menu">
            <?php if ( is_active_sidebar( 'submenu-widget' ) ) {
                dynamic_sidebar( 'submenu-widget' ); 
            }
            ?>
        </div>
        
        <main id="main" class="site-main" role="main">


            <div class="posts-list">

            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'template-parts/loop', get_post_type() ); ?>

                <?php
                    // If comments are open or we have at least one comment, load up the comment template.
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                ?>

            <?php endwhile; // End of the loop. ?>

            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
