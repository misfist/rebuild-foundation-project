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

        <h2 class="site-name">Site</h2>

        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

        <h3 class="event-date"><time datetime="">Date</time></h3>

    </header><!-- .entry-header -->

    <div class="entry-content">

        <?php if( has_post_thumbnail() ) { ?>

        <div class="gallery single-image">

            <div class="site-image"><?php the_post_thumbnail( 'full' ); ?></div>
            
        </div>

        <?php }?>

        <div id="details">
            <div class="event-date"><time datetime="">Date</time></div>

            <div class="event-time">
                <time class="start-time">Time</time>
            </div>

            <div class="event-export">
                <a href="" class="google-calendar">Google</a>
                <a href="" class="ical">iCal</a>
            </div>

            <div class="entry-meta">
                <?php rebuild_foundation_entry_footer(); ?>
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
