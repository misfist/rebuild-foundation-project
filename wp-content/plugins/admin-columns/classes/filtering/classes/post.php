<?php

/**
 * Filtering Model for Posts ánd Media!
 *
 * @since 1.0
 */
class CAC_Filtering_Model_Post extends CAC_Filtering_Model {

	/**
	 * Constructor
	 *
	 * @since 1.0
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
	 * @since 1.0
	 */
	public function enable_filtering( $columns ) {

		$include_types = array(

			// WP default columns
			'categories',
			'tags',

			// Custom columns
			'column-author_name',
			'column-before_moretag',
			'column-comment_count',
			'column-comment_status',
			'column-excerpt',
			'column-featured_image',
			'column-last_modified_author',
			'column-page_template',
			'column-ping_status',
			'column-post_formats',
			'column-roles',
			'column-status',
			'column-sticky',
			'column-taxonomy',

			// WooCommerce columns
			'product_cat', // default
			'product_tag', // default
			'order_status', // default
			'customer_message', // default
			'column-wc-featured',
			'column-wc-visibility',
			'column-wc-free_shipping',
			//'column-wc-apply_before_tax',
			'column-wc-order_coupons_used',
			'column-wc-shipping_class',
			'column-wc-parent',
			'column-wc-payment_method',
			'column-wc-reviews_enabled'
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
	public function filter_by_author_name( $where ) {
		return $where . $this->wpdb->prepare( "AND {$this->wpdb->posts}.post_author = %s", $this->get_filter_value( 'column-author_name' ) );
	}
	public function filter_by_before_moretag( $where ) {
		return $where . "AND {$this->wpdb->posts}.post_content" . $this->get_sql_value( $this->get_filter_value( 'column-before_moretag' ), '<!--more-->' );
	}
	public function filter_by_comment_count( $where ) {
		$val = $this->get_filter_value( 'column-comment_count' );
		$sql_val = ' = ' . $val;
		if ( 'cpac_not_empty' == $val ) {
			$sql_val = ' != 0';
		}
		else if ( 'cpac_empty' == $val ) {
			$sql_val = ' = 0';
		}
		return "{$where} AND {$this->wpdb->posts}.comment_count" . $sql_val;
	}
	public function filter_by_comment_status( $where ) {
		return $where . "AND {$this->wpdb->posts}.comment_status" . $this->get_sql_value( $this->get_filter_value( 'column-comment_status' ) );
	}
	public function filter_by_excerpt( $where ) {
		$val = $this->get_filter_value( 'column-excerpt' );
		$sql_val = '1' === $val ? " != ''" : " = ''";
		return "{$where} AND {$this->wpdb->posts}.post_excerpt" . $sql_val;
	}
	public function filter_by_ping_status( $where ) {
		return $where . $this->wpdb->prepare( "AND {$this->wpdb->posts}.ping_status = %s", $this->get_filter_value( 'column-ping_status' ) );
	}
	public function filter_by_sticky( $where ) {
		$val = $this->get_filter_value( 'column-sticky' );
		if ( ! ( $stickies = get_option( 'sticky_posts' ) ) ) {
			return $where;
		}
		$sql_val = '1' === $val ? " IN ('" . implode( "','", $stickies ) . "')" : " NOT IN ('" . implode( "','", $stickies ) . "')";
		return "{$where} AND {$this->wpdb->posts}.ID" . $sql_val;
	}
	public function filter_by_wc_reviews_enabled( $where ) {
		return $where . "AND {$this->wpdb->posts}.comment_status" . $this->get_sql_value( $this->get_filter_value( 'column-wc-reviews_enabled' ) );
	}

	/**
	 * Get SQL compare
	 *
	 * @since 1.0
	 *
	 * @param string $filter_value Selected filter value
	 * @param string $value_to_match_empty Overwrite the filter value
	 * @return string SQL compare
	 */
	private function get_sql_value( $filter_value, $value_to_match_empty = '' ) {
		$sql_query_compare = " = '{$filter_value}'";

		if ( 'cpac_not_empty' === $filter_value || '1' === $filter_value ) {
			$val = $value_to_match_empty ? $value_to_match_empty : $filter_value;
			$sql_query_compare = " LIKE '%{$val}%'";
		}
		else if ( 'cpac_empty' == $filter_value || '0' === $filter_value) {
			$val = $value_to_match_empty ? $value_to_match_empty : $filter_value;
			$sql_query_compare = " NOT LIKE '%{$val}%'";
		}

		return $sql_query_compare;
	}

	/**
	 * Handle filter request
	 *
	 * @since 1.0
	 */
	public function handle_filter_requests( $vars ) {

		global $pagenow;

		if ( $this->storage_model->page . '.php' != $pagenow || empty( $_REQUEST['cpac_filter'] ) ) {
			return $vars;
		}

		if ( ! empty( $vars['post_type'] ) && $vars['post_type'] !== $this->storage_model->post_type ) {
			return $vars;
		}

		// go through all filter requests per column
		foreach ( $_REQUEST['cpac_filter'] as $name => $value ) {

			$value = urldecode( $value );

			if ( ! $column = $this->storage_model->get_column_by_name( $name ) ) {
				continue;
			}

			// When using ranges, a value can be NULL
			if ( ! $column->properties->use_filter_operator && strlen( $value ) < 1 ) {
				continue;
			}

			// add the value to so we can use it in the 'post_where' callback
			$this->set_filter_value( $column->properties->type, $value );

			// meta arguments
			$meta_value 		= in_array( $value, array( 'cpac_empty', 'cpac_not_empty' ) ) ? '' : $value;
			$meta_query_compare = 'cpac_not_empty' == $value ? '!=' : '=';

			// custom meta compare operators
			if ( $column->properties->use_filter_operator && in_array( $column->options->filter_operator, array( '>', '>=', '<', '<=' ) ) ) {
				$meta_query_compare = $column->options->filter_operator;
			}

			switch ( $column->properties->type ) :

				// Default
				case 'tags' :
					$vars['tax_query'] = $this->get_taxonomy_tax_query( $value, 'post_tag', $vars );
					break;

				// Custom
				case 'column-author_name' :
					add_filter( 'posts_where', array( $this, 'filter_by_author_name' ) );
					break;

				case 'column-before_moretag' :
					add_filter( 'posts_where', array( $this, 'filter_by_before_moretag' ) );
					break;

				case 'column-comment_count' :
					add_filter( 'posts_where', array( $this, 'filter_by_comment_count' ) );
					break;

				case 'column-comment_status':
					add_filter( 'posts_where', array( $this, 'filter_by_comment_status' ) );
					break;

				case 'column-excerpt' :
					add_filter( 'posts_where', array( $this, 'filter_by_excerpt' ) );
					break;

				case 'column-featured_image' :
					// check for keys that dont exist
					if ( 'cpac_empty' == $value ) {
						$meta_query_compare = 'NOT EXISTS';
					}

					$vars['meta_query'][] = array(
						'key'		=> '_thumbnail_id',
						'value' 	=> $meta_value,
						'compare'	=> $meta_query_compare
					);
					break;

				case 'column-last_modified_author' :
					$vars['meta_query'][] = array(
						'key'		=> '_edit_last',
						'value' 	=> $meta_value,
						'compare'	=> $meta_query_compare
					);
					break;

				case 'column-page_template' :
					$vars['meta_query'][] = array(
						'key'		=> '_wp_page_template',
						'value' 	=> $meta_value,
						'compare'	=> $meta_query_compare
					);
					break;

				case 'column-ping_status' :
					add_filter( 'posts_where', array( $this, 'filter_by_ping_status' ) );
					break;

				case 'column-post_formats' :
					$vars['tax_query'][] = array(
						'taxonomy'	=> 'post_format',
						'field'		=> 'slug',
						'terms'		=> $value
					);
					break;

				case 'column-roles' :
					$user_ids = get_users( array( 'role' => $value, 'fields' => 'id' ));
					$vars['author'] = implode( ',', $user_ids );
					break;

				case 'column-sticky' :
					add_filter( 'posts_where', array( $this, 'filter_by_sticky' ) );
					break;

				case 'column-status' :
					$vars['post_status'] = $value;
					break;

				case 'column-taxonomy' :
					$vars['tax_query'] = $this->get_taxonomy_tax_query( $value, $column->options->taxonomy, $vars );
					break;

				// Custom Fields
				case 'column-meta' :
					$is_numeric = in_array( $column->options->field_type, array( 'numeric' ) );

					// Operator BETWEEN
					if ( $is_numeric && isset( $column->options->filter_operator ) && 'between' == $column->options->filter_operator ) {

						$lesser_than = ! empty( $_REQUEST['cpac_filter'][$name . '-lesser'] ) ? $_REQUEST['cpac_filter'][$name . '-lesser'] : '';
						$greater_than = $meta_value ? $meta_value : '';

						if ( $lesser_than ) {
							$vars['meta_query'][] = array(
								'key'		=> $column->get_field_key(),
								'value' 	=> $lesser_than,
								'compare'	=> '<=',
								'type'		=> 'NUMERIC'
							);
						}
						if ( $greater_than ) {
							$vars['meta_query'][] = array(
								'key'		=> $column->get_field_key(),
								'value' 	=> $greater_than,
								'compare'	=> '>=',
								'type'		=> 'NUMERIC'
							);
						}

					}

					elseif( $meta_value ) {
						$vars['meta_query'][] = array(
							'key'		=> $column->get_field_key(),
							'value' 	=> $meta_value,
							'compare'	=> $meta_query_compare,
							'type'		=> $is_numeric ? 'NUMERIC' : 'CHAR'
						);
					}

					break;

				// ACF
				case 'column-acf_field' :

					if ( method_exists( $column, 'get_field' ) && ( $acf_field_obj = $column->get_field() ) ) {

						$meta_key = $acf_field_obj['name'];

						// Meta date query
						if ( 'date_picker' === $column->get_field_type() ) {

							switch( $column->options->filter_type ) {

								case 'monthly':
									$vars['meta_query'][] = array(
										'key'		=> $meta_key,
										'value' 	=> date( 'Ymd', strtotime( $meta_value . '01' ) ),
										'compare'	=> '>=',
										'type'		=> 'NUMERIC'
									);
									$vars['meta_query'][] = array(
										'key'		=> $meta_key,
										'value' 	=> date( 'Ymd', strtotime( "+1 month", strtotime( $meta_value . '01' ) ) ),
										'compare'	=> '<',
										'type'		=> 'NUMERIC'
									);
								break;
								case 'yearly':
									$vars['meta_query'][] = array(
										'key'		=> $meta_key,
										'value' 	=> date( 'Ymd', strtotime( $meta_value . '0101' ) ),
										'compare'	=> '>=',
										'type'		=> 'NUMERIC'
									);
									$vars['meta_query'][] = array(
										'key'		=> $meta_key,
										'value' 	=> date( 'Ymd', strtotime( "+1 year", strtotime( $meta_value . '0101' ) ) ),
										'compare'	=> '<',
										'type'		=> 'NUMERIC'
									);
								break;
								default:
									$vars['meta_query'][] = array(
										'key'		=> $meta_key,
										'value' 	=> $meta_value,
										'compare'	=> $meta_query_compare
									);
							}
						}

						// Defaul Meta Query
						else {
							$vars['meta_query'][] = array(
								'key'		=> $meta_key,
								'value' 	=> $meta_value,
								'compare'	=> $meta_query_compare
							);
						}

					}
					break;

				// WooCommerce
				case 'product_cat' :
					$vars['tax_query'][] = array(
						'taxonomy'	=> 'product_cat',
						'field'		=> 'slug',
						'terms'		=> $value
					);
					break;

				case 'product_tag' :
					$vars['tax_query'][] = array(
						'taxonomy'	=> 'product_tag',
						'field'		=> 'slug',
						'terms'		=> $value
					);
					break;

				case 'column-wc-featured' :
					$vars['meta_query'][] = array(
						'key'		=> '_featured',
						'value' 	=> $meta_value,
						'compare'	=> $meta_query_compare
					);
					break;

				case 'column-wc-visibility' :
					$vars['meta_query'][] = array(
						'key'		=> '_visibility',
						'value' 	=> $meta_value,
						'compare'	=> $meta_query_compare
					);
					break;

				case 'column-wc-free_shipping':
					$vars['meta_query'][] = array(
						'key' => 'free_shipping',
						'value' => $meta_value
					);
					break;

				case 'column-wc-order_coupons_used':
					if( 'no' == $meta_value ){
						$meta_query_compare = 'NOT EXISTS';
					}

					$vars['meta_query'][] = array(
						'key' => '_recorded_coupon_usage_counts',
						'value' => $meta_value,
						'compare'	=> $meta_query_compare
					);

					break;

				case 'column-wc-shipping_class':
					$vars['tax_query'] = $this->get_taxonomy_tax_query( $value, 'product_shipping_class', $vars );
					break;

				case 'column-wc-parent':
					$vars['post_parent'] = $value;
					break;

				case 'column-wc-payment_method':
					$vars['meta_query'][] = array(
						'key' => '_payment_method',
						'value' => $meta_value
					);
					break;

				case 'column-wc-reviews_enabled':
					add_filter( 'posts_where', array( $this, 'filter_by_wc_reviews_enabled' ) );
					break;

				case 'order_status':
					$vars['post_status'] = ( substr( $value, 0, 3 ) == 'wc-' ) ? $value : 'wc-' . $value;
					break;

				case 'customer_message' :
					add_filter( 'posts_where', array( $this, 'filter_by_excerpt' ) );
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

			// Default
			case 'tags' :
				$empty_option = true;
				$terms_args = apply_filters( 'cac/addon/filtering/taxonomy/terms_args', array() );
				$options = $this->apply_indenting_markup( get_terms( 'post_tag', $terms_args ) );
				break;

			// Custom
			case 'column-sticky' :
				$options = array(
					0 => __( 'Not sticky', 'codepress-admin-columns' ),
					1 => __( 'Sticky', 'codepress-admin-columns' ),
				);
				break;

			case 'column-roles' :
				global $wp_roles;
				foreach( $wp_roles->role_names as $role => $name ) {
					$options[ $role ] = $name;
				}
				break;

			case 'column-page_template' :
				if ( $values = $this->get_values_by_meta_key( '_wp_page_template' ) ) {
					foreach ( $values as $value ) {
						$page_template = $value[0];
						if ( $label = array_search( $page_template, get_page_templates() ) ) {
							$page_template = $label;
						}
						$options[ $value[0] ] = $page_template;
					}
				}
				break;

			case 'column-ping_status' :
				if ( $values = $this->get_post_fields( 'ping_status' ) ) {
					foreach ( $values as $value ) {
						$options[ $value ] = $value;
					}
				}
				break;

			case 'column-post_formats' :
				$options = $this->apply_indenting_markup( $this->indent( get_terms( 'post_format', array( 'hide_empty' => false ) ), 0, 'parent', 'term_id' ) );
				break;

			case 'column-excerpt' :
				$options = array(
					0 => __( 'Empty', 'codepress-admin-columns' ),
					1 => __( 'Has excerpt', 'codepress-admin-columns' ),
				);
				break;

			case 'column-comment_count' :
				$empty_option = true;
				if ( $values = $this->get_post_fields( 'comment_count' ) ) {
					foreach ( $values as $value ) {
						$options[ $value ] = $value;
					}
				}
				break;

			case 'column-before_moretag' :
				$options = array(
					0 => __( 'Empty', 'codepress-admin-columns' ),
					1 => __( 'Has more tag', 'codepress-admin-columns' ),
				);
				break;

			case 'column-author_name' :
				if ( $values = $this->get_post_fields( 'post_author' ) ) {
					foreach ( $values as $value ) {
						$options[ $value ] = $column->get_display_name( $value );
					}
				}
				break;

			case 'column-featured_image' :
				$empty_option = true;
				if ( $values = $this->get_values_by_meta_key( '_thumbnail_id' ) ) {
					foreach ( $values as $value ) {
						$options[ $value[0] ] = $value[0];
					}
				}
				break;

			case 'column-comment_status':
			case 'column-wc-reviews_enabled':
				$options = array(
					'open' => __( 'Open' ),
					'closed' => __( 'Closed' )
				);
				break;

			case 'column-status' :
				if ( $values = $this->get_post_fields( 'post_status' ) ) {
					foreach ( $values as $value ) {
						if ( 'auto-draft' != $value ) {
							$options[ $value ] = $value;
						}
					}
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

			case 'column-last_modified_author' :
				if ( $values = $this->get_values_by_meta_key( '_edit_last' ) ) {
					foreach ( $values as $value ) {
						$options[ $value[0] ] = $column->get_display_name( $value[0] );
					}
				}
				break;

			// Custom Field column
			case 'column-meta' :
				if ( $_options = $this->get_meta_options( $column ) ) {
					$empty_option = $_options['empty_option'];
					$options = $_options['options'];
				}
				break;

			// ACF column
			case 'column-acf_field' :
				if ( $_options = $this->get_acf_options( $column ) ) {
					$order = $_options['order'];
					$empty_option = $_options['empty_option'];
					$options = $_options['options'];
				}
				break;


			// WooCommerce columns
			case 'column-wc-featured':
				$options = array(
					'no' => __( 'No' ),
					'yes' => __( 'Yes' )
				);
				break;

			case 'column-wc-visibility':
				$options = $column->get_visibility_options();
				break;

			case 'column-wc-free_shipping':
				$options = array(
					'no' => __( 'No' ),
					'yes' => __( 'Yes' )
				);
				break;

			case 'column-wc-order_coupons_used':
				$options = array(
					'no' => __( 'No' ),
					'yes' => __( 'Yes' )
				);
				break;

			case 'column-wc-shipping_class':
				$empty_option = true;
				$order = false; // do not sort, messes up the indenting
				$terms_args = apply_filters( 'cac/addon/filtering/taxonomy/terms_args', array() );
				$options = $this->apply_indenting_markup( $this->indent( get_terms( 'product_shipping_class', $terms_args ), 0, 'parent', 'term_id' ) );
				break;

			case 'column-wc-parent':
				$empty_option = true;

				if ( $values = $this->get_post_fields( 'post_parent' ) ) {
					foreach ( $values as $value ) {
						$options[ $value ] = get_the_title( $value );
					}
				}
				break;
			case 'column-wc-payment_method':
				$empty_option = true;

				if ( WC()->payment_gateways() ) {
					$payment_gateways = WC()->payment_gateways->payment_gateways();
				} else {
					$payment_gateways = array();
				}

				foreach ( $payment_gateways as $gateway ) {
					if ( $gateway->enabled == 'yes' ) {
						$options[ $gateway->id ] = $gateway->get_title();
					}
				}
				break;

			case 'order_status':
				$options = array();

				if ( cpac_is_wc_version_gte( '2.2' ) ) {
					$options = wc_get_order_statuses();
				}

				else {
					$statuses_raw = (array) get_terms( 'shop_order_status', array( 'hide_empty' => 0, 'orderby' => 'id' ) );

					foreach ( $statuses_raw as $status ) {
						$options[ $status->slug ] = $status->name;
					}
				}
				break;

			case 'customer_message':
				$options = array(
					0 => __( 'Empty', 'codepress-admin-columns' ),
					1 => __( 'Has customer message', 'codepress-admin-columns' ),
				);
				break;

			default :
				if ( method_exists( $column, 'get_filter_options' ) ) {
					$options = (array) $column->get_filter_options();
				}

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