<?php

/**
 * Addon class
 *
 * @since 3.5
 */
class CAC_Filtering_Model_User extends CAC_Filtering_Model {

	/**
	 * Constructor
	 *
	 * @since 3.5
	 */
	public function __construct( $storage_model ) {

		parent::__construct( $storage_model );

		// handle filtering request
		add_action( 'pre_get_users', array( $this, 'handle_filter_requests'), 2 );

		// add dropdowns
		add_action( 'restrict_manage_users', array( $this, 'add_filtering_dropdown' ) );
		add_action( 'restrict_manage_users', array( $this, 'add_filtering_buttom' ), 11 ); // placement after dropdowns
	}

	/**
	 * Enable filtering
	 *
	 * @since 3.5
	 */
	public function enable_filtering( $columns ) {

		$include_types = array(

			// WP default columns
			'email',
			'role',
			'username',

			// Custom columns
			'column-first_name',
			'column-last_name',
			'column-rich_editing',
			'column-user_registered',
			'column-user_url',

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
	public function filter_by_email( $query ) {
		$query->query_where .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->users}.user_email = %s", $this->get_filter_value( 'email' ) );
		return $query;
	}
	public function filter_by_username( $query ) {
		$query->query_where .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->users}.user_login = %s", $this->get_filter_value( 'username' ) );
		return $query;
	}

	/**
	 * Filter by custom columns
	 *
	 * @since 3.5
	 */
	public function filter_by_user_registered( $query ) {
		$query->query_where .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->users}.user_registered LIKE %s", $this->get_filter_value( 'column-user_registered' ) . '%' );
		return $query;
	}
	public function filter_by_user_url( $query ) {
		$query->query_where .= ' ' . $this->wpdb->prepare( "AND {$this->wpdb->users}.user_url = %s", $this->get_filter_value( 'column-user_url' ) );
		return $query;
	}

	/**
	 * Handle filter request
	 *
	 * @since 3.5
	 */
	public function handle_filter_requests( $user_query ) {

		global $pagenow;

		if ( $this->storage_model->page . '.php' != $pagenow || empty( $_REQUEST['cpac_filter'] ) || ! isset ( $_GET['cpac_filter_action'] ) ) {
			return $user_query;
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
				case 'email' :
					add_filter( 'pre_user_query', array( $this, 'filter_by_email' ) );
					break;

				case 'role' :
					$user_query->set( 'role', $value );
					break;

				case 'username' :
					add_filter( 'pre_user_query', array( $this, 'filter_by_username' ) );
					break;

				// Custom
				case 'column-first_name' :
					$user_query->set( 'meta_query', array(
						array(
							'key' => 'first_name',
							'value' => $meta_value,
							'compare' => $meta_query_compare
						)
					));
					break;

				case 'column-last_name' :
					$user_query->set( 'meta_query', array(
						array(
							'key' => 'last_name',
							'value' => $meta_value,
							'compare' => $meta_query_compare
						)
					));
					break;

				case 'column-rich_editing' :
					$user_query->set( 'meta_query', array(
						array(
							'key' => 'rich_editing',
							'value' => '1' === $value ? 'true' : 'false',
							'compare' => '='
						)
					));
					break;

				case 'column-user_registered' :
					add_filter( 'pre_user_query', array( $this, 'filter_by_user_registered' ) );
					break;

				case 'column-user_url' :
					add_filter( 'pre_user_query', array( $this, 'filter_by_user_url' ) );
					break;

				// Custom Fields
				case 'column-meta' :
					$user_query->set( 'meta_query', array(
						array(
							'key' => $column->options->field,
							'value' => $meta_value,
							'compare' => $meta_query_compare
						)
					));
					break;

				// ACF
				case 'column-acf_field' :
					if ( method_exists( $column, 'get_field' ) && ( $acf_field_obj = $column->get_field() ) ) {
						$user_query->set( 'meta_query', array(
							array(
								'key' => $acf_field_obj['name'],
								'value' => $meta_value,
								'compare' => $meta_query_compare
							)
						));
					}
					break;

				// Try to filter by using the column's custom defined filter method
				default :
					if ( method_exists( $column, 'get_filter_user_vars' ) ) {
						$column->set_filter( $this );
						$user_query = $column->get_filter_user_vars( $user_query );
					}

			endswitch;

		}

		return $user_query;
	}

	/**
	 * Get values by user field
	 *
	 * @since 3.5
	 */
	public function get_values_by_user_field( $user_field ) {

		$options = array();

		$user_field = sanitize_key( $user_field );

		$sql = "
			SELECT DISTINCT {$user_field}
			FROM {$this->wpdb->users}
			WHERE $user_field <> ''
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
			FROM {$this->wpdb->usermeta} um
			INNER JOIN {$this->wpdb->users} u ON um.user_id = u.ID
			WHERE um.meta_key = %s
			AND um.meta_value != ''
			ORDER BY 1
		";

		$values = $this->wpdb->get_results( $this->wpdb->prepare( $sql, $meta_key ), ARRAY_N );

		if ( is_wp_error( $values ) || ! $values ) {
			return array();
		}

		return $values;
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
			case 'email' :
				$empty_option = true;
				if ( $values = $this->get_values_by_user_field( 'user_email' ) ) {
					foreach ( $values as $value ) {
						$options[ $value[0] ] = $value[0];
					}
				}
				break;

			case 'role' :
				$empty_option = true;
				$roles = new WP_Roles();
				foreach ( $this->get_user_ids() as $id ) {
					$u = get_userdata( $id );
					if ( ! empty( $u->roles[0] ) ) {
						$options[ $u->roles[0] ] = $roles->roles[ $u->roles[0] ]['name'];
					}
				}
				break;

			case 'username' :
				$empty_option = true;
				if ( $values = $this->get_values_by_user_field( 'user_login' ) ) {
					foreach ( $values as $value ) {
						$options[ $value[0] ] = $value[0];
					}
				}
				break;

			// Custom
			case 'column-rich_editing' :
				$options = array(
					0 => __( 'No' ),
					1 => __( 'Yes' ),
				);
				break;

			case 'column-user_registered' :
				$order = '';
				foreach ( $this->get_user_ids() as $id ) {
					$registered_date = $column->get_raw_value( $id );
					$date = substr( $registered_date, 0, 7 ); // only year and month
					$options[ $date ] = date_i18n( 'F Y', strtotime( get_date_from_gmt( $registered_date ) ) );
				}
				krsort( $options );
				break;


			case 'column-user_url' :
				$empty_option = true;
				if ( $values = $this->get_values_by_user_field( 'user_url' ) ) {
					foreach ( $values as $value ) {
						$options[ $value[0] ] = $value[0];
					}
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

			default :
				if ( method_exists( $column, 'get_filter_options' ) ) {
					$options = $column->get_filter_options();
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

	/**
	 * @since 3.5
	 */
	public function add_filtering_buttom() {
		if ( $this->has_dropdown ) : ?>
		<input type="submit" name="cpac_filter_action" class="button" value="<?php _e ( 'Filter' ); ?>">
		<?php endif;
	}
}