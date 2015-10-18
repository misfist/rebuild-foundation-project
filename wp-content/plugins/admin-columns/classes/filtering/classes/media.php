<?php

/**
 * Filtering Model for Posts ánd Media!
 *
 * @since 3.5
 */
class CAC_Filtering_Model_Media extends CAC_Filtering_Model {

	/**
	 * Constructor
	 *
	 * @since 3.5
	 */
	public function __construct( $storage_model ) {

		parent::__construct( $storage_model );

		// handle filtering request
		add_filter( 'request', array( $this, 'handle_filter_requests' ), 2 );

		// add dropdowns
		add_action( 'restrict_manage_posts', array( $this, 'add_filtering_dropdown' ) );
	}

	/**
	 * Enable filtering
	 *
	 * @since 3.5
	 */
	public function enable_filtering( $columns ) {

		$include_types = array(

			// WP default columns
			'author',
			'comments',
			'date',
			'parent',

			// Custom columns
			'column-description',
			'column-mime_type',
			'column-taxonomy',
		);

		foreach ( $columns as $column ) {
			if ( in_array( $column->properties->type, $include_types ) ) {
				$column->set_properties( 'is_filterable', true );
			}

			$this->enable_filterable_custom_field( $column );
			$this->enable_filterable_acf_field( $column );
		}
	}

	/**
	 * @since 3.5
	 */
	public function filter_by_comments( $where ) {
		if ( '0' == $this->get_filter_value( 'comments' ) ) {
			$where .= "AND {$this->wpdb->posts}.comment_count = '0'";
		}
		elseif ( '1' == $this->get_filter_value( 'comments' ) ) {
			$where .= "AND {$this->wpdb->posts}.comment_count <> '0'";
		}
		return $where;
	}
	public function filter_by_mime_type( $where ) {
		return $where . $this->wpdb->prepare( "AND {$this->wpdb->posts}.post_mime_type = %s", $this->get_filter_value( 'column-mime_type' ) );
	}
	public function filter_by_description( $where ) {
		return $where . $this->wpdb->prepare( "AND {$this->wpdb->posts}.post_content = %s", $this->get_filter_value( 'column-description' ) );
	}

	/**
	 * Handle filter request
	 *
	 * @since 3.5
	 */
	public function handle_filter_requests( $vars ) {

		global $pagenow;
		if ( $this->storage_model->page . '.php' != $pagenow || empty( $_REQUEST['cpac_filter'] ) ) {
			return $vars;
		}

		foreach ( $_REQUEST['cpac_filter'] as $name => $value ) {

			$value = urldecode( $value );

			if ( strlen( $value ) < 1 ) {
				continue;
			}

			if ( ! $column = $this->storage_model->get_column_by_name( $name ) ) {
				continue;
			}

			// add the value to so we can use it in the 'post_where' callback
			$this->set_filter_value( $column->properties->type, $value );

			// meta arguments
			$meta_value 		= in_array( $value, array( 'cpac_empty', 'cpac_not_empty' ) ) ? '' : $value;
			$meta_query_compare = 'cpac_not_empty' == $value ? '!=' : '=';

			switch ( $column->properties->type ) :

				// WP Default
				case 'author' :
					$vars['author'] = $value;
					break;

				case 'date' :
					$vars['date_query'][] = array(
						'year' => absint( substr( $value, 0, 4 ) ),
						'month' => absint( substr( $value, -2 ) ),
					);
					break;

				case 'parent' :
					$vars['post_parent'] = $value;
					break;

				case 'comments' :
					add_filter( 'posts_where', array( $this, 'filter_by_comments' ) );
					break;

				// Custom
				case 'column-description' :
					add_filter( 'posts_where', array( $this, 'filter_by_description' ) );
					break;

				case 'column-mime_type' :
					add_filter( 'posts_where', array( $this, 'filter_by_mime_type' ) );
					break;

				case 'column-taxonomy' :
					$vars['tax_query'] = $this->get_taxonomy_tax_query( $value, $column->options->taxonomy, $vars );
					break;


				// Custom Fields
				case 'column-meta' :
					$vars['meta_query'][] = array(
						'key'		=> $column->options->field,
						'value' 	=> $meta_value,
						'compare'	=> $meta_query_compare
					);
					break;

				// ACF
				case 'column-acf_field' :
					if ( method_exists( $column, 'get_field' ) && ( $acf_field_obj = $column->get_field() ) ) {
						$vars['meta_query'][] = array(
							'key'		=> $acf_field_obj['name'],
							'value' 	=> $meta_value,
							'compare'	=> $meta_query_compare
						);
					}
					break;

				// Try to filter by using the column's custom defined filter method
				default :
					if ( method_exists( $column, 'get_filter_post_vars' ) ) {
						$column->set_filter( $this ); // use $column->get_filter() to use the model inside a column object
						$vars = array_merge( $vars, (array) $column->get_filter_post_vars() );
					}

			endswitch;
		}

		return $vars;
	}

	/**
	 * @since 3.6
	 */
	public function get_dropdown_options_by_column( $column ) {

		$options = array();
		$empty_option = false;
		$order = 'ASC';

		switch ( $column->properties->type ) :

			// WP Default
			case 'author' :
				if ( $values = $this->get_post_fields( 'post_author' ) ) {
					foreach ( $values as $value ) {
						$user = get_user_by( 'id', $value );
						$options[ $value ] = $user->display_name;
					}
				}
				break;

			case 'comments' :
				$top_label = __( 'All comments', 'codepress-admin-columns' );
				$options = array(
					0 => __( 'No comments', 'capc' ),
					1 => __( 'Has comments', 'capc' ),
				);
				break;

			case 'date' :
				$order = '';
				foreach ( $this->get_post_fields( 'post_date' ) as $_value ) {
					$date = substr( $_value, 0, 7 ); // only year and month
					$options[ $date ] = date_i18n( 'F Y', strtotime( $_value ) );
				}
				krsort( $options );
				break;

			case 'parent' :
				foreach ( $this->get_post_fields( 'post_parent' ) as $_value ) {
					$options[ $_value ] = get_the_title( $_value );
				}
				break;

			// Custom
			case 'column-description' :
				foreach ( $this->get_post_fields( 'post_content' ) as $_value ) {
					$options[ $_value ] = strip_tags( $_value );
				}
				break;

			case 'column-mime_type' :
				$mime_types = array_flip( wp_get_mime_types() );
				foreach ( $this->get_post_fields( 'post_mime_type' ) as $_value ) {
					$options[ $_value ] = $mime_types[ $_value ];
				}
				break;

			case 'column-taxonomy' :
				if ( taxonomy_exists( $column->options->taxonomy ) ) {
					$empty_option = true;
					$order = false; // do not sort, messes up the indenting
					$terms_args = apply_filters( 'cac/addon/filtering/taxonomy/terms_args', array() );
					$options = $this->apply_indenting_markup( $this->indent( get_terms( $column->options->taxonomy, $terms_args ), 0, 'parent', 'term_id' ) );
				}
				break;

			case 'column-meta' :
				if ( $_options = $this->get_meta_options( $column ) ) {
					$empty_option = $_options['empty_option'];
					$options = $_options['options'];
				}
				break;

			case 'column-acf_field' :
				if ( $_options = $this->get_acf_options( $column ) ) {
					$empty_option = $_options['empty_option'];
					$options = $_options['options'];
				}
				break;

		endswitch;

		// sort the options
		if ( 'ASC' == $order ) {
			asort( $options );
		}
		if ( 'DESC' == $order ) {
			arsort( $options );
		}

		return array( 'options' => $options, 'empty_option' => $empty_option );
	}
}