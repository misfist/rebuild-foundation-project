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

/**
 * Custom Fields
 * Add custom fields for custom post types
 * Relies on Advanced Custom Fields & Advanced Custom Fields: Date and Time Picker
 * @return
 */

if( function_exists( 'acf_add_local_field_group' ) ) {

    /*
     * Location Fields
     *
     */

    acf_add_local_field_group( array(
        'key' => 'group_5618bc8728ddf',
        'title' => 'Location Details',
        'fields' => array(
            array(
                'key' => 'field_5618bd699a558',
                'label' => 'Location Address',
                'name' => 'location_address',
                'type' => 'google_map',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
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
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'location',
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

    /*
     * Site Fields
     *
     */

    acf_add_local_field_group( array(
        'key' => 'group_5622c317754a9',
        'title' => 'Site Association',
        'fields' => array(
            array(
                'key' => 'field_5622c31ffc630',
                'label' => 'Site',
                'name' => 'site',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'site_category',
                'field_type' => 'select',
                'allow_null' => 1,
                'add_term' => 0,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'object',
                'multiple' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'site',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'event',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'exhibition',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(
            0 => 'custom_fields',
            1 => 'discussion',
            2 => 'comments',
            3 => 'revisions',
            4 => 'format',
            5 => 'page_attributes',
            6 => 'send-trackbacks',
        ),
        'active' => 1,
        'description' => '',
    ));

    /*
     * Event Fields
     *
     */

    acf_add_local_field_group( array(
        'key' => 'group_561990787d10e',
        'title' => 'Event Details',
        'fields' => array(
            array(
                'key' => 'field_5622bc84ec363',
                'label' => 'Event Details',
                'name' => 'event_details',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'left',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_5622bbc5e9502',
                'label' => 'Location',
                'name' => 'location',
                'type' => 'post_object',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'location',
                ),
                'taxonomy' => array(
                ),
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'object',
                'ui' => 1,
            ),
            array(
                'key' => 'field_561990c3168c8',
                'label' => 'Start Date',
                'name' => 'start_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'Ymd',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_561992069e9d6',
                'label' => 'Start Time',
                'name' => 'start_time',
                'type' => 'date_time_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
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
            array(
                'key' => 'field_561990c316900',
                'label' => 'End Date',
                'name' => 'end_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'Ymd',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_5619924eab9d2',
                'label' => 'End Time',
                'name' => 'end_time',
                'type' => 'date_time_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
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
            array(
                'key' => 'field_5622be0d53ba3',
                'label' => 'Gallery',
                'name' => 'gallery',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'left',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_5622bddadc3ee',
                'label' => 'Gallery',
                'name' => 'post_gallery',
                'type' => 'gallery',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
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
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'event',
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

    /*
     * Exhibition Fields
     *
     */

    acf_add_local_field_group( array(
        'key' => 'group_5622bb53e29c8',
        'title' => 'Exhibition Details',
        'fields' => array(
            array(
                'key' => 'field_5622c045c08d4',
                'label' => 'Schedule',
                'name' => 'schedule',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'left',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_5622c22243df6',
                'label' => 'Exhibition Scope',
                'name' => 'exhibition_scope',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'exhibition_category',
                'field_type' => 'select',
                'allow_null' => 0,
                'add_term' => 0,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
                'multiple' => 0,
            ),
            array(
                'key' => 'field_56195d8bd568c',
                'label' => 'Exhibition Scheduled',
                'name' => 'exhibition_scheduled',
                'type' => 'true_false',
                'instructions' => 'If specific start and end dates aren\'t yet scheduled, uncheck this box to enter ad hoc month and year.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array(
                'key' => 'field_5618bfcb85f93',
                'label' => 'Start Date',
                'name' => 'start_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'm/d/Y',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_5618bffc85f94',
                'label' => 'End Date',
                'name' => 'end_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_56195e25d568d',
                'label' => 'Ad Hoc Date',
                'name' => 'ad_hoc_date',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_56195d8bd568c',
                            'operator' => '!=',
                            'value' => '1',
                        ),
                    ),
                ),
                'wrapper' => array(
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
            array(
                'key' => 'field_5622d1b29e216',
                'label' => 'Location',
                'name' => 'location',
                'type' => 'post_object',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'location',
                ),
                'taxonomy' => array(
                ),
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'object',
                'ui' => 1,
            ),
            array(
                'key' => 'field_5622c1113b45d',
                'label' => 'Hours',
                'name' => 'hours',
                'type' => 'textarea',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'new_lines' => 'wpautop',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'field_5622c187c6213',
                'label' => 'Gallery',
                'name' => 'gallery',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'left',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_5622c194c6214',
                'label' => 'Gallery',
                'name' => 'post_gallery',
                'type' => 'gallery',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
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
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'exhibition',
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

    /*
     * Site Fields
     *
     */

    acf_add_local_field_group( array(
        'key' => 'group_5618c1002e81e',
        'title' => 'Site Details',
        'fields' => array(
            array(
                'key' => 'field_5622bf0abc7dc',
                'label' => 'Site Details',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'left',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_56195b586d2a9',
                'label' => 'Show Blog Posts',
                'name' => 'show_blog_posts',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array(
                'key' => 'field_5618c10cd4d6c',
                'label' => 'Short Name',
                'name' => 'short_name',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
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
            array(
                'key' => 'field_5622bf22bc7dd',
                'label' => 'Location',
                'name' => 'location',
                'type' => 'post_object',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'location',
                ),
                'taxonomy' => array(
                ),
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'object',
                'ui' => 1,
            ),
            array(
                'key' => 'field_5622bf6d1a674',
                'label' => 'Hours',
                'name' => 'hours',
                'type' => 'textarea',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => 4,
                'new_lines' => 'wpautop',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'field_5622bfa35f6f9',
                'label' => 'Gallery',
                'name' => 'gallery',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'left',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_5622bfb55f6fa',
                'label' => 'Gallery',
                'name' => 'post_gallery',
                'type' => 'gallery',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
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
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'site',
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

    /*
     * Staff CPT Fields
     *
     */

    acf_add_local_field_group( array(
        'key' => 'group_564bb18ed063a',
        'title' => 'Staff Details',
        'fields' => array(
            array(
                'key' => 'field_564bb1ab499d2',
                'label' => 'Title',
                'name' => 'staff_title',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array(
                'key' => 'field_564cebe2ebbd4',
                'label' => 'Email',
                'name' => 'staff_email',
                'type' => 'email',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'staff@rebuild-foundation.org',
                'prepend' => 'mailto:',
                'append' => '',
            ),
            array(
                'key' => 'field_564d10bff09e6',
                'label' => 'Staff Group',
                'name' => 'staff_group',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'staff_category',
                'field_type' => 'checkbox',
                'allow_null' => 0,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'object',
                'multiple' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'staff',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'acf_after_title',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array (
            0 => 'permalink',
            1 => 'excerpt',
            2 => 'custom_fields',
            3 => 'discussion',
            4 => 'comments',
            5 => 'slug',
            6 => 'author',
            7 => 'format',
            8 => 'page_attributes',
            9 => 'categories',
            10 => 'tags',
            11 => 'send-trackbacks',
        ),
        'active' => 1,
        'description' => '',
    ));


}


/**
 * Year and Month Query Vars
 * Add `year` and `month` query_var for events
 * @return
 */

if(! function_exists( 'rebuild_register_query_vars' ) ) {

    function rebuild_register_query_vars() {

        global $wp;

        $wp->add_query_var( 'start_date' );
        $wp->add_query_var( 'end_date' );
        $wp->add_query_var( 'event_year' );
        $wp->add_query_var( 'event_month' );

    }

    add_filter( 'init', 'rebuild_register_query_vars' );

}


?>