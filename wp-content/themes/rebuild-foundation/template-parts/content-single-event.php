<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php global $EM_Event; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">

        <?php the_title( '<div style="display:none">', '</div>' ); ?>

        <h2 class="site-name"><?php echo $EM_Event->output( '#_SITENAME' ); ?></h2>

        <h1 class="event-title"><?php echo $EM_Event->output( '#_EVENTNAME' ); ?></h1>

        <h3 class="event-date"><time datetime="2011-01-12"><?php echo $EM_Event->output( '#M #j' ); ?></time></h3>

    </header><!-- .entry-header -->

    <div class="entry-content">

        <?php if( has_post_thumbnail() ) { ?>

        <div class="gallery single-image">

            <div class="site-image"><?php the_post_thumbnail( 'full' ); ?></div>
            
        </div>

        <?php }?>

        <div id="details">
            <div class="event-date"><time datetime="<?php echo $EM_Event->output( '#_{Y-m-d}' ); ?>"><?php echo $EM_Event->output( '#_{l, F j, Y}' ); ?></time></div>

            <div class="event-time">
                <time class="start-time"><?php echo $EM_Event->output( '#_EVENTTIMES' ); ?></time>
            </div>

            <div class="event-export">
                <a href="<?php echo $EM_Event->output( '#_EVENTGCALURL' ); ?>" class="google-calendar"><?php _e( 'Google Calendar', 'rebuild-foundation' ); ?></a>
                <a href="<?php echo $EM_Event->output( '#_EVENTICALURL' ); ?>" class="ical"><?php _e( '.ics', 'rebuild-foundation' ); ?></a>
            </div>

            <div class="entry-meta">
                <?php rebuild_foundation_entry_footer(); ?>
            </div>

            <div class="description">
                <?php the_content(); ?>
            </div>

        </div>


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
