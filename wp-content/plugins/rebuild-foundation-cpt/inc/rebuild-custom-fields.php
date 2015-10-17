<?php

/**
 * Rebuild Foundation Site Custom Post Type
 *
 * @author    Pea
 * @license   GPL-2.0+
 * @link      http://misfist.com
 * @since     1.0.0
 * @package   Rebuild_Foundation_Custom_Post_Types
 */

if( function_exists( 'acf_add_local_field_group' ) ) {

    acf_add_local_field_group( array(
        'key' => 'group_561990787d10e',
        'title' => 'Event Details',
        'fields' => array (
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));

    acf_add_local_field_group(array (
        'key' => 'group_56212b0939f87',
        'title' => 'Gallery',
        'fields' => array (
            array (
                'key' => 'field_56212b0f7e1ab',
                'label' => 'Gallery',
                'name' => 'post_gallery',
                'type' => 'gallery',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'min' => '',
                'max' => '',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_site',
                ),
            ),
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_event',
                ),
            ),
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_exhibition',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));

    acf_add_local_field_group(array (
        'key' => 'group_5618c1002e81e',
        'title' => 'Site Details',
        'fields' => array (
            array (
                'key' => 'field_56195b586d2a9',
                'label' => 'Show Blog Posts',
                'name' => 'show_blog_posts',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array (
                'key' => 'field_5618c10cd4d6c',
                'label' => 'Short Name',
                'name' => 'short_name',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'Enter short name (optional)',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_site',
                ),
            ),
            array (
                array (
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'rebuild_site_category',
                ),
            ),
        ),
        'menu_order' => 10,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));

    acf_add_local_field_group(array (
        'key' => 'group_5618bc8728ddf',
        'title' => 'Location Fields',
        'fields' => array (
            array (
                'key' => 'field_5618bd699a558',
                'label' => 'Location Address',
                'name' => 'location_address',
                'type' => 'google_map',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'center_lat' => '41.771689',
                'center_lng' => '-87.5888277',
                'zoom' => '',
                'height' => '',
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_location',
                ),
            ),
        ),
        'menu_order' => 30,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));

    acf_add_local_field_group(array (
        'key' => 'group_561990c313a5f',
        'title' => 'Date & Time',
        'fields' => array (
            array (
                'key' => 'field_561990c3168c8',
                'label' => 'Start Date',
                'name' => 'start_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'Ymd',
                'first_day' => 1,
            ),
            array (
                'key' => 'field_561990c316900',
                'label' => 'End Date',
                'name' => 'end_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'Ymd',
                'first_day' => 1,
            ),
            array (
                'key' => 'field_561992069e9d6',
                'label' => 'Start Time',
                'name' => 'start_time',
                'type' => 'date_time_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'show_date' => 'false',
                'date_format' => 'm/d/y',
                'time_format' => 'h:mm tt',
                'show_week_number' => 'false',
                'picker' => 'select',
                'save_as_timestamp' => 'true',
                'get_as_timestamp' => 'false',
            ),
            array (
                'key' => 'field_5619924eab9d2',
                'label' => 'End Time',
                'name' => 'end_time',
                'type' => 'date_time_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'show_date' => 'false',
                'date_format' => 'm/d/y',
                'time_format' => 'h:mm tt',
                'show_week_number' => 'false',
                'picker' => 'select',
                'save_as_timestamp' => 'true',
                'get_as_timestamp' => 'false',
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_event',
                ),
            ),
        ),
        'menu_order' => 40,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));

    acf_add_local_field_group(array (
        'key' => 'group_5618bfc1421de',
        'title' => 'Time Period',
        'fields' => array (
            array (
                'key' => 'field_56195d8bd568c',
                'label' => 'Exhibition Scheduled',
                'name' => 'exhibition_scheduled',
                'type' => 'true_false',
                'instructions' => 'If specific start and end dates aren\'t yet scheduled, uncheck this box to enter ad hoc month and year.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array (
                'key' => 'field_5618bfcb85f93',
                'label' => 'Start Date',
                'name' => 'start_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_56195d8bd568c',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'm/d/Y',
                'first_day' => 1,
            ),
            array (
                'key' => 'field_5618bffc85f94',
                'label' => 'End Date',
                'name' => 'end_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_56195d8bd568c',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
            ),
            array (
                'key' => 'field_56195e25d568d',
                'label' => 'Ad Hoc Date',
                'name' => 'ad_hoc_date',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_56195d8bd568c',
                            'operator' => '!=',
                            'value' => '1',
                        ),
                    ),
                ),
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'e.g. February 2016',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_exhibition',
                ),
            ),
        ),
        'menu_order' => 40,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));

    acf_add_local_field_group(array (
        'key' => 'group_5618bed820d71',
        'title' => 'Hours',
        'fields' => array (
            array (
                'key' => 'field_5618bf490d7c4',
                'label' => 'Hours',
                'name' => 'hours',
                'type' => 'textarea',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 3,
                'new_lines' => 'wpautop',
                'readonly' => 0,
                'disabled' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_site',
                ),
            ),
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'rebuild_exhibition',
                ),
            ),
        ),
        'menu_order' => 50,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    ));

}

?>