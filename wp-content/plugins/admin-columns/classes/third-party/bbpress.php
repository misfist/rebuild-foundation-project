<?php

class cpac_bbpress_support {

	private $post_types;

	function __construct() {

		$this->post_types = array( 'topic', 'forum', 'reply' );

		// remove the old filter which disabled bbpress support
		remove_filter( 'cac/post_types', 'cpac_posttypes_remove_bbpress' );

		// init
		add_action('cac/loaded', array( $this, 'init' ) );

		// add bbpress menu type
		add_filter( 'cac/menu_types', array( $this, 'add_menu_type' ) );
		add_filter( 'cac/storage_models', array( $this, 'set_menu_type' ) );

		// default names
		add_filter( 'cac/columns/defaults', array( $this, 'default_column_names' ), 10, 3 );
	}

	public function init( $cpac ) {

		// The bail() method from bbpress looks for $current_screen->post_type. This will fake the post_type var.
		if ( class_exists( 'bbPress', false ) && ( $cpac->is_settings_screen() || $cpac->is_doing_ajax() ) ) {
			add_filter( 'manage_topic_posts_columns', array( $this, 'set_topic_screen' ), 9 );
			add_filter( 'manage_forum_posts_columns', array( $this, 'set_forum_screen' ), 9 );
			add_filter( 'manage_reply_posts_columns', array( $this, 'set_reply_screen' ), 9 );

			add_filter( 'manage_topic_posts_columns', array( $this, 'reset_screen' ), 11 );
			add_filter( 'manage_forum_posts_columns', array( $this, 'reset_screen' ), 11 );
			add_filter( 'manage_reply_posts_columns', array( $this, 'reset_screen' ), 11 );
		}
	}

	public function add_menu_type( $menu_types ) {
		$menu_types['bbpress'] = __( 'bbPress', 'codepress-admin-columns' );
		return $menu_types;
	}

	private function set_screen( $post_type ) {
		global $current_screen;
		$current_screen = new stdclass;
		$current_screen->post_type = $post_type;
	}

	public function reset_screen( $headers ) {
		global $current_screen;
		$current_screen = null;
		return $headers;
	}

	public function set_topic_screen( $headers ) {
		$this->set_screen( 'topic' );
		return $headers;
	}
	public function set_forum_screen( $headers ) {
		$this->set_screen( 'forum' );
		return $headers;
	}
	public function set_reply_screen( $headers ) {
		$this->set_screen( 'reply' );
		return $headers;
	}
	public function set_menu_type( $storage_models ) {
		if ( class_exists( 'bbPress', false ) ) {
			foreach ( $storage_models as $k => $storage_model ) {
				if ( in_array( $storage_model->get_post_type(), $this->post_types ) ) {
					$storage_models[ $k ] = $storage_model->set_menu_type( 'bbpress' );
				}
			}
		}
		return $storage_models;
	}
	public function default_column_names( $column_names, $column, $storage_model ) {
		$columns = array();
		switch ( $storage_model->get_post_type() ) {
			case 'forum' :
				$columns = array(
					'author',
					'bbp_forum_topic_count',
					'bbp_forum_reply_count',
					'bbp_forum_created',
					'bbp_forum_freshness'
				);
				break;
			case 'topic' :
				$columns = array(
					'title',
					'bbp_topic_forum',
					'bbp_topic_reply_count',
					'bbp_topic_voice_count',
					'bbp_topic_author',
					'bbp_topic_created',
					'bbp_topic_freshness'
				);
				break;
			case 'reply' :
				$columns = array(
					'title',
					'bbp_reply_forum',
					'bbp_reply_topic',
					'bbp_reply_author',
					'bbp_reply_created'
				);
				break;
		}

		return array_merge( (array) $column_names, $columns );
	}
}
new cpac_bbpress_support;