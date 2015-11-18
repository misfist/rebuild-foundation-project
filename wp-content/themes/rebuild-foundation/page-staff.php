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

        <header class="page-header entry-header">

            <?php the_title( '<h1 class="entry-title page-title">', '</h1>' ); ?>

        </header><!-- .entry-header -->

        <main id="main" class="site-main" role="main">

            <?php if( have_posts() ) : ?>

                <?php while( have_posts() ) : ?>

                    <?php the_post(); ?>

                    <?php get_template_part( 'template-parts/content', 'staff' ); ?>

                <?php endwhile ?>

            <?php endif; ?>

            <article class="posts-list">

                <?php $taxonomy = 'staff_category'; ?>
                <?php $post_type = 'staff'; ?>

                <?php $tax_terms = get_terms( $taxonomy, 'orderby=name'); ?>

                <?php foreach ( $tax_terms as $term ) : ?>

                    <h2 class="tax-title">
                        <?php echo $term->name; ?>
                    </h2>

                    <ul class="<?php echo $term->slug; ?>-group">

                    <?php $args = array(
                        'posts_per_page' => -1,
                        $taxonomy => $term->slug,
                        'post_type' => $post_type,
                        'orderby' => 'title',
                        'order'   => 'ASC',
                    );
                    ?>
                    <?php $tax_query = new WP_Query( $args ); ?>

                    <?php if ( $tax_query->have_posts() ) : ?>

                        <?php while ( $tax_query->have_posts() ) : $tax_query->the_post(); ?>

                        <?php

                            /*
                             * Include the Post-Format-specific template for the content.
                             * If you want to override this in a child theme, then include a file
                             * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                             */
                            get_template_part( 'template-parts/loop', get_post_type() );
                        ?>

                        <?php endwhile; ?>

                        <?php rebuild_custom_posts_navigation(); ?>

                    <?php else : ?>

                        <?php get_template_part( 'template-parts/loop', 'none' ); ?>

                    <?php endif; ?>

                    </ul>

                <?php endforeach; ?>

                <footer class="entry-footer">
                <?php
                    edit_post_link(
                        sprintf(
                            /* translators: %s: Name of current post */
                            esc_html__( 'Edit %s', 'rebuild-foundation' ),
                            the_title( '<span class="screen-reader-text">"', '"</span>', false )
                        ),
                        '<span class="edit-link">',
                        '</span>'
                    );
                ?>
                </footer><!-- .entry-footer -->

            </article>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
