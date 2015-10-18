<?php

/**
 * Addon class
 *
 * @since 1.0
 */
class CAC_Sortable_Model_User extends CAC_Sortable_Model {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	function __construct( $storage_model ) {
		parent::__construct( $storage_model );

		// default sortby
		$this->default_orderby = '';

		// handle sorting request
		add_action( 'pre_user_query', array( $this, 'handle_sorting_request' ), 1 );

		// register sortable headings
		add_filter( "manage_users_sortable_columns", array( $this, 'add_sortable_headings' ) );

		// add reset button
		add_action( 'restrict_manage_users', array( $this, 'add_reset_button' ) );
	}

	/**
	 * @see CAC_Sortable_Model::get_sortables()
	 * @since 1.0
	 */
	public function get_sortables() {

		$column_names = array(

			// WP default columns
			'role',
			'posts',

			// Custom Columns
			'column-first_name',
			'column-display_name',
			'column-last_name',
			'column-meta',
			'column-nickname',
			'column-user_commentcount',
			'column-user_description',
			'column-user_id',
			'column-user_postcount',
			'column-user_registered',
			'column-user_url',

			// ACF Fields
			'column-acf_field',

			// WooCommerce
			'column-wc-user-orders',
			'column-wc-user-order_count'
			//'column-wc-user-total-sales'

			// WC Subscriptions extension
			//'column-wc-user-subscription-expiration-date',
			//'column-wc-user-subscription-trial-expiration-date',
			//'column-wc-user-subscription-status',
			//'column-wc-user-subscription-next-payment-date',
			//'column-wc-user-subscription-last-payment-date',
			//'column-wc-user-subscription-end-date'
		);

		return $column_names;
	}

	/**
	 * Get Users
	 *
	 * Do not use get_users() method.
	 *
	 * @since 1.0
	 */
	private function get_user_ids() {
		global $wpdb;
		return $wpdb->get_col( "SELECT ID FROM $wpdb->users" );
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
	public function handle_sorting_request( $user_query ) {
		global $wpdb;

		$vars = $user_query->query_vars;

		// prevent looping because this filter is trigered by get_users();
		/*if ( 'ID' === $vars['fields'] ) {
			return;
		}*/

		// sorting event?
		if ( empty( $vars['orderby'] ) ) {
			return;
		}

		$vars = $this->apply_sorting_preference( $vars );

		$column = $this->get_column_by_orderby( $vars['orderby'] );
		if ( empty( $column ) ) {
			return;
		}

		// unsorted Users
		$_users = array();

		switch ( $column->properties->type ) :

			// WP Default Columns
			case 'role' :
				$sort_flag = SORT_REGULAR;
				foreach ( $this->get_user_ids() as $id ) {
					$u = get_userdata( $id );
					$role = ! empty( $u->roles[0] ) ? $u->roles[0] : '';
					if ( $role ) {
						$_users[ $id ] = $this->prepare_sort_string_value( $role );
					}
				}
				break;

			case 'posts' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_user_ids() as $id ) {
					$_users[ $id ] = $column->get_user_postcount( $id, 'post' );
				}
				break;

			// Custom Columns
			case 'column-user_id' :
				$user_query->query_orderby = "ORDER BY ID {$vars['order']}";
				$vars['orderby'] = 'ID';
				break;

			case 'column-user_registered' :
				$user_query->query_orderby = "ORDER BY user_registered {$vars['order']}";
				$vars['orderby'] = 'registered';
				break;

			case 'column-nickname' :
				$sort_flag = SORT_REGULAR;
				break;

			case 'column-first_name' :
				$sort_flag = SORT_REGULAR;
				break;

			case 'column-display_name' :
				$sort_flag = SORT_REGULAR;
				break;

			case 'column-last_name' :
				$sort_flag = SORT_REGULAR;
				break;

			case 'column-user_url' :
				$sort_flag = SORT_REGULAR;
				break;

			case 'column-user_description' :
				$sort_flag = SORT_REGULAR;
				break;

			case 'column-user_commentcount' :
				// @todo: maybe use WP_Comment_Query to generate this subquery? penalty is extra query and bloat, advantage is WP_Comment_Query filters used
				$sub_query = "
					LEFT JOIN (
						SELECT user_id, COUNT(user_id) AS comment_count
						FROM {$wpdb->comments}
						WHERE user_id <> 0
						GROUP BY user_id
					) AS comments
					ON {$wpdb->users}.ID = comments.user_id
				";

				$user_query->query_from .= $sub_query;
				$user_query->query_orderby = "ORDER BY comment_count " . $vars['order'];

				if ( ! $this->show_all_results ) {
					$user_query->query_where .= " AND comment_count IS NOT NULL";
				}

				break;
			case 'column-user_postcount' :
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_user_ids() as $id ) {
					$_users[ $id ] = $column->get_count( $id );
				}
				break;

			case 'column-meta' :
				$sort_flag = SORT_REGULAR;

				if ( 'numeric' == $column->options->field_type ) {
					$sort_flag = SORT_NUMERIC;
				}

				if ( 'checkmark' == $column->options->field_type ) {
					foreach ( $this->get_user_ids() as $id ) {
						$value = $column->get_value( $id );
						$_users[ $id ] = $this->prepare_sort_string_value( $value ? '1' : '0' );
					}
				}
				if ( in_array( $column->options->field_type, array( 'image', 'library_id' ) ) ) {
					$sort_flag = SORT_NUMERIC;
					foreach ( $this->get_user_ids() as $id ) {
						$thumbs = $column->get_thumbnails( $column->get_meta_by_id( $id ) );
						$_users[ $id ] = $thumbs ? count( $thumbs ) : 0;
					}
				}
				break;

			case 'column-acf_field' :
				if ( method_exists( $column, 'get_field' ) ) {
					$field = $column->get_field();
					$sort_flag = in_array( $field['type'], array( 'date_picker', 'number' ) ) ? SORT_NUMERIC : SORT_REGULAR;

					foreach ( $this->get_user_ids() as $id ) {
						$value = $column->get_sorting_value( $id );
						if ( $value || $this->show_all_results ) {
							$_users[ $id ] = $this->prepare_sort_string_value( $value );
						}
					}
				}
				break;

			// WooCommerce
			case 'column-wc-user-orders':
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_user_ids() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$_users[ $id ] = count( $value );
					}
				}
				break;
			case 'column-wc-user-order_count':
				$sort_flag = SORT_NUMERIC;
				break;


			case 'column-wc-user-total-sales':
				$sort_flag = SORT_NUMERIC;
				foreach ( $this->get_user_ids() as $id ) {
					$value = $column->get_sorting_value( $id );
					if ( $value || $this->show_all_results ) {
						$_users[ $id ] = $value;
					}
				}
				break;

			// Try to sort by raw value.
			default :
				$sort_flag = SORT_REGULAR;
				foreach ( $this->get_user_ids() as $id ) {
					$_users[ $id ] = $column->get_raw_value( $id );
				}

		endswitch;

		if ( isset( $sort_flag ) ) {

			// set sorting value
			if ( empty( $_users ) ) {
				foreach ( $this->get_user_ids() as $id ) {
					$value = $column->get_raw_value( $id );
					if ( $value || $this->show_all_results ) {
						$_users[ $id ] = $this->prepare_sort_string_value( $value );
					}
				}
			}

			// sorting
			if ( 'ASC' == $vars['order'] ) {
				asort( $_users, $sort_flag );
			} else {
				arsort( $_users, $sort_flag );
			}

			// alter orderby SQL
			if ( ! empty( $_users ) ) {
				global $wpdb;

				// for MU site compatibility
				$prefix = $wpdb->base_prefix;

				$column_names = implode( ',', array_keys( $_users ) );
				$user_query->query_where 	.= " AND {$prefix}users.ID IN ({$column_names})";
				$user_query->query_orderby 	= "ORDER BY FIELD({$prefix}users.ID,{$column_names})";
			}

			// cleanup the vars we dont need
			$vars['order']	 = '';
			$vars['orderby'] = '';
		}

		$user_query->query_vars = array_merge( $user_query->query_vars, $vars );
	}
}