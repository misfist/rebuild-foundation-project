<?php
/**
 * CPAC_Column_Post_Excerpt
 *
 * @since 2.0.0
 */
class CPAC_Column_Term_Excerpt extends CPAC_Column {

	public function init() {

		parent::init();

		// define properties
		$this->properties['type']	= 'column-excerpt';
		$this->properties['label']	= __( 'Excerpt', 'codepress-admin-columns' );

		// define additional options
		$this->options['excerpt_length'] = 30;
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0.0
	 */
	public function get_value( $term_id ) {
		$raw_value = $this->get_raw_value( $term_id );
		return $this->get_shortened_string( $raw_value );
	}

	/**
	 * @see CPAC_Column::get_raw_value()
	 * @since 2.0.3
	 */
	public function get_raw_value( $term_id ) {
		return $this->get_term_field( 'description', $term_id, $this->storage_model->taxonomy );
	}

	/**
	 * @see CPAC_Column::display_settings()
	 * @since 2.0.0
	 */
	public function display_settings() {

		$this->display_field_excerpt_length();
	}
}