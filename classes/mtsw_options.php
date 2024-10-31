<?php

/**
 * MTSW Options Class
 *
 * Check and construct options user choices
 *
 */
class MTSW_options {

	/**
	* @var string
	*/
	var $id;

	/**
	* @var string
	*/
	var $post_type;

	/**
	* @var string
	*/
	var $taxonomy;

	/**
	* @var array of array Array of parent term ids of children term ids
	*/
	var $children_term_ids;

	/**
	* @var string
	*/
	var $def_search_type;

	/**
	* @var string
	*/
	var $blank_search_type;

	/**
	* @var string
	*/
	var $order;

	/**
   * constructor
   *
   * @param array $options
   */
  function __construct( $options=array() ) {
  	global $mtsw;

  	$mtsw_post_type_slugs = $mtsw->get_mtsw_post_type_slugs();
  	$mtsw_taxonomy_slugs = $mtsw->get_mtsw_taxonomy_slugs();

  	$this->id = ( isset( $options['id'] ) ) ? esc_attr( $options['id'] ) : '';
		$this->post_type = ( isset( $options['post_type'] ) && in_array( $options['post_type'], $mtsw_post_type_slugs ) ) ? esc_attr( $options['post_type'] ) : $mtsw_post_type_slugs[0];
		$this->taxonomy = ( isset( $options['taxonomy'] ) && in_array( $options['taxonomy'], $mtsw_taxonomy_slugs[$this->post_type] ) ) ? esc_attr( $options['taxonomy'] ) : $mtsw_taxonomy_slugs[$this->post_type][0];
		if ( !empty( $options['children_term_ids'] ) ) {
			foreach ( $options['children_term_ids'] as $id => $array )
				$options['children_term_ids'][$id] = array_map( 'esc_attr', $options['children_term_ids'][$id] );
			$this->children_term_ids = $options['children_term_ids'];
		} else {
			$this->children_term_ids = array();
		}
		$this->def_search_type = ( isset( $options['def_search_type'] ) ) ? esc_attr( $options['def_search_type'] ) : 'and';
		$this->blank_search_type = ( isset( $options['blank_search_type'] ) ) ? esc_attr( $options['blank_search_type'] ) : 'none';
		$this->order = ( isset( $options['order'] ) ) ? esc_attr( $options['order'] ) : 'default';

  }

}