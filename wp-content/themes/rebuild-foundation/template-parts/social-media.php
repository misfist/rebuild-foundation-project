<?php
/**
 * Template part for displaying social media buttons
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php if ( function_exists( 'sharing_display' ) ) {
    sharing_display( '', true );
}
 
if ( class_exists( 'Jetpack_Likes' ) ) {
    $custom_likes = new Jetpack_Likes;
    echo $custom_likes->post_likes( '' );
} ?>
