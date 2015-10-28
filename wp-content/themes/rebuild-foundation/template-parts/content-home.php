<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package RebuildFoundation
 */

?>

<?php 
$fields = get_fields();

if( $fields ) : 

    foreach( $fields as $field_name => $value ) :
        // get_field_object( $field_name, $post_id, $options )
        // - $value has already been loaded for us, no point to load it again in the get_field_object function
        $field = get_field_object( $field_name, false, array( 'load_value' => false ) ); ?>

        <section id="<?php echo $field_name; ?>">
			<?php echo $value; ?>  
		</section>
    
    <?php endforeach; ?>

<?php endif; ?>
