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
        <div class="featured-image">
            <?php if( has_post_thumbnail( ) ) :?>

                <figure class="entry-thumbnail">
                    <?php the_post_thumbnail(); ?>
                    <?php $caption = rebuild_get_the_feature_caption(); ?>
                    <?php if ( $caption ) : ?>
                        <figcaption class="caption"><?php echo $caption; ?></figcaption>
                    <?php endif; ?>
                </figure>

            <?php endif; ?>
        </div>
        
        <header class="entry-header">

            <h1 class="entry-title page-title"><?php the_title( ); ?></h1>

        </header><!-- .entry-header -->

        <nav class="page-sub-menu" role="navigation">

            <a href="#main" class="skip">Skip to content</a>
            <?php wp_nav_menu( array( 
                'theme_location' => 'primary', 
                'menu_id' => 'sub-pages',
                'container_class' => '',
                'container' => '',
                'fallback_cb' => false
                ) ); ?>

        </nav>
        
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
