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

<article id="post-<?php the_ID(); ?>" <?php post_class( 'site' ); ?>>

    <header class="entry-header site-header">

        <div class="entry-meta location">
            <?php ( function_exists( 'rebuild_formatted_address' ) ) ? rebuild_formatted_address() : ''; ?>
        </div><!-- .entry-meta -->

        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

    </header><!-- .entry-header -->

    <div class="site-image" style="background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id() ); ?>)"></div>

    <div class="entry-content site-content">

        <section class="main-content">

            <!-- //// Gallery or Image-->
            <?php 
            $images = get_field( 'post_gallery' );

            if( $images ): ?>
            
                <div class="gallery">
                    <?php foreach( $images as $image ): ?>

                        <div class="site-slide">

                            <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" />

                            <p><?php echo $image['caption']; ?></p>

                        </div>

                    <?php endforeach; ?>
                </div>

            <?php elseif( has_post_thumbnail() ): ?>

                 <div class="gallery single-image">

                    <figure class="entry-thumbnail site-image">

                        <?php the_post_thumbnail( 'full' ); ?>
     
                    </figure>

                </div>

            <?php endif; ?>

            <div class="description">
                <?php the_content(); ?>
            </div>

            <div id="details">

                <h4 class="hours-location"><?php _e( 'Hours & Location', 'rebuild-foundation' ); ?></h4>

                <div class="entry-meta location">
                    <?php ( function_exists( 'rebuild_formatted_address' ) ) ? rebuild_formatted_address() : ''; ?>

                </div>

                <div class="entry-meta hours">
                    <?php the_field( 'hours' ); ?>
                </div>
                
            </div>
            
        </section>

        <!-- //// Exhibitions Section -->
        <section class="exhibitions">

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

               <h2><?php _e( 'Exhibitions', 'rebuild-foundation' ); ?></h2>

               <?php while ( $exhibitions->have_posts() ) : $exhibitions->the_post(); ?>

                    <?php get_template_part( 'template-parts/loop', $exhibition_post_type ); ?>

                <?php endwhile; else: ?>

                    <?php get_template_part( 'template-parts/loop', 'none' ); ?>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </section>

        <!-- //// Events Section -->
        <section class="events">

            <?php
            $event_post_type = 'event';
            $today = date( 'Ymd' );
            ?>
            
            <div class="upcoming-events">
                <?php
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
                    'orderby' => 'meta_value',
                    'meta_key' => 'start_date',
                    'tax_query' => $site_tax,
                    'meta_query' => $future_scope,
                );

                $future_event_query = new WP_Query( $future_event_args ); ?>

                <?php if ( $future_event_query->have_posts() ) : ?>

                    <h2><?php _e( 'Upcoming Events', 'rebuild-foundation' ); ?></h2>

                    <?php while ( $future_event_query->have_posts() ) : $future_event_query->the_post(); ?>

                        <?php get_template_part( 'template-parts/loop', 'site-event-future' ); ?>

                    <?php endwhile; else: ?>

                        <?php get_template_part( 'template-parts/loop', 'none' ); ?>

                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

            </div>

            <div class="past-events">
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
                    'tax_query' => $site_tax,
                    'meta_query' => $past_scope,
                    'order' => 'ASC',
                    'orderby' => 'meta_value',
                    'meta_key' => 'start_date'
                );

                $future_event_query = new WP_Query( $future_event_args ); ?>

                <?php if ( $future_event_query->have_posts() ) : ?>

                    <h2><?php _e( 'Past Events', 'rebuild-foundation' ); ?></h2>

                    <?php while ( $future_event_query->have_posts() ) : $future_event_query->the_post(); ?>

                        <?php get_template_part( 'template-parts/loop', 'site-event-past' ); ?>

                    <?php endwhile; else: ?>

                        <?php get_template_part( 'template-parts/loop', 'none' ); ?>

                <?php endif; ?>

                <?php wp_reset_postdata(); ?>
    
            </div>

        </section>

        <!-- //// Blog Section - Only show if 'show_blog_posts' is true -->

        <?php if( get_field( 'show_blog_posts' ) ) : ?>

            <section class="posts">
                
                 <?php 
                // WP_Query arguments
                $blog_post_type = 'post';
                $blog_args = array (
                    'post_type' => $blog_post_type,
                    'tax_query' => $site_tax,
                );

                $blog_query = new WP_Query( $blog_args ); ?>

                <?php if ( $blog_query->have_posts() ) : ?>

                   <h2><?php _e( 'Recent Blog Posts', 'rebuild-foundation' ); ?></h2>

                    <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>

                        <?php get_template_part( 'template-parts/loop-site', $blog_post_type ); ?>

                    <?php endwhile; else: ?>

                        <?php get_template_part( 'template-parts/loop', 'none' ); ?>

                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

            </section>

        <?php endif; ?>

        <?php
            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rebuild-foundation' ),
                'after'  => '</div>',
            ) );
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php rebuild_foundation_entry_footer(); ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-## -->

