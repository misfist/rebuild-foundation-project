<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package RebuildFoundation
 */


?>

<div id="secondary" class="widget-area" role="complementary">
    <nav id="site-navigation" class="main-navigation" role="navigation">
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'rebuild-foundation' ); ?></button>
        <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
    </nav><!-- #site-navigation -->

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) {
        dynamic_sidebar( 'sidebar-1' ); 
    }
    ?>
</div><!-- #secondary -->
