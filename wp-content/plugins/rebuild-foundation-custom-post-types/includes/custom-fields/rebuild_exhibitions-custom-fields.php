<?php 
/*
 * Custom fields for Rebuild Foundation Custom Post Types `rebuild_exhibitions`
 * Author: Pea
 * Author URI: http://misfist.com
 * Field generator: http://hasinhayder.github.io/cmb2-metabox-generator/
 */

add_action( 'cmb2_init', 'rebuild_foundation_exhibitions_add_metabox' );

function rebuild_foundation_exhibitions_add_metabox() {

    $prefix = '_rebuild_exhibition_';

    $cmb = new_cmb2_box( array(
        'id'           => $prefix . 'rebuild_exhibition_info',
        'title'        => __( 'Exhibition Information', 'rebuild-foundation-cpt' ),
        'object_types' => array( 'rebuild_exhibitions' ),
        'context'      => 'normal',
        'priority'     => 'default',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Site Category', 'rebuild-foundation-cpt' ),
        'id' => $prefix . 'site_category',
        'type' => 'taxonomy_select',
        'taxonomy' => 'rebuild_sites_category',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Gallery', 'rebuild-foundation-cpt' ),
        'id' => $prefix . 'gallery',
        'type' => 'file_list',
        'preview_size' => array( 150, 150 ),
        'options' => array(
            'add_upload_files_text' => 'Add or Upload Image',
            'file_text' => 'Image:',
        )
    ) );

    $cmb->add_field( array(
        'name' => __( 'Address', 'rebuild-foundation-cpt' ),
        'id' => $prefix . 'location',
        'type' => 'address',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Hours', 'rebuild-foundation-cpt' ),
        'id' => $prefix . 'hours',
        'type' => 'textarea_small',
    ) );
    $cmb->add_field( array(
        'name'  => __( 'Date Range', 'rebuild-foundation-cpt' ),
        'desc' => __( 'Enter Exhibition Dates', 'rebuild-foundation-cpt' ),
        'id' => $prefix . 'date_range',
        'type' => 'date_range',
    ) );

}

/**
 * Only return default value if we don't have a post ID (in the 'post' query variable)
 *
 * @param  bool  $default On/Off (true/false)
 * @return mixed          Returns true or '', the blank default
 */
// function cmb2_set_checkbox_default_for_new_post( $default ) {
//     return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
// }

/**
 * Return array of all our custom fields
 *
 * @param  current post
 * @return array
 */

if(! function_exists( 'rebuild_exhibition_fields' ) ) {

    function rebuild_exhibition_fields( ) {

        global $post;
        $post_id = $post->ID;
        $prefix = '_rebuild_exhibition_';
        $rebuild_custom_fields = [];

        $rebuild_custom_fields['id'] = $post_id;

        $location = get_post_meta( $post_id, $prefix . 'location', true ) ? get_post_meta( $post_id, $prefix . 'location', true ) : '';

        $rebuild_custom_fields['street'] = isset( $location["address-1"] ) ? $location["address-1"] : '';

        $rebuild_custom_fields['street2'] = isset( $location["address-2"] ) ? $location["address-2"] : '';
        
        $rebuild_custom_fields['city'] = isset( $location["city"] ) ? $location["city"] : '';

        $rebuild_custom_fields['state'] = isset( $location["state"] ) ? $location["state"] : '';

        $rebuild_custom_fields['zip'] = isset( $location["zip"] ) ? $location["zip"] : '';

        $rebuild_custom_fields['hours'] = get_post_meta( $post_id, $prefix . 'hours', true ) ? wpautop( get_post_meta( $post_id, $prefix . 'hours', true ) ) : '';

        $rebuild_custom_fields['gallery'] = get_post_meta( $post_id, $prefix . 'gallery', true ) ? get_post_meta( $post_id, $prefix . 'gallery', true ) : '';

        $rebuild_custom_fields['category'] = wp_get_post_terms( $post_id, 'rebuild_sites_category', array( "fields" => "slugs" ) ) ? wp_get_post_terms( $post_id, 'rebuild_sites_category', array( "fields" => "slugs" ) )[0] : '';

        $rebuild_custom_fields['date_range'] = get_post_meta( $post_id, $prefix . 'date_range', true ) ? get_post_meta( $post_id, $prefix . 'date_range', true ) : '';

        return $rebuild_custom_fields;
    }
}


?>