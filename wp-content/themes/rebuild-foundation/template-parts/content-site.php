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
            $today = date( 'Ymd' );
            $current = 'current';
            $future = 'future';
            $past = 'past';
            $exhibition_tax = array(
                $site_tax,
                array(
                    'taxonomy' => 'exhibition_category',
                    'field'    => 'slug',
                    'terms'    => $current
                ),
            );
            $exhibitions_query = array( 
                'post_type'   => $exhibition_post_type,
                'meta_key' => 'start_date',
                'tax_query' => $exhibition_tax,
                'orderby' => 'meta_value_num',
            );

            $exhibitions = new WP_Query( $exhibitions_query ); ?>

            <?php if ( $exhibitions->have_posts() ) : ?>

            <!-- //// Exhibitions Section -->

            <section class="exhibitions">

               <h2><?php _e( 'Exhibitions', 'rebuild-foundation' ); ?></h2> | 
               <?php rebuild_get_site_category_content(); ?>

               <?php while ( $exhibitions->have_posts() ) : $exhibitions->the_post(); ?>

                    <?php get_template_part( 'template-parts/loop', $exhibition_post_type ); ?>



                <?php endwhile; ?>

                

            </section>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        <!-- //// Events Section -->

        <section class="events">

            <?php
            $event_post_type = 'event';
            $today = date( 'Ymd' );
            $future_scope = array(
                array(
                    'key' => 'start_date',
                    'value'=> $today,
                    'compare'=> '>=',
                    'type'=> 'date',
                ),
            );
            ?>

            <?php 
            // WP_Query arguments
            $future_event_args = array (
                'post_type' => $event_post_type,
                'posts_per_page' => 4,
                'orderby' => 'meta_value',
                'meta_key' => 'start_date',
                'tax_query' => $site_tax,
                'meta_query' => $future_scope,
            );

            $future_event_query = new WP_Query( $future_event_args ); ?>

            <?php if ( $future_event_query->have_posts() ) : ?>

            <div class="upcoming-events">

                <h2><?php _e( 'Upcoming Events', 'rebuild-foundation' ); ?></h2> | <?php rebuild_get_site_category_content(); ?>

                <?php while ( $future_event_query->have_posts() ) : $future_event_query->the_post(); ?>

                    <?php get_template_part( 'template-parts/loop', 'site-event-future' ); ?>

                <?php endwhile; ?>
                
                

            </div>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

    
            <?php
            $past_scope = array(
                array(
                    'key' => 'end_date',
                    'value'=> $today,
                    'compare'=> '<',
                    'type'=> 'date',
                ),
            );
            ?>

            <?php 
            // WP_Query arguments
            $future_event_args = array (
                'post_type' => $event_post_type,
                'posts_per_page' => 4,
                'tax_query' => $site_tax,
                'meta_query' => $past_scope,
                'order' => 'ASC',
                'orderby' => 'meta_value',
                'meta_key' => 'start_date'
            );

            $future_event_query = new WP_Query( $future_event_args ); ?>

            <?php if ( $future_event_query->have_posts() ) : ?>

            <div class="past-events">

                <h2><?php _e( 'Past Events', 'rebuild-foundation' ); ?></h2>

                <?php while ( $future_event_query->have_posts() ) : $future_event_query->the_post(); ?>

                    <?php get_template_part( 'template-parts/loop', 'site-event-past' ); ?>

                <?php endwhile; ?>

            </div>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
    
        </section>

        <!-- //// Blog Section - Only show if 'show_blog_posts' is true -->

        <?php if( get_field( 'show_blog_posts' ) ) : ?>

            <?php 
            // WP_Query arguments
            $blog_post_type = 'post';
            $blog_args = array (
                'post_type' => $blog_post_type,
                'tax_query' => $site_tax,
            );

            $blog_query = new WP_Query( $blog_args ); ?>

            <?php if ( $blog_query->have_posts() ) : ?>

           <section class="posts">

               <h2><?php _e( 'Recent Blog Posts', 'rebuild-foundation' ); ?></h2> | <?php rebuild_get_site_category_content(); ?>

                <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>

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

