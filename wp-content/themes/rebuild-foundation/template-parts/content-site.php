<?php
/**
 * Template part for displaying single sites.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php 

$site_cat = rebuild_get_site_category();
$site_tax = array(
    array(
        'taxonomy' => 'site_category',
        'field'    => 'slug',
        'terms'    => $site_cat
    ),
);  
$today = date( 'Ymd' );

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( get_post_type() ); ?>>

    <header class="entry-header site-header">

        <?php if( get_field( 'location' ) ) : ?>

            <div class="entry-meta location">
                <?php ( function_exists( 'rebuild_formatted_address' ) ) ? rebuild_formatted_address() : ''; ?>
            </div>

        <?php endif; ?>

        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

    </header><!-- .entry-header -->

    <?php get_template_part( 'template-parts/gallery-site' ); ?>

    <div class="entry-content site-content">

        <section class="main-content">

            <div class="description">
                <?php the_content(); ?>
            </div>

            <div id="details">

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
                
            </div>
            
        </section>

        <?php

        $exhibition_post_type = 'exhibition';
        $scope = rebuild_exhibition_scope( $site_cat );
        $exhibition_query = rebuild_get_exhibition_query( $site_cat, $scope );

        ?>

        <?php if( isset( $exhibition_query ) && $exhibition_query->have_posts ) : ?>

        <!-- //// Exhibitions Section -->

        <section class="exhibitions">

           <h2><?php _e( 'Exhibitions', 'rebuild-foundation' ); ?></h2> | 
           <?php rebuild_get_site_category_content( 'exhibition' ); ?>

           <?php while ( $exhibition_query->have_posts() ) : $exhibition_query->the_post(); ?>

                <?php get_template_part( 'template-parts/loop', $exhibition_post_type ); ?>

            <?php endwhile; ?>

        </section>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

        <!-- //// Events Section -->

        <section class="events">

            <?php 
            $site_event_future_query = rebuild_event_query( $site_cat, $scope = 'future', 4 );

            ?>

            <?php if ( isset( $site_event_future_query ) && $site_event_future_query->have_posts() ) : ?>

            <div class="upcoming-events">

                <h2><?php _e( 'Upcoming Events', 'rebuild-foundation' ); ?></h2> | <?php rebuild_get_site_category_content( 'event' ); ?>

                <?php while ( $site_event_future_query->have_posts() ) : $site_event_future_query->the_post(); ?>

                    <?php get_template_part( 'template-parts/loop', 'site-event-future' ); ?>

                <?php endwhile; ?>
                
            </div>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

            <?php $site_event_past_query = rebuild_event_query( $site_cat, $scope = 'past', 4 ); ?>

            <?php if ( isset( $site_event_past_query ) && $site_event_past_query->have_posts() ) : ?>

            <div class="past-events">

                <h2><?php _e( 'Past Events', 'rebuild-foundation' ); ?></h2>

                <?php while ( $site_event_past_query->have_posts() ) : $site_event_past_query->the_post(); ?>

                    <?php get_template_part( 'template-parts/loop', 'site-event-past' ); ?>

                <?php endwhile; ?>

            </div>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
    
        </section>

        <!-- //// Blog Section - Only show if 'show_blog_posts' is true -->

        <?php if( get_field( 'show_blog_posts' ) ) : ?>

             <?php 
             $site_name = ( strlen( $site_cat ) < 20 ) ? $site_cat : substr( $site_cat, 0, 19 );
            $trans_name = $site_name . '_blog_q';
            $cache_time = 60; // Time in minutes between updates.
            $blog_post_type = 'post';

            if( false === ( $site_blog_query = get_transient( $trans_name ) ) ) {

                echo 'no transient';

                $blog_args = array (
                    'post_type' => $blog_post_type,
                    'tax_query' => $site_tax,
                    'posts_per_page' => 4
                );
                   
               $site_blog_query = new WP_Query( $blog_args );

               set_transient( $trans_name, $site_blog_query, 60 * $cache_time );
            }
            ?>

            <?php if ( $site_blog_query->have_posts() ) : ?>

           <section class="posts">

               <h2><?php _e( 'Recent Blog Posts', 'rebuild-foundation' ); ?></h2> | <?php rebuild_get_site_category_content( 'post' ); ?>

                <?php while ( $site_blog_query->have_posts() ) : $site_blog_query->the_post(); ?>

                    <?php get_template_part( 'template-parts/loop-site', $blog_post_type ); ?>

                <?php endwhile; ?>
                
            </section>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        <?php endif; ?>

        <?php
            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rebuild-foundation' ),
                'after'  => '</div>',
            ) );
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">

    </footer><!-- .entry-footer -->
</article><!-- #post-## -->

