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

            /*
             * Post Columns
             *
             */

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
                    'label' => 'Featured Images',
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
                    'label' => 'Sites',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'site_category',
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
                    'label' => 'Dates',
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

            /*
             * Page Columns
             *
             */

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

            /*
             * Site Columns
             *
             */

            ac_register_columns( 'site', array(
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
                    'label' => 'Featured Images',
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
                    'label' => 'Locations',
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

            /*
             * Event Columns
             *
             */

            ac_register_columns( 'event', array(
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
                    'label' => 'Featured Images',
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
                    'label' => 'Sites',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'site_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'column-acf_field' => array(
                    'column-name' => 'column-acf_field',
                    'type' => 'column-acf_field',
                    'clone' => '',
                    'label' => 'Start Dates',
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
                    'label' => 'Start Times',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_561992069e9d6',
                    'sort' => 'on'
                ),
                'column-acf_field-2' => array(
                    'column-name' => 'column-acf_field-2',
                    'type' => 'column-acf_field',
                    'clone' => '2',
                    'label' => 'Locations',
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
                    'taxonomy' => 'event_category',
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
                    'taxonomy' => 'event_tag',
                    'filter' => 'off',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                )
            ) );

            /*
             * Exhibition Columns
             *
             */

            ac_register_columns( 'exhibition', array(
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
                    'label' => 'Featured Images',
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
                    'label' => 'Sites',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'site_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'column-taxonomy-1' => array(
                    'column-name' => 'column-taxonomy-1',
                    'type' => 'column-taxonomy',
                    'clone' => '1',
                    'label' => 'Scopes',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'exhibition_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'column-acf_field' => array(
                    'column-name' => 'column-acf_field',
                    'type' => 'column-acf_field',
                    'clone' => '',
                    'label' => 'Start Dates',
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
                    'label' => 'End Dates',
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
                    'label' => 'Ad Hoc Dates',
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
                    'label' => 'Locations',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_5622d1b29e216',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off'
                )
            ) );

            /*
             * Location Columns
             *
             */

            ac_register_columns( 'location', array(
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
                    'label' => 'Addresses',
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

            /*
             * Category Columns
             *
             */

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

            /*
             * Site Category Columns
             *
             */

            ac_register_columns( 'wp-taxonomy_site_category', array(
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

            /*
             * Event Category Columns
             *
             */

            ac_register_columns( 'wp-taxonomy_event_category', array(
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

            /*
             * Event Tag Columns
             *
             */

            ac_register_columns( 'wp-taxonomy_event_tag', array(
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

            /*
             * Exhibition Category Columns
             *
             */

            ac_register_columns( 'wp-taxonomy_exhibition_category', array(
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

            /*
             * Staff Columns
             *
             */

            ac_register_columns( 'staff', array(
                'ssid' => array(
                    'column-name' => 'ssid',
                    'type' => 'ssid',
                    'clone' => '',
                    'label' => 'ID',
                    'width' => '',
                    'width_unit' => '%'
                ),
                'title' => array(
                    'column-name' => 'title',
                    'type' => 'title',
                    'clone' => '',
                    'label' => 'Name',
                    'width' => '',
                    'width_unit' => '%',
                    'sort' => 'on',
                    'edit' => 'on'
                ),
                'column-featured_image' => array(
                    'column-name' => 'column-featured_image',
                    'type' => 'column-featured_image',
                    'clone' => '',
                    'label' => 'Photo',
                    'width' => '',
                    'width_unit' => '%',
                    'image_size' => 'cpac-custom',
                    'image_size_w' => '80',
                    'image_size_h' => '80',
                    'filter' => 'off',
                    'sort' => 'off',
                    'edit' => 'off'
                ),
                'column-taxonomy' => array(
                    'column-name' => 'column-taxonomy',
                    'type' => 'column-taxonomy',
                    'clone' => '',
                    'label' => 'Group',
                    'width' => '',
                    'width_unit' => '%',
                    'taxonomy' => 'staff_category',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off',
                    'enable_term_creation' => 'off'
                ),
                'column-acf_field' => array(
                    'column-name' => 'column-acf_field',
                    'type' => 'column-acf_field',
                    'clone' => '',
                    'label' => 'Title',
                    'width' => '',
                    'width_unit' => '%',
                    'field' => 'field_564bb1ab499d2',
                    'filter' => 'on',
                    'sort' => 'on',
                    'edit' => 'off'
                )
            ) );

        }

        add_action( 'init', 'rebuild_custom_columns' );

    }


}


?>