<?php

/**
 * Rebuild Foundation Custom Columns
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

if(! function_exists( 'rebuild_custom_columns' ) ) {

    if ( function_exists( 'ac_register_columns' ) ) {

        function rebuild_custom_columns() {

            ac_register_columns( 'post', array(
                'column-postid' => array(
                    'column-name' => 'column-postid',
                    'type' => 'column-postid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on'
                ),
                'title' => array(
                    'column-name' => 'title',
                    'type' => 'title',
                    'clone' => '',
                    'label' => 'Title',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-featured_image' => array(
                    'column-name' => 'column-featured_image',
                    'type' => 'column-featured_image',
                    'clone' => '',
                    'label' => 'Featured Image',
                    'width' => '',
                    'width_unit' => '%',
                    'image_size_w' => '80',
                    'image_size_h' => '80',
                    'filter' => 'off',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-taxonomy' => array(
                    'column-name' => 'column-taxonomy',
                    'type' => 'column-taxonomy',
                    'clone' => '',
                    'label' => 'Taxonomy',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'rebuild_site_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'categories' => array(
                    'column-name' => 'categories',
                    'type' => 'categories',
                    'clone' => '',
                    'label' => 'Categories',
                    'width' => '',
                    'width_unit' => '%',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'tags' => array(
                    'column-name' => 'tags',
                    'type' => 'tags',
                    'clone' => '',
                    'label' => 'Tags',
                    'width' => '',
                    'width_unit' => '%',
                    'filter' => 'off',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'date' => array(
                    'column-name' => 'date',
                    'type' => 'date',
                    'clone' => '',
                    'label' => 'Date',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off',
                    'date_save_format' => ''
                ),
                'author' => array(
                    'column-name' => 'author',
                    'type' => 'author',
                    'clone' => '',
                    'label' => 'Author',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'off'
                )
            ) );

            ac_register_columns( 'page', array(
                'column-postid' => array(
                    'column-name' => 'column-postid',
                    'type' => 'column-postid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on'
                ),
                'title' => array(
                    'column-name' => 'title',
                    'type' => 'title',
                    'clone' => '',
                    'label' => 'Title',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'author' => array(
                    'column-name' => 'author',
                    'type' => 'author',
                    'clone' => '',
                    'label' => 'Author',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'date' => array(
                    'column-name' => 'date',
                    'type' => 'date',
                    'clone' => '',
                    'label' => 'Date',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off',
                    'date_save_format' => ''
                )
            ) );

            ac_register_columns( 'rebuild_site', array(
                'column-postid' => array(
                    'column-name' => 'column-postid',
                    'type' => 'column-postid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on'
                ),
                'title' => array(
                    'column-name' => 'title',
                    'type' => 'title',
                    'clone' => '',
                    'label' => 'Title',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-featured_image' => array(
                    'column-name' => 'column-featured_image',
                    'type' => 'column-featured_image',
                    'clone' => '',
                    'label' => 'Featured Image',
                    'width' => '',
                    'width_unit' => '%',
                    'image_size_w' => '80',
                    'image_size_h' => '80',
                    'filter' => 'off',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-acf_field' => array(
                    'column-name' => 'column-acf_field',
                    'type' => 'column-acf_field',
                    'clone' => '',
                    'label' => 'Location',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_5622bf22bc7dd',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-acf_field-1' => array(
                    'column-name' => 'column-acf_field-1',
                    'type' => 'column-acf_field',
                    'clone' => '1',
                    'label' => 'Hours',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_5622bf6d1a674',
                    'excerpt_length' => '15',
                    'sort' => 'on',
                    'edit' => 'off'
                )
            ) );

            ac_register_columns( 'rebuild_event', array(
                'column-postid' => array(
                    'column-name' => 'column-postid',
                    'type' => 'column-postid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on'
                ),
                'title' => array(
                    'column-name' => 'title',
                    'type' => 'title',
                    'clone' => '',
                    'label' => 'Title',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-featured_image' => array(
                    'column-name' => 'column-featured_image',
                    'type' => 'column-featured_image',
                    'clone' => '',
                    'label' => 'Featured Image',
                    'width' => '',
                    'width_unit' => '%',
                    'image_size' => 'cpac-custom',
                    'image_size_w' => '80',
                    'image_size_h' => '80',
                    'filter' => 'off',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-taxonomy-2' => array(
                    'column-name' => 'column-taxonomy-2',
                    'type' => 'column-taxonomy',
                    'clone' => '2',
                    'label' => 'Site',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'rebuild_site_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'column-acf_field' => array(
                    'column-name' => 'column-acf_field',
                    'type' => 'column-acf_field',
                    'clone' => '',
                    'label' => 'Start Date',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_5618bfcb85f93',
                    'filter' => 'on',
                    'filter_type' => 'monthly',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-acf_field-1' => array(
                    'column-name' => 'column-acf_field-1',
                    'type' => 'column-acf_field',
                    'clone' => '1',
                    'label' => 'End Date',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_561990c316900',
                    'filter' => 'on',
                    'filter_type' => 'monthly',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-acf_field-2' => array(
                    'column-name' => 'column-acf_field-2',
                    'type' => 'column-acf_field',
                    'clone' => '2',
                    'label' => 'Location',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_5622bbc5e9502',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-taxonomy' => array(
                    'column-name' => 'column-taxonomy',
                    'type' => 'column-taxonomy',
                    'clone' => '',
                    'label' => 'Categories',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'rebuild_event_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'column-taxonomy-1' => array(
                    'column-name' => 'column-taxonomy-1',
                    'type' => 'column-taxonomy',
                    'clone' => '1',
                    'label' => 'Tags',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'rebuild_event_tag',
                    'filter' => 'off',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                )
            ) );

            ac_register_columns( 'rebuild_exhibition', array(
                'column-postid' => array(
                    'column-name' => 'column-postid',
                    'type' => 'column-postid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on'
                ),
                'title' => array(
                    'column-name' => 'title',
                    'type' => 'title',
                    'clone' => '',
                    'label' => 'Title',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-featured_image' => array(
                    'column-name' => 'column-featured_image',
                    'type' => 'column-featured_image',
                    'clone' => '',
                    'label' => 'Featured Image',
                    'width' => '',
                    'width_unit' => '%',
                    'image_size' => 'cpac-custom',
                    'image_size_w' => '50',
                    'image_size_h' => '50',
                    'filter' => 'off',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-taxonomy' => array(
                    'column-name' => 'column-taxonomy',
                    'type' => 'column-taxonomy',
                    'clone' => '',
                    'label' => 'Site',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'rebuild_site_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'column-taxonomy-1' => array(
                    'column-name' => 'column-taxonomy-1',
                    'type' => 'column-taxonomy',
                    'clone' => '1',
                    'label' => 'Scope',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'rebuild_exhibition_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'column-acf_field' => array(
                    'column-name' => 'column-acf_field',
                    'type' => 'column-acf_field',
                    'clone' => '',
                    'label' => 'Start Date',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_561990c3168c8',
                    'filter' => 'on',
                    'filter_type' => 'monthly',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-acf_field-1' => array(
                    'column-name' => 'column-acf_field-1',
                    'type' => 'column-acf_field',
                    'clone' => '1',
                    'label' => 'End Date',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_561990c316900',
                    'filter' => 'on',
                    'filter_type' => 'monthly',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-meta-2' => array(
                    'column-name' => 'column-meta-2',
                    'type' => 'column-meta',
                    'clone' => '2',
                    'label' => 'Ad Hoc Date',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'ad_hoc_date',
                    'field_type' => '',
                    'before' => '',
                    'after' => '',
                    'filter' => 'off',
                    'sort' => 'on',
                    'date_format' => 'm/d/Y'
                ),
                'column-acf_field-2' => array(
                    'column-name' => 'column-acf_field-2',
                    'type' => 'column-acf_field',
                    'clone' => '2',
                    'label' => 'Location',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_5622d1b29e216',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off'
                )
            ) );

            ac_register_columns( 'rebuild_location', array(
                'column-postid' => array(
                    'column-name' => 'column-postid',
                    'type' => 'column-postid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on'
                ),
                'title' => array(
                    'column-name' => 'title',
                    'type' => 'title',
                    'clone' => '',
                    'label' => 'Title',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'off'
                ),
                'column-meta' => array(
                    'column-name' => 'column-meta',
                    'type' => 'column-meta',
                    'clone' => '',
                    'label' => 'Address',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'location_address',
                    'field_type' => '',
                    'before' => '',
                    'after' => '',
                    'filter' => 'off',
                    'sort' => 'on',
                    'date_format' => 'm/d/Y'
                )
            ) );

            ac_register_columns( 'wp-taxonomy_category', array(
                'column-termid' => array(
                    'column-name' => 'column-termid',
                    'type' => 'column-termid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%'
                ),
                'name' => array(
                    'column-name' => 'name',
                    'type' => 'name',
                    'clone' => '',
                    'label' => 'Name',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'description' => array(
                    'column-name' => 'description',
                    'type' => 'description',
                    'clone' => '',
                    'label' => 'Description',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'slug' => array(
                    'column-name' => 'slug',
                    'type' => 'slug',
                    'clone' => '',
                    'label' => 'Slug',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'posts' => array(
                    'column-name' => 'posts',
                    'type' => 'posts',
                    'clone' => '',
                    'label' => 'Count',
                    'width' => '',
                    'width_unit' => '%'
                )
            ) );

            ac_register_columns( 'wp-taxonomy_rebuild_site_category', array(
                'column-termid' => array(
                    'column-name' => 'column-termid',
                    'type' => 'column-termid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%'
                ),
                'name' => array(
                    'column-name' => 'name',
                    'type' => 'name',
                    'clone' => '',
                    'label' => 'Name',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'description' => array(
                    'column-name' => 'description',
                    'type' => 'description',
                    'clone' => '',
                    'label' => 'Description',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'slug' => array(
                    'column-name' => 'slug',
                    'type' => 'slug',
                    'clone' => '',
                    'label' => 'Slug',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'posts' => array(
                    'column-name' => 'posts',
                    'type' => 'posts',
                    'clone' => '',
                    'label' => 'Count',
                    'width' => '',
                    'width_unit' => '%'
                )
            ) );

            ac_register_columns( 'wp-taxonomy_rebuild_event_category', array(
                'column-termid' => array(
                    'column-name' => 'column-termid',
                    'type' => 'column-termid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%'
                ),
                'name' => array(
                    'column-name' => 'name',
                    'type' => 'name',
                    'clone' => '',
                    'label' => 'Name',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'description' => array(
                    'column-name' => 'description',
                    'type' => 'description',
                    'clone' => '',
                    'label' => 'Description',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'slug' => array(
                    'column-name' => 'slug',
                    'type' => 'slug',
                    'clone' => '',
                    'label' => 'Slug',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'posts' => array(
                    'column-name' => 'posts',
                    'type' => 'posts',
                    'clone' => '',
                    'label' => 'Count',
                    'width' => '',
                    'width_unit' => '%'
                )
            ) );

            ac_register_columns( 'wp-taxonomy_rebuild_event_tag', array(
                'column-termid' => array(
                    'column-name' => 'column-termid',
                    'type' => 'column-termid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%'
                ),
                'name' => array(
                    'column-name' => 'name',
                    'type' => 'name',
                    'clone' => '',
                    'label' => 'Name',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'description' => array(
                    'column-name' => 'description',
                    'type' => 'description',
                    'clone' => '',
                    'label' => 'Description',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'slug' => array(
                    'column-name' => 'slug',
                    'type' => 'slug',
                    'clone' => '',
                    'label' => 'Slug',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'posts' => array(
                    'column-name' => 'posts',
                    'type' => 'posts',
                    'clone' => '',
                    'label' => 'Count',
                    'width' => '',
                    'width_unit' => '%'
                )
            ) );

            ac_register_columns( 'wp-taxonomy_rebuild_exhibition_category', array(
                'column-termid' => array(
                    'column-name' => 'column-termid',
                    'type' => 'column-termid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%'
                ),
                'name' => array(
                    'column-name' => 'name',
                    'type' => 'name',
                    'clone' => '',
                    'label' => 'Name',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'description' => array(
                    'column-name' => 'description',
                    'type' => 'description',
                    'clone' => '',
                    'label' => 'Description',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'slug' => array(
                    'column-name' => 'slug',
                    'type' => 'slug',
                    'clone' => '',
                    'label' => 'Slug',
                    'width' => '',
                    'width_unit' => '%',
                    'edit' => 'off'
                ),
                'posts' => array(
                    'column-name' => 'posts',
                    'type' => 'posts',
                    'clone' => '',
                    'label' => 'Count',
                    'width' => '',
                    'width_unit' => '%'
                )
            ) );

        }

        add_action( 'init', 'rebuild_custom_columns' );

    }


}


?>