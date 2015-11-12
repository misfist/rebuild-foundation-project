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

<article id="no-results" <?php post_class( $post_type ); ?>>
	
	<div class="no-posts"><?php _e( 'No ', 'rebuild-foundation'); ?> <?php echo $post_type_label ?></div>
    
</article><!-- #post-## -->
