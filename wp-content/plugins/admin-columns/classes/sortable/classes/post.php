<?php

/**
 * Addon class
 *
 * @since 1.0
 */
class CAC_Sortable_Model_Post extends CAC_Sortable_Model {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	function __construct( $storage_model ) {
		parent::__construct( $storage_model );

		// default sortby
		$this->default_orderby = 'menu_order title';

		// handle sorting request
		add_filter( 'request', array( $this, 'handle_sorting_request'), 1 );

		// register sortable headings
		add_filter( "manage_edit-{$this->storage_model->key}_sortable_columns", array( $this, 'add_sortable_headings' ) );

		// add reset button
		add_action( 'restrict_manage_posts', array( $this, 'add_reset_button' ) );
	}

	/**
	 * Get sortables
	 *
	 * @see CAC_Sortable_Model::get_sortables()
	 * @since 1.0
	 */
	public function get_sortables() {

		$column_names = array(

			// WP default columns
			'author',
			'categories',
			'tags',
			'title',

			// Custom Columns
			'column-attachment',
			'column-attachment_count',
			'column-author_name',
			'column-before_moretag',
			'column-comment_count',
			'column-comment_status',
			'column-depth',
			'column-estimated_reading_time',
			'column-excerpt',
			'column-featured_image',
			'column-last_modified_author',
			'column-meta',
			'column-modified',
			'column-order',
			'column-page_template',
			'column-parent',
			'column-path',
			'column-ping_status',
			'column-post_formats',
			'column-postid',
			'column-roles',
			'column-slug',
			'column-status',
			'column-sticky',
			'column-taxonomy',
			'column-used_by_menu',
			'column-word_count',

			// ACF Fields
			'column-acf_field',

			// WooCommerce columns

			// Default WC
			'product_cat',
			'product_tag',

			// WC Product
			'price',
			'sku',
			'column-wc-dimensions',
			'column-wc-backorders_allowed',
			'column-wc-featured',
			'column-wc-parent',
			'column-wc-reviews_enabled',
			'column-wc-shipping_class',
			'column-wc-stock-status',
			'column-wc-variation',
			'column-wc-visibility',
			'column-wc-weight',

			// WC Order
			'order_status',
			'column-wc-cart_discount',
			'column-wc-order_discount',
			'column-wc-payment_method',

			// WC Coupon
			'type',
			'amount',
			'usage',
			'customer_message',
			'column-wc-free_shipping',
			'column-wc-apply_before_tax',
			'column-wc-product_type',
			'column-wc-payment_method',
			'column-wc-transaction_id',
			'expiry_date',
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

		// only trigger on edit page
		if ( 'edit.php' != $pagenow ) {
			return $vars;
		}

		// only handle request for this storage type
		if ( empty( $vars['post_type'] ) || $vars['post_type'] !== $this->storage_model->key ) {
			return $vars;
		}

		$post_type = $vars['post_type'];

		// apply sorting preference
		$new_vars = $this->apply_sorting_preference( $vars );

		// no sorting
		if ( empty( $new_vars['orderby'] ) ) {
			return $vars;
		}

		$column = $this->get_column_by_orderby( $new_vars['orderby'] );

		if ( empty( $column ) ) {
			return $vars;
		}

		// overwrite vars
		$vars = $new_vars;

		$posts = array();

		switch ( $column->properties->type ) :

			// WP Default Columns
			case 'title' :
				$vars['orderby'] = 'title';
				break;

			case 'author' :
				$vars['orderby'] = 'author';
				break;

			case 'categories' :
				$sort_flag = SORT_NUMERIC;
				$posts = $this->get_posts_sorted_by_taxonomy( 'category' );
				break;

			case 'tags' :
				$sort_flag = SORT_NUMERIC;
				$posts = $this->get_posts_sorted_by_taxonomy( 'post_tag' );
				break;

			// Custom Columns
			case 'column-postid' :
				$vars['orderby'] = 'ID';
				break;

			case 'column-order' :
				$vars['orderby'] = 'menu_order';
				break;

			case 'column-modified' :
				$vars['orderby'] = 'modified';
				break;

			case 'column-comment_count' :
				$vars['orderby'] = 'comment_count';
				break;

			case 'column-depth' :
				$sort_flag = SORT_STRING;
				break;

			case 'column-excerpt':
			case 'customer_message':
				$sort_flag = SORT_STRING;
				foreach ( $this->get_posts() as $id ) {
					if( ! ( $value = get_post_field( 'post_excerpt', $id ) ) ) {
						$value = trim( strip_tags( get_post_field( 'post_content', $id ) ) );
					}

					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $this->prepare_sort_string_value( $value );
					}
				}
				break;

			case 'column-word_count' :
				$sort_flag = SORT_NUMERIC;
				break;

			case 'column-page_template' :
				$sort_flag = SORT_STRING;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value;
					}
				}
				break;

			case 'column-path' :
				$sort_flag = SORT_STRING;
				break;

			case 'column-post_formats' :
				$sort_flag = SORT_REGULAR;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value;
					}
				}
				break;

			case 'column-attachment' :
			case 'column-attachment_count' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value;
					}
				}
				break;

			// @todo: can be improved, slug will sort 'slug-93', 'slug-9' and then 'slug-83'.
			// needs sorting mix with string and numeric
			case 'column-slug' :
				$sort_flag = SORT_REGULAR;
				break;

			case 'column-sticky' :
				$sort_flag = SORT_REGULAR;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value ? 0 : $id;
					}
				}
				break;

			case 'column-featured_image' :
				$sort_flag = SORT_REGULAR;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value ? 0 : $id;
					}
				}
				break;

			case 'column-last_modified_author' :
					$sort_flag = SORT_REGULAR;
					foreach ( $this->get_posts() as $id ) {
						$posts[ $id ] = $column->get_value( $id );
					}
				break;

			case 'column-roles' :
				$sort_flag = SORT_STRING;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value;
					}
				}
				break;

			case 'column-status' :
				$sort_flag = SORT_STRING;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value . strtotime( $id );
					}
				}
				break;

			case 'column-wc-reviews_enabled' :
			case 'column-comment_status' :
				$sort_flag = SORT_STRING;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value . strtotime( $id );
					}
				}
				break;

			case 'column-ping_status' :
				$sort_flag = SORT_STRING;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value . strtotime( $id );
					}
				}
				break;

			case 'column-taxonomy' :
				$sort_flag = SORT_NUMERIC;
				$posts = $this->get_posts_sorted_by_taxonomy( $column->options->taxonomy );
				break;

			case 'column-author_name' :
				$sort_flag = SORT_STRING;
				if ( 'userid' == $column->options->display_author_as ) {
					$sort_flag  = SORT_NUMERIC;
				}
				break;

			case 'column-before_moretag' :
				$sort_flag = SORT_STRING;
				break;

			case 'column-parent' :
				$sort_flag = SORT_REGULAR;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value . strtotime( $id );
					}
				}
				break;


			// Custom Field
			case 'column-meta' :

				// Post Title
				if ( 'title_by_id' == $column->options->field_type ) {
					$sort_flag = SORT_REGULAR;
					foreach ( $this->get_posts() as $id ) {

						// sort by the actual post_title instead of ID
						$meta 		= $column->get_meta_by_id( $id );
						$title_ids 	= $column->get_ids_from_meta( $meta );
						$title 		= isset( $title_ids[0] ) ? get_post_field( 'post_title', $title_ids[0] ) : '';

						if ( $title || $this->show_all_results ) {
							$posts[ $id ] = $title;
						}
					}
				}
				elseif ( 'count' == $column->options->field_type ) {
					$sort_flag = SORT_NUMERIC;
					foreach ( $this->get_posts() as $id ) {
						$count = $column->get_raw_value( $id, false );
						if ( $count || $this->show_all_results ) {
							$posts[ $id ] = count( $count );
						}
					}
				}
				elseif ( 'date' == $column->options->field_type ) {
					$sort_flag = SORT_NUMERIC;
					foreach ( $this->get_posts() as $id ) {
						$raw = $column->get_raw_value( $id );
						$timestamp = $column->get_timestamp( $raw );
						if ( $timestamp || $this->show_all_results ) {
							$posts[ $id ] = $timestamp;
						}
					}
 				}
				elseif ( 'term_by_id' == $column->options->field_type ) {
					$sort_flag = SORT_REGULAR;
					foreach ( $this->get_posts() as $id ) {
						$terms = $column->get_terms_by_id( $column->get_raw_value( $id ) );
						if ( $terms || $this->show_all_results ) {
							$posts[ $id ] = $terms;
						}
					}
				}

				// Default
				else {

					$is_type_numeric = in_array( $column->options->field_type, array( 'numeric', 'library_id', 'count' ) );

					// Show all resulsts
					if ( $this->show_all_results ) {
						$sort_flag = $is_type_numeric ? SORT_NUMERIC : SORT_REGULAR;
						foreach ( $this->get_posts() as $id ) {
							$value = $column->get_raw_value( $id );
							if ( $value || $this->show_all_results ) {
								$posts[ $id ] = $value;
							}
						}
					}

					// Show results that contain values only
					else {
						$vars = array_merge( $vars, array(
							'meta_key' 	=> $column->get_field_key(),
							'orderby' 	=> $is_type_numeric ? 'meta_value_num' : 'meta_value'
						));
					}
				}

				break;

			// ACF
			case 'column-acf_field' :

				// make sure acf has not been deactivated in the meanwhile...
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

			// WooCommerce
			case 'product_cat' :
				$sort_flag = SORT_NUMERIC;
				$posts = $this->get_posts_sorted_by_taxonomy( 'product_cat' );
				break;

			case 'product_tag' :
				$sort_flag = SORT_NUMERIC;
				$posts = $this->get_posts_sorted_by_taxonomy( 'product_tag' );
				break;

			case 'column-wc-parent':
				$sort_flag = SORT_REGULAR;

				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );

					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = get_the_title( $value );
					}
				}
				break;

			case 'column-wc-price' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$raw_value = $column->get_raw_value( $id );
					$value = isset( $raw_value['regular_price'] ) ? $raw_value['regular_price'] : '';
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value . strtotime( $id );
					}
				}
				break;

			case 'column-wc-dimensions' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$raw_value = $column->get_raw_value( $id );

					$value = '';
					if ( $raw_value['length'] || $raw_value['width'] || $raw_value['height'] ) {
						$value = $raw_value['length'] * $raw_value['width'] * $raw_value['height'];
					}
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value;
					}
				}
				break;

			case 'amount':
				$sort_flag = SORT_NUMERIC;
				break;

			case 'usage':
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_posts() as $id ) {
					$raw_value = $column->get_raw_value( $id );
					$usage = isset( $raw_value['usage_limit'] ) ? $raw_value['usage_limit'] : false;
					if ( $usage || $this->show_all_results ) {
						$posts[ $id ] = $usage;
					}
				}
				break;

			case 'column-wc-visibility':
				$sort_flag = SORT_REGULAR;
				foreach ( $this->get_posts() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$posts[ $id ] = $value;
					}
				}
				break;

			// Try to sort by raw value.
			// Only used by added custom admin column throuhg the API
			default :

				// available since cpac 2.0.3
				if ( method_exists( $column, 'get_raw_value' ) ) {

					$sort_flag = SORT_REGULAR;
					foreach ( $this->get_posts() as $id ) {
						$value = $column->get_raw_value( $id );

						if ( $value || $this->show_all_results ) {
							$posts[ $id ] = $value;
						}
					}
				}

		endswitch;

		// we will add the sorted post ids to vars['post__in'] and remove unused vars
		if ( isset( $sort_flag ) ) {

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