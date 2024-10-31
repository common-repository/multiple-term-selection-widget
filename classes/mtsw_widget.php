<?php

/**
 * MTSW Widget Class
 */
class MTSW_widget extends WP_Widget {
    
  /**
   * constructor
   */
  function __construct() {
    parent::__construct( false, $name = __('Multi-Term-Selection', 'mtsw') );	
  }

  /**
   * @see WP_Widget::widget
   */
  function widget($args, $instance) {	   
    echo $this->prepare_mtsw_form($args, $instance);      
  }

  /**
   * @see WP_Widget::update
   */
  function update($new_instance, $old_instance) {				
    return $new_instance;
  }

  /**
   * @see WP_Widget::form
   */
  function form($instance) {
    global $mtsw;

    if ( $mtsw_post_types = $mtsw->get_mtsw_post_types() ) {

      $mtsw_widget_options = new MTSW_widget_options( $instance );
      require(MTSW_PATH_VIEWS . '/admin_widget.php');

    } else {

      echo '<p><b>' . __('No Post Types can be used. You need at least one hierarchical taxonomy (with at least one parent and one child terms) related to one post type to use Multiple Term Selection Widget !', 'mtsw') . '</b></p>';

    }
  }

  /**
   * Prepare the form
   * 
   * @see MTSW_widget::widget
   */
  function prepare_mtsw_form($args, $instance) {
    global $mtsw;

    if ( !$mtsw->get_mtsw_post_types() || !$instance )
      return '';

    $mtsw_widget_options = new MTSW_widget_options( $instance );
    
    if ( !$mtsw_widget_options->included_parent_term_ids )
      return '';

    $included_parent_terms = $mtsw_widget_options->get_included_parent_terms();
    $allowed_children_terms = $mtsw_widget_options->get_allowed_children_terms();
    if ( ( $terms = get_query_var( 'terms' ) ) &&
         ( $terms != 'all' ) &&
         ( $mtsw_widget_options->post_type == get_query_var( 'post_type' ) ) ) {
      $selected_term_ids = explode( ',', strtr( $terms, ';', ',' ) );
    } else {
      $selected_term_ids = array();
    }

    if ( $mtsw_widget_options->post_type == get_query_var( 'post_type' ) ) {
      if ( !( $search_type = get_query_var( 'search_type' ) ) )
        $search_type = $mtsw_widget_options->def_search_type;
    } else {
      $search_type = $mtsw_widget_options->def_search_type;
    }

    if ( $mtsw_widget_options->hide_empty && $search_type == 'and' ) {
      $non_empty_children_term_ids = $mtsw_widget_options->get_non_empty_children_term_ids( $selected_term_ids );
    }

    extract( $args );
    ob_start();
    require(MTSW_PATH_VIEWS . '/widget.php');
    $html = ob_get_contents();
    ob_end_clean();

    return $html;

  }

}

?>