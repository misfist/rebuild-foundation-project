<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php 

    if( function_exists( 'rebuild_site_fields' ) ) {
        $custom_fields = rebuild_site_fields();
        // echo '<pre>';
        // var_dump( $custom_fields );
        // echo '</pre>';
    }
    
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">

        <div class="entry-meta location">
            <span class="street"><?php echo isset( $custom_fields['street'] ) ? $custom_fields['street']  : ''; ?></span>
            <span class="street"><?php echo isset( $custom_fields['street2'] ) ? $custom_fields['street2']  : ''; ?></span>
            <span class="city"><?php echo isset( $custom_fields['city'] ) ? $custom_fields['city']  : ''; ?></span>
            <span class="state"><?php echo isset( $custom_fields['state'] ) ? $custom_fields['state']  : ''; ?></span>
            <span class="zip"><?php echo isset( $custom_fields['zip'] ) ? $custom_fields['zip']  : ''; ?></span>
        </div><!-- .entry-meta -->

        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

    </header><!-- .entry-header -->

    <div class="entry-content">

        <!-- Main Content Section -->
        <section class="main-content">

            <!-- Gallery -->
            <?php if( !empty( $custom_fields['gallery'] ) ) { ?>
            <div class="gallery">
            <?php
            foreach( $custom_fields['gallery'] as $image ) { ?>

                <div class="site-slide"><img src="<?php echo $image; ?>" alt=""></div>

            <?php }?>
            </div>
            <?php } elseif( has_post_thumbnail() ) { ?>

            <div class="gallery single-image">

                <div class="site-image"><?php the_post_thumbnail( 'full' ); ?></div>
                
            </div>

            <?php }?>

            <div class="description">
                <?php the_content(); ?>
            </div>

            <div id="details">

                <h4 class="hours-location"><?php _e( 'Hours & Location', 'rebuild-foundation' ); ?></h4>

                <div class="entry-meta location">
                    <span class="street"><?php echo isset( $custom_fields['street'] ) ? $custom_fields['street']  : ''; ?></span>
                    <span class="city"><?php echo isset( $custom_fields['city'] ) ? $custom_fields['city']  : ''; ?></span>
                    <span class="state"><?php echo isset( $custom_fields['state'] ) ? $custom_fields['state']  : ''; ?></span>
                    <span class="zip"><?php echo isset( $custom_fields['zip'] ) ? $custom_fields['zip']  : ''; ?></span>
                </div>

                <div class="entry-meta hours">
                    <?php echo isset( $custom_fields['hours'] ) ? $custom_fields['hours']  : ''; ?>
                </div>
                
            </div>
            
        </section>

        <!-- Exhibitions Section -->
        <section class="exhibitions">
            
            <h2><?php _e( 'Exhibitions', 'rebuild-foundation' ); ?></h2>

            <?php 
            $exhibitions_query = new WP_Query( array(
                'post_type' => 'rebuild_exhibitions', 
                'orderby' => 'meta_value',
                'meta_key' => 'start_date' 
            ) );

            if ( $exhibitions_query->have_posts() ) {
                while ( $exhibitions_query->have_posts() ) {
                    $exhibitions_query->the_post(); ?>

                <?php get_template_part( 'template-parts/content', get_post_type() ); ?>

            <?php  }
            } else { ?>
                
                No exhibitions

            <?php }

            wp_reset_postdata();

            ?>

        </section>

        <!-- Events Section -->
        <section class="events">
            
            <div class="upcoming-events">
                <h2><?php _e( 'Upcoming Events', 'rebuild-foundation' ); ?></h2>

            </div>

            <div class="past-events">
                <h2><?php _e( 'Past Events', 'rebuild-foundation' ); ?></h2>
            </div>

        </section>

        <!-- Blog Section - Only show if 'show_blog_posts' === 'on' -->
        <?php if( $custom_fields['category'] && 'on' === $custom_fields['show_blog_posts'] ) { ?>

        <section class="posts">
            
            <h2><?php _e( 'Recent Posts', 'rebuild-foundation' ); ?></h2>

            <?php 
            // WP_Query arguments
            $sites_args = array (
                'post_type' => 'post',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'rebuild_sites_category',
                        'field'    => 'slug',
                        'terms'    => $custom_fields['category']
                    ),
                ),
            );

            // The Query
            $sites_query = new WP_Query( $sites_args );

            // The Loop
            if ( $sites_query->have_posts() ) {
                while ( $sites_query->have_posts() ) {
                    $sites_query->the_post();

                    get_template_part( 'template-parts/content', 'excerpt' );
                }
            } else {

                get_template_part( 'template-parts/content', 'none' );

            }

            // Restore original Post Data
            wp_reset_postdata();

            ?>

        </section>

        <?php } else {

            // Write an error

        } ?>
        
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

