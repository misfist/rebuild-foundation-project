<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php 
    $query = $wp_query->query;
    $post_type = $query['post_type'];
    $post_obj = get_post_type_object( $post_type );
    $post_type_label = $post_obj->labels->name;
?>

<article id="404" data-year="" data-month=""  <?php post_class( $post_type ); ?>>
	
	<div class="no-posts"><?php _e( 'No ' . $post_type_label, 'rebuild-foundation'); ?></div>
    
</article><!-- #post-## -->
