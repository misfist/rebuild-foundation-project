<?php 
/*
 * Custom fields for Rebuild Foundation Sites `rebuild_sites`
 * Author: Pea
 * Author URI: http://misfist.com
 * Field generator: http://hasinhayder.github.io/cmb2-metabox-generator/
 */

add_action( 'cmb2_init', 'rebuild_foundation_site_add_metabox' );

function rebuild_foundation_site_add_metabox() {

    $prefix = '_rebuild_site_';

    $cmb = new_cmb2_box( array(
        'id'           => $prefix . 'rebuild_sites_info',
        'title'        => __( 'Site Information', 'rebuild-foundation' ),
        'object_types' => array( 'rebuild_sites' ),
        'context'      => 'normal',
        'priority'     => 'high',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Short Name', 'rebuild-foundation' ),
        'id' => $prefix . 'short_name',
        'type' => 'text_medium',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Blog Posts', 'rebuild-foundation' ),
        'id' => $prefix . 'show_blog_posts',
        'type' => 'checkbox',
        'default' => cmb2_set_checkbox_default_for_new_post( true )
    ) );

    $cmb->add_field( array(
        'name' => __( 'Category', 'rebuild-foundation' ),
        'id' => $prefix . 'category',
        'type' => 'taxonomy_select',
        'taxonomy' => 'rebuild_sites_category',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Gallery', 'rebuild-foundation' ),
        'id' => $prefix . 'gallery',
        'type' => 'file_list',
        'preview_size' => array( 150, 150 ), // Default: array( 50, 50 )
        'options' => array(
            'add_upload_files_text' => 'Add or Upload Image', // default: "Add or Upload Files"
            //'remove_image_text' => 'Replacement', // default: "Remove Image"
            'file_text' => 'Image:', // default: "File:"
            //'file_download_text' => 'Replacement', // default: "Download"
            //'remove_text' => 'Replacement', // default: "Remove"
        )
    ) );

    $cmb->add_field( array(
        'name' => __( 'Address', 'rebuild-foundation' ),
        'id' => $prefix . 'location',
        'type' => 'address',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Hours', 'rebuild-foundation' ),
        'id' => $prefix . 'hours',
        'type' => 'textarea_small',
    ) );

}

/**
 * Only return default value if we don't have a post ID (in the 'post' query variable)
 *
 * @param  bool  $default On/Off (true/false)
 * @return mixed          Returns true or '', the blank default
 */
function cmb2_set_checkbox_default_for_new_post( $default ) {
    return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
}

/**
 * Return array of all our custom fields
 *
 * @param  current post
 * @return array
 */
if(! function_exists( 'rebuild_site_fields' ) ) {

    function rebuild_site_fields( ) {

        global $post;
        $post_id = $post->ID;
        $prefix = '_rebuild_site_';
        $rebuild_custom_fields = [];

        $rebuild_custom_fields['id'] = $post_id;

        $rebuild_custom_fields['short_name'] = get_post_meta( $post_id, $prefix . 'short_name', true ) ? get_post_meta( $post_id, $prefix . 'short_name', true ) : '';

        $location = get_post_meta( $post_id, $prefix . 'location', true ) ? get_post_meta( $post_id, $prefix . 'location', true ) : '';

        $rebuild_custom_fields['street'] = isset( $location["address-1"] ) ? $location["address-1"] : '';

        $rebuild_custom_fields['street2'] = isset( $location["address-2"] ) ? $location["address-2"] : '';
        
        $rebuild_custom_fields['city'] = isset( $location["city"] ) ? $location["city"] : '';

        $rebuild_custom_fields['state'] = isset( $location["state"] ) ? $location["state"] : '';

        $rebuild_custom_fields['zip'] = isset( $location["zip"] ) ? $location["zip"] : '';

        $rebuild_custom_fields['hours'] = get_post_meta( $post_id, $prefix . 'hours', true ) ? wpautop( get_post_meta( $post_id, $prefix . 'hours', true ) ) : '';

        $rebuild_custom_fields['gallery'] = get_post_meta( $post_id, $prefix . 'gallery', true ) ? get_post_meta( $post_id, $prefix . 'gallery', true ) : '';

        $rebuild_custom_fields['category'] = wp_get_post_terms( $post_id, 'rebuild_sites_category', array( "fields" => "slugs" ) ) ? wp_get_post_terms( $post_id, 'rebuild_sites_category', array( "fields" => "slugs" ) )[0] : '';

        $rebuild_custom_fields['show_blog_posts'] = get_post_meta( $post_id, $prefix . 'show_blog_posts', true ) ? get_post_meta( $post_id, $prefix . 'show_blog_posts', true ) : 'off';

        return $rebuild_custom_fields;
    }

}


?>