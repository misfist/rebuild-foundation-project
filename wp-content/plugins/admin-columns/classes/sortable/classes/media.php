<?php

/**
 * Addon class
 *
 * @since 1.0
 */
class CAC_Sortable_Model_Media extends CAC_Sortable_Model {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct( $storage_model ) {
		parent::__construct( $storage_model );

		// default sortby
		$this->default_orderby = '';

		// handle sorting request
		add_filter( 'request', array( $this, 'handle_sorting_request'), 1 );

		// register sortable headings
		add_filter( "manage_upload_sortable_columns", array( $this, 'add_sortable_headings' ) );

		// add reset button
		add_action( 'restrict_manage_posts', array( $this, 'add_reset_button' ) );
	}

	/**
	 * @see CAC_Sortable_Model::get_sortables()
	 * @since 1.0
	 */
	public function get_sortables() {

		$column_names = array(

			// WP default columns

			// Custom Columns
			'column-alternate_text',
			'column-available_sizes',
			'column-caption',
			'column-dimensions',
			'column-description',
			'column-exif_data',
			'column-file_name',
			'column-file_size',
			'column-height',
			'column-meta',
			'column-mediaid',
			'column-mime_type',
			'column-taxonomy',
			'column-width',

			// ACF Fields
			'column-acf_field',
		);

		return $column_names;
	}

	/**
	 * Admin requests for orderby column
	 *
	 * Only works for WP_Query objects ( such as posts and media )
	 *
	 * @since 1.0
	 *
	 * @param array $vars
	 * @return array Vars
	 */
	public function handle_sorting_request( $vars ) {

		global $pagenow;

		// only trigger on upload page
		if ( 'upload.php' != $pagenow ) {
			return $vars;
		}

		$vars = $this->apply_sorting_preference( $vars );

		if ( empty( $vars['orderby'] ) ) {
			return $vars;
		}

		$column = $this->get_column_by_orderby( $vars['orderby'] );
		if ( empty( $column ) ) {
			return $vars;
		}

		// unsorted Attachments
		$posts = array();

		switch ( $column->properties->type ) :

			// WP Default Columns

			// Custom Columns
			case 'column-mediaid' :
				$vars['orderby'] = 'ID';
				break;

			case 'column-width' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$meta 	= wp_get_attachment_metadata( $id );
					$width 	= ! empty( $meta['width'] ) ? $meta['width'] : 0;
					if ( $width ) {
						$posts[ $id ] = $width;
					}
				}
				break;

			case 'column-height' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$meta 	= wp_get_attachment_metadata( $id );
					$height = ! empty( $meta['height'] ) ? $meta['height'] : 0;
					if ( $height ) {
						$posts[ $id ] = $height;
					}
				}
				break;

			case 'column-dimensions' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$meta 	= wp_get_attachment_metadata( $id );
					$height = ! empty( $meta['height'] ) ? $meta['height'] : 0;
					$width 	= ! empty( $meta['width'] ) ? $meta['width'] : 0;
					$surface = $height * $width;

					if ( $surface ) {
						$posts[ $id ] = $surface;
					}
				}
				break;

			case 'column-caption' :
				$sort_flag = SORT_STRING;
				break;

			case 'column-description' :
				$sort_flag = SORT_STRING;
				break;

			case 'column-mime_type' :
				$sort_flag = SORT_STRING;
				break;

			case 'column-file_name' :
				$sort_flag = SORT_STRING;
				foreach ( $this->get_posts() as $id ) {
					$meta 	= get_post_meta( $id, '_wp_attached_file', true );
					$file	= ! empty( $meta ) ? strtolower( basename( $meta ) ) : '';
					if ( $file ) {
						$posts[ $id ] = $file;
					}
				}
				break;

			case 'column-alternate_text' :
				$sort_flag = SORT_STRING;
				break;

			case 'column-file_size' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$file = wp_get_attachment_url( $id );
					if ( $file ) {
						$abs = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $file );
						if ( file_exists( $abs ) ) {
							$posts[ $id ] = $this->prepare_sort_string_value( filesize( $abs ) );
						}
					}
				}
				break;

			case 'column-available_sizes' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$meta = get_post_meta( $id, '_wp_attachment_metadata', true );
					if ( isset( $meta['sizes'] ) ) {
						$posts[ $id ] = count( array_intersect( array_keys( $meta['sizes'] ), get_intermediate_image_sizes() ) );
					}
				}
				break;

			case 'column-taxonomy' :
				$sort_flag 	= SORT_STRING;
				$posts 		= $this->get_posts_sorted_by_taxonomy( $column->options->taxonomy );
				break;

			case 'column-meta' :
				$field_type = 'meta_value';
				if ( in_array( $column->options->field_type, array( 'numeric', 'library_id') ) ) {
					$field_type = 'meta_value_num';
				}

				$vars = array_merge( $vars, array(
					'meta_key' 	=> $column->options->field,
					'orderby' 	=> $field_type
				));
				break;

			case 'column-exif_data' :
				$sort_flag = SORT_STRING;
				break;

			case 'column-acf_field' :
				if ( method_exists( $column, 'get_field' ) ) {
					$field = $column->get_field();
					$sort_flag = in_array( $field['type'], array( 'date_picker', 'number' ) ) ? SORT_NUMERIC : SORT_REGULAR;

					foreach ( $this->get_posts() as $id ) {
						$value = $column->get_sorting_value( $id );
						if ( $value || $this->show_all_results ) {
							$posts[ $id ] = $this->prepare_sort_string_value( $value );
						}
					}
				}

				break;

		endswitch;

		// we will add the sorted post ids to vars['post__in'] and remove unused vars
		if ( isset( $sort_flag ) ) {

			// orderby column value
			if ( ! $posts ) {
				foreach ( $this->get_posts() as $id ) {
					$posts[ $id ] = $this->prepare_sort_string_value( $column->get_value( $id ) );
				}
			}

			// set post__in vars
			$vars = $this->get_vars_post__in( $vars, $posts, $sort_flag );
		}

		/**
		 * Filters the sorting vars
		 *
		 * @since 3.2.1
		 *
		 * @param $vars array WP Query vars
		 * @param $column object Column instance
		 */
		return apply_filters( 'cac/addon/sortable/vars', $vars, $column );
	}
}