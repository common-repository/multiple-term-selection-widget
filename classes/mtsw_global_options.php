<?php

/**
 * MTSW Global Options Class
 */
class MTSW_global_options {

	/**
	* @var boolean
	*/
	var $select2;

	/**
	* @var boolean MTSW css for select2
	*/
	var $mtsw_select2_css;

	/**
   * constructor
   *
   * @param array $options
   */
  function __construct( $options=array() ) {

		$this->select2 = ( isset( $options['select2'] ) ) ? (boolean)intval( esc_attr( $options['select2'] ) ) : true;
		$this->mtsw_select2_css = ( isset( $options['mtsw_select2_css'] ) ) ? ( (boolean)intval( esc_attr( $options['mtsw_select2_css'] ) ) && $this->select2 ) : $this->select2;

  }

}