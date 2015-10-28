<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'event' ); ?>>
    <header class="entry-header">
        
        <h2 class="site-name"><?php rebuild_get_site_category_content(); ?></h2>

        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

        <?php $date = get_field( 'start_date' ); ?>
        <h3 class="event-date"><time datetime="<?php echo ( $date ) ? date( 'Y-m-d', strtotime( $date ) ) : ''; ?>"><?php echo ( $date ) ? date( 'M d', strtotime( $date ) ) : ''; ?></time></h3>

    </header><!-- .entry-header -->

    <div class="entry-content">

        <?php if( has_post_thumbnail( ) ) :?>

            <figure class="entry-thumbnail">
                <?php the_post_thumbnail(''); ?>
                <?php if ( $caption = get_post( get_post_thumbnail_id() )->post_excerpt ) : ?>
                    <figcaption class="caption"><?php echo $caption; ?></figcaption>
                <?php endif; ?>
            </figure>

        <?php endif; ?>

        <div id="details">
            <div class="event-date"><time datetime="<?php echo ( $date ) ? date( 'Y-m-d', strtotime( $date ) ) : ''; ?>"><?php echo ( $date ) ? date( 'l, F d, Y', strtotime( $date ) ) : ''; ?></time></div>

            <div class="event-time">
                <?php $start_time = get_field( 'start_time' ); ?>
                <?php $end_time = get_field( 'end_time' ); ?>
                <time class="start-time"><?php echo ( $start_time ) ? date( 'g:ia', strtotime( $start_time ) ) : ''; ?></time>
                <time class="end-time"><?php echo ( $end_time ) ? date( 'g:ia', strtotime( $end_time ) ) : ''; ?></time>
            </div>

            <div class="event-export">
                <a href="ical" class="ical"><?php _e( '.ical', 'rebuild-foundation' ) ?></a> 
                <a href="<?php echo generate_calendar_button(); ?>" class="google-calendar" target="_blank"><?php _e( 'Google Calendar', 'rebuild-foundation' ) ?></a>
            </div>

            <div class="entry-meta location">
                <?php ( function_exists( 'rebuild_formatted_address' ) ) ? rebuild_formatted_address() : ''; ?>
            </div>

            <div class="entry-meta tags">
                <?php ( function_exists( 'rebuild_foundation_entry_footer' ) ) ? rebuild_foundation_entry_footer() : '' ; ?>
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
