<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        
        <h2 class="site-name"><?php rebuild_get_site_category_content(); ?></h2>

        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

        <?php $date = get_field( 'start_date' ); ?>
        <h3 class="event-date"><time datetime="<?php echo ( $date ) ? date( 'Y-m-d', strtotime( $date ) ) : ''; ?>"><?php echo ( $date ) ? date( 'M d', strtotime( $date ) ) : ''; ?></time></h3>

    </header><!-- .entry-header -->

    <div class="entry-content">

        <?php if( has_post_thumbnail() ) { ?>

        <div class="gallery single-image">

            <div class="site-image"><?php the_post_thumbnail( 'full' ); ?></div>
            
        </div>

        <?php }?>

        <div id="details">
            <div class="event-date"><time datetime="<?php echo ( $date ) ? date( 'Y-m-d', strtotime( $date ) ) : ''; ?>"><?php echo ( $date ) ? date( 'l, F d, Y', strtotime( $date ) ) : ''; ?></time></div>

            <div class="event-time">
                <?php $start_time = get_field( 'start_time' ); ?>
                <?php $end_time = get_field( 'end_time' ); ?>
                <time class="start-time"><?php echo ( $start_time ) ? date( 'g:ia', strtotime( $start_time ) ) : ''; ?></time>
                <time class="end-time"><?php echo ( $end_time ) ? date( 'g:ia', strtotime( $end_time ) ) : ''; ?></time>
            </div>


            <div class="event-export">
                <a href="" class="google-calendar">Google</a>
                <a href="" class="ical">iCal</a>
            </div>

            <div class="entry-meta location">
                <?php ( function_exists( 'rebuild_formatted_address' ) ) ? rebuild_formatted_address() : ''; ?>
            </div>

            <div class="entry-meta">
                
            </div>

            <div class="description">
                <?php the_content(); ?>
            </div>

        </div>


    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php
            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'rebuild-foundation' ),
                'after'  => '</div>',
            ) );
        ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-## -->
