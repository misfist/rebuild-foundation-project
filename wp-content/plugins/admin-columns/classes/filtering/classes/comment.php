<?php

/**
 * Addon class
 *
 * @since 3.5
 */
class CAC_Filtering_Model_Comment extends CAC_Filtering_Model {

	/**
	 * Constructor
	 *
	 * @since 3.5
	 */
	public function __construct( $storage_model ) {

		parent::__construct( $storage_model );

		// handle filtering request
		add_action( 'pre_get_comments', array( $this, 'handle_filter_requests'), 2 );

		// add dropdowns
		add_action( 'restrict_manage_comments', array( $this, 'add_filtering_dropdown' ) );
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
			'response',

			// Custom Columns
			'column-agent',
			'column-approved',
			'column-author_email',
			'column-author_ip',
			'column-author_url',
			'column-author_name',
			'column-date',
			'column-date_gmt',
			'column-reply_to',
			'column-type',
			'column-user',
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
	 * Filter by default columns
	 *
	 * @since 3.5
	 */
	public function filter_by_agent( $comments_clauses ) {
		$comments_clauses['where'] .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->comments}.comment_agent = %s", $this->get_filter_value( 'column-agent' ) );
		return $comments_clauses;
	}
	public function filter_by_author( $comments_clauses ) {
		$comments_clauses['where'] .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->comments}.comment_author = %s", $this->get_filter_value( 'author' ) );
		return $comments_clauses;
	}
	public function filter_by_approved( $comments_clauses ) {
		$comments_clauses['where'] .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->comments}.comment_approved = %s", $this->get_filter_value( 'column-approved' ) );
		return $comments_clauses;
	}
	public function filter_by_author_ip( $comments_clauses ) {
		$comments_clauses['where'] .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->comments}.comment_author_IP = %s", $this->get_filter_value( 'column-author_ip' ) );
		return $comments_clauses;
	}
	public function filter_by_author_url( $comments_clauses ) {
		$comments_clauses['where'] .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->comments}.comment_author_url = %s", $this->get_filter_value( 'column-author_url' ) );
		return $comments_clauses;
	}
	public function filter_by_author_name( $comments_clauses ) {
		$comments_clauses['where'] .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->comments}.comment_author = %s", $this->get_filter_value( 'column-author_name' ) );
		return $comments_clauses;
	}
	public function filter_by_date( $comments_clauses ) {
		$comments_clauses['where'] .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->comments}.comment_date LIKE %s", $this->get_filter_value( 'column-date' ) . '%' );
		return $comments_clauses;
	}
	public function filter_by_date_gmt( $comments_clauses ) {
		$comments_clauses['where'] .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->comments}.comment_date_gmt LIKE %s", $this->get_filter_value( 'column-date_gmt' ) . '%' );
		return $comments_clauses;
	}

	/**
	 * Handle filter request
	 *
	 * @since 3.5
	 */
	public function handle_filter_requests( $comment_query ) {


		global $pagenow;

		if ( $this->storage_model->page . '.php' != $pagenow || empty( $_REQUEST['cpac_filter'] ) ) {
			return $comment_query;
		}

		// only run once
		if ( ! $comment_query->query_vars['number'] ) {
			return $comment_query;
		}

		// go through all filter requests per column
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
			$meta_value = in_array( $value, array( 'cpac_empty', 'cpac_not_empty' ) ) ? '' : $value;
			$meta_query_compare = 'cpac_not_empty' == $value ? '!=' : '=';

			switch ( $column->properties->type ) :

				// WP Default
				case 'author' :
					add_filter( 'comments_clauses', array( $this, 'filter_by_author' ) );
					break;

				case 'response' :
					$comment_query->query_vars['post_id'] = $meta_value;
					break;

				// Custom
				case 'column-agent' :
					add_filter( 'comments_clauses', array( $this, 'filter_by_agent' ) );
					break;

				case 'column-approved' :
					add_filter( 'comments_clauses', array( $this, 'filter_by_approved' ) );
					break;

				case 'column-author_email' :
					$comment_query->query_vars['author_email'] = $meta_value;
					break;

				case 'column-author_ip' :
					add_filter( 'comments_clauses', array( $this, 'filter_by_author_ip' ) );
					break;

				case 'column-author_url' :
					add_filter( 'comments_clauses', array( $this, 'filter_by_author_url' ) );
					break;

				case 'column-author_name' :
					add_filter( 'comments_clauses', array( $this, 'filter_by_author_name' ) );
					break;

				case 'column-date' :
					add_filter( 'comments_clauses', array( $this, 'filter_by_date' ) );
					break;

				case 'column-date_gmt' :
					add_filter( 'comments_clauses', array( $this, 'filter_by_date_gmt' ) );
					break;

				case 'column-reply_to' :
					$comment_query->query_vars['parent'] = $value;
					break;

				case 'column-user' :
					$comment_query->query_vars['user_id'] = $value;
					break;

				case 'column-type' :
					$comment_query->query_vars['type'] = $value;
					break;

				// Custom Fields
				case 'column-meta' :
					$comment_query->meta_query->parse_query_vars( array(
						'meta_query' => array( array(
							'key' => $column->options->field,
							'value' => $meta_value,
							'compare' => '='
						))
					));
					break;

				// ACF
				case 'column-acf_field' :
					if ( method_exists( $column, 'get_field' ) && ( $acf_field_obj = $column->get_field() ) ) {
						$comment_query->meta_query->parse_query_vars( array(
							'meta_query' => array( array(
								'key'		=> $acf_field_obj['name'],
								'value' 	=> $meta_value,
								'compare'	=> $meta_query_compare
							))
						));
					}
					break;

			endswitch;
		}

		return $comment_query;
	}

	/**
	 * Get values by user field
	 *
	 * @since 3.5
	 */
	public function get_values_by_comment_field( $comment_field ) {

		$options = array();

		$comment_field = sanitize_key( $comment_field );

		$sql = "
			SELECT DISTINCT {$comment_field}
			FROM {$this->wpdb->comments}
			WHERE {$comment_field} <> ''
			ORDER BY 1
		";

		$values = $this->wpdb->get_results( $sql, ARRAY_N );

		if ( is_wp_error( $values ) || ! $values ) {
			return array();
		}

		return $values;
	}

	/**
	 * Get values by meta key
	 *
	 * @since 3.5
	 */
	public function get_values_by_meta_key( $meta_key ) {

		$sql = "
			SELECT DISTINCT meta_value
			FROM {$this->wpdb->commentmeta} cm
			INNER JOIN {$this->wpdb->comments} c ON cm.comment_id = c.comment_ID
			WHERE cm.meta_key = %s
			AND cm.meta_value != ''
			ORDER BY 1
		";

		$values = $this->wpdb->get_results( $this->wpdb->prepare( $sql, $meta_key ), ARRAY_N );

		if ( is_wp_error( $values ) || ! $values ) {
			return array();
		}

		return $values;
	}

	/**
	 * Add filtering dropdown
	 *
	 * @since 3.5
	 * @todo: Add support for customfield values longer then 30 characters.
	 */
	public function get_dropdown_options_by_column() {

		$options = array();
		$empty_option = false;
		$order = 'ASC';

		switch ( $column->properties->type ) :

			// WP Default
			case 'author' :
				foreach ( $this->get_comment_fields( 'comment_author' ) as $_value ) {
					$options[ $_value ] = $_value;
				}
				break;

			case 'response' :
				foreach ( $this->get_comment_fields( 'comment_post_ID' ) as $_value ) {
					$options[ $_value ] = get_the_title( $_value );
				}
				break;

			// Custom
			case 'column-agent' :
				foreach ( $this->get_comment_fields( 'comment_agent' ) as $_value ) {
					$options[ $_value ] = $_value;
				}
				break;

			case 'column-approved' :
				$options = array(
					0 => __( 'No' ),
					1 => __( 'Yes' ),
				);
				break;

			case 'column-author_email' :
				foreach ( $this->get_comment_fields( 'comment_author_email' ) as $_value ) {
					$options[ $_value ] = $_value;
				}
				break;

			case 'column-author_ip' :
				foreach ( $this->get_comment_fields( 'comment_author_IP' ) as $_value ) {
					$options[ $_value ] = $_value;
				}
				break;

			case 'column-author_url' :
				foreach ( $this->get_comment_fields( 'comment_author_url' ) as $_value ) {
					$options[ $_value ] = $_value;
				}
				break;

			case 'column-author_name' :
				foreach ( $this->get_comment_fields( 'comment_author' ) as $_value ) {
					$options[ $_value ] = $_value;
				}
				break;

			case 'column-date' :
				$order = '';
				foreach ( $this->get_comment_fields( 'comment_date' ) as $_value ) {
					$date = substr( $_value, 0, 7 ); // only year and month
					$options[ $date ] = date_i18n( 'F Y', strtotime( $_value ) );
				}
				krsort( $options );
				break;

			case 'column-date_gmt' :
				$order = false; // we are sorting by key
				foreach ( $this->get_comment_fields( 'comment_date_gmt' ) as $_value ) {
					$date = substr( $_value, 0, 7 ); // only year and month
					$options[ $date ] = date_i18n( 'F Y', strtotime( $_value ) );
				}
				krsort( $options );
				break;

			case 'column-reply_to' :
				foreach ( $this->get_comment_fields( 'comment_parent' ) as $_value ) {
					 $options[ $_value ] = get_comment_author( $_value ) . ' (' . $_value . ')';
				}
				break;

			case 'column-type' :
				foreach ( $this->get_comment_fields( 'comment_type' ) as $_value ) {
					 $options[ $_value ] = $_value;
				}
				break;

			case 'column-user' :
				foreach ( $this->get_comment_fields( 'user_id' ) as $_value ) {
					 $options[ $_value ] = $column->get_display_name( $_value );
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

			// Filter by raw value
			case 'column-first_name' :
			case 'column-last_name' :
				$empty_option = true;
				foreach ( $this->get_user_ids() as $id ) {
					if ( $raw_value = $column->get_raw_value( $id ) ) {
						$options[ $raw_value ] = $raw_value;
					}
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