<?php

/**
 * MTSW Widget Options Class
 */
class MTSW_widget_options extends MTSW_options {

	/**
	* @var string
	*/
	var $title;

	/**
	* @var array Included parent term ids
	*/
	var $included_parent_term_ids;

	/**
	* @var array Default parent terms of the taxonomy ordered by the included parent term ids and after by name
	*/
	var $mtsw_parent_terms;

	/**
	* @var array Included parent terms of the taxonomy
	*/
	var $included_parent_terms;

	/**
	* @var array Included children term ids
	*/
	var $included_children_term_ids;

	/**
	* @var array Excluded children term ids
	*/
	var $excluded_children_term_ids;

	/**
	* @var array Default children terms of the taxonomy ($this->taxonomy) ordered by the included children terms and after by name
	*/
	var $mtsw_children_terms;

	/**
	* @var array Allowed children terms (from the widget admin choices) of the taxonomy ($this->taxonomy) ordered by the included children terms
	*/
	var $allowed_children_terms;

	/**
	* @var boolean
	*/
	var $automatically_add_children;

	/**
	* @var boolean
	*/
	var $multi_selection;

	/**
	* @var boolean
	*/
	var $user_choice;

	/**
	* @var boolean
	*/
	var $hide_empty;

	/**
	* @var string
	*/
	var $submit_button;

	/**
	* @var array
	*/
	var $right_children_term_ids;

	/**
   * constructor
   *
   * @param array $options
   */
  function __construct( $options=array() ) {
  	global $mtsw;

  	parent::__construct( $options );

  	$this->title = ( isset( $options['title'] ) ) ? esc_attr( $options['title'] ) : '';
		$this->included_parent_term_ids = ( !empty( $options['included_parent_term_ids'] ) ) ? array_map( 'esc_attr', $options['included_parent_term_ids'] ) : array();

		// remove included children term ids registered by the shortcode
		// if they are no longer valids
		$this->included_parent_term_ids = array_filter( $this->included_parent_term_ids, array( $this, 'in_mtsw_default_parent_term_ids') );

		$this->included_children_term_ids = ( !empty( $options['included_children_term_ids'] ) ) ? array_map( 'esc_attr', $options['included_children_term_ids'] ) : array();
		$this->excluded_children_term_ids = ( !empty( $options['excluded_children_term_ids'] ) ) ? explode( ',', esc_attr($options['excluded_children_term_ids']) ) : array();

		// remove included children term ids and excluded children term ids
		// registered by the shortcode if they are no longer valids
		$this->included_children_term_ids = array_filter( $this->included_children_term_ids, array( $this, 'in_mtsw_default_children_term_ids') );
		$this->excluded_children_term_ids = array_filter( $this->excluded_children_term_ids, array( $this, 'in_mtsw_default_children_term_ids') );

		$this->automatically_add_children = ( isset( $options['automatically_add_children'] ) ) ? esc_attr( $options['automatically_add_children'] ) : '1alpha';
		$this->multi_selection = ( isset( $options['multi_selection'] ) );
		$this->user_choice = ( isset( $options['user_choice'] ) );
		$this->hide_empty = ( isset( $options['hide_empty'] ) ) ? (boolean)intval( esc_attr( $options['hide_empty'] ) ) : true;
		$this->submit_button = ( !empty( $options['submit_button'] ) ) ? esc_attr( $options['submit_button'] ) : __('Search', 'mtsw');
		$this->ascendant_term_ids = array();

  }

  /**
	* Test if a term id is in $mtsw_default_parent_term_ids
	*
	* @param integer $term_id
	* @return boolean
	*/
	private function in_mtsw_default_parent_term_ids( $term_id ) {
		global $mtsw;

		return $mtsw->in_mtsw_default_parent_term_ids( $this->taxonomy, $term_id );
	}

	/**
	* Test if a term id is in $mtsw_default_parent_term_ids
	*
	* @param integer $term_id
	* @return boolean
	*/
	private function in_mtsw_default_children_term_ids( $term_id ) {
		global $mtsw;

		return $mtsw->in_mtsw_default_children_term_ids( $this->taxonomy, $term_id );
	}

  /**
	*	Return the default parent terms of the taxonomy ($this->taxonomy) ordered by
	* the included parent terms and after by name
	*
	* @return array Array of Terms
	*/
  function get_mtsw_parent_terms() {
  	if ( !isset ( $this->mtsw_parent_terms ) ) {
	  	global $mtsw;

	  	$mtsw_default_parent_terms = $mtsw->get_mtsw_default_parent_terms( $this->taxonomy );
	  	$flip_included_parent_term_ids = array_flip( $this->included_parent_term_ids );
	  	$mtsw_parent_terms = array();
	  	if ( $nb_flip_included_parent_term_ids = count($flip_included_parent_term_ids) )
				$mtsw_parent_terms = array_fill( 0, $nb_flip_included_parent_term_ids, 0 );

	  	foreach( $mtsw_default_parent_terms as $term ) {
	  		if ( isset( $flip_included_parent_term_ids[$term->term_id] ) )
	  			$mtsw_parent_terms[$flip_included_parent_term_ids[$term->term_id]] = $term;
	  		else
	  			$mtsw_parent_terms[] = $term;
	  	}

	  	$this->mtsw_parent_terms = $mtsw_parent_terms;
	  }
	  return $this->mtsw_parent_terms;
  }

  /**
	*	Return the included parent terms of the taxonomy ($this->taxonomy)
	*
	* @return array Array of Terms
	*/
  function get_included_parent_terms() {
  	if ( !isset ( $this->included_parent_terms ) ) {
	  	$nb_included_parent_term_ids = count($this->included_parent_term_ids);
	  	$mtsw_parent_terms = $this->get_mtsw_parent_terms();
	  	$this->included_parent_terms = array_slice( $mtsw_parent_terms, 0, $nb_included_parent_term_ids );
	  }
	  return $this->included_parent_terms;
  }

  /**
	*	Sort the indexed terms
	* Put the alloawed children terms in first
	* Do not maintain include children term ids order
	*
	* @param array of array $indexed_terms Array of array of an integer (position) and term
	*/
  private function alphabetical_order_mtsw_children_indexed_term_sort( $indexed_terms ) {
  	$not_excluded_indexed_terms = array();
  	$excluded_indexed_terms = array();
  	foreach( $indexed_terms as $indexed_term ) {
  		if ( !in_array( $indexed_term[1]->term_id, $this->excluded_children_term_ids ) )
  			$not_excluded_indexed_terms[] = $indexed_term;
  		else
  			$excluded_indexed_terms[] = $indexed_term;
  	}
  	return array_merge( $not_excluded_indexed_terms, $excluded_indexed_terms );
  }

  /**
	*	Compare $a and $b.
	* If the term_id of the terms of $a and $b are not in the included children terms
	* 	return the string comparaison of their positions
	* If only one of them is in the included children terms
	* 	return the result of a comparaison where the only one is the smaller
	* If the two are in the included_children_term_ids
	* 	return the string comparaison of their positions in the included children terms
	*
	* @param array $a Array of an integer (position) and term
	* @param array $b Array of an integer (position) and term
	* @return integer -1 or 1
	*/
  private function cmp_mtsw_children_indexed_term( $a, $b ) {
  	$flip_included_children_term_ids = array_flip( $this->included_children_term_ids );

  	$is_flip_a = ( isset( $flip_included_children_term_ids[$a[1]->term_id] ) );
  	$is_flip_b = ( isset( $flip_included_children_term_ids[$b[1]->term_id] ) );

  	if ( !$is_flip_a && !$is_flip_b )
  		return ( $a[0] < $b[0] ) ? -1 : 1;
  	
  	if ( $is_flip_a && !$is_flip_b )
  		return -1;
  	
  	if ( !$is_flip_a && $is_flip_b )
  		return 1;

  	return ( $flip_included_children_term_ids[$a[1]->term_id] < $flip_included_children_term_ids[$b[1]->term_id] ) ? -1 : 1;
  }

  /**
	*	Return the default children terms of the taxonomy ($this->taxonomy) ordered by
	* the allowed children terms and after by name
	*
	* @return array of array Array of parent terms id associated with children terms (array of the initial position and term object)
	*/
  function get_mtsw_children_terms() {
  	if ( !isset ( $this->mtsw_children_terms ) ) {
	  	global $mtsw;
	  	$mtsw_children_terms = array();

	  	$mtsw_default_children_terms = $mtsw->get_mtsw_default_children_terms( $this->taxonomy );

	  	$children_term_ids = array_merge( $this->included_children_term_ids, $this->excluded_children_term_ids );
	  	foreach( $mtsw_default_children_terms as $id => $terms ) {
	  		$indexed_children_terms = array();
	  		$i = 0;
	  		$new_children = false;
	  		foreach( $terms as $term ) {
	  			if ( !$new_children && !in_array( $term->term_id, $children_term_ids ) )
	  				$new_children = true;
	  			$indexed_children_terms[] = array( $i, $term );
	  			$i++;
	  		}
	  		if ( $new_children && ( $this->automatically_add_children == '1alpha' ) )
	  			$indexed_children_terms = $this->alphabetical_order_mtsw_children_indexed_term_sort( $indexed_children_terms );
	  		else
	  			usort( $indexed_children_terms, array( $this, "cmp_mtsw_children_indexed_term" ) );
	  		$mtsw_children_terms[$id] = array();
	  		foreach( $indexed_children_terms as $term )
	  			$mtsw_children_terms[$id][] = array( 'position' => $term[0], 'term' => $term[1] );
	  	}

	  	$this->mtsw_children_terms = $mtsw_children_terms;
	  }
	  return $this->mtsw_children_terms;
  }

  /**
	*	Test if the term is allowed :
	* if automatically_add_children, id not in the excluded children term ids,
	* else id in the included children term ids
	* if hide_empty, count > 0
	* 
	* @param object $term
	* @return boolean
	*/
  function is_term_allowed( $term ) {
  	if ( $this->hide_empty && $term->count == 0 )
  		return false;
  	if ( preg_match( '/^1/', $this->automatically_add_children ) )
  		return ( !in_array( $term->term_id, $this->excluded_children_term_ids ) );
  	else
  		return ( in_array( $term->term_id, $this->included_children_term_ids ) );
  }

  /**
	*	Test if the children term is allowed
	* 
	* @param array $term (array of the initial position and term object)
	* @return boolean
	*/
  function is_children_term_allowed( $term ) {
  	return $this->is_term_allowed( $term['term'] );
  }

  /**
	*	Return the included children terms of the taxonomy ($this->taxonomy)
	*
	* @return array of array Array of parent terms id associated with children terms (array of the initial position and term object)
	*/
  function get_allowed_children_terms() {
  	if ( !isset ( $this->allowed_children_terms ) ) {
	  	$mtsw_children_terms = $this->get_mtsw_children_terms();
	  	foreach( $mtsw_children_terms as $id => $terms )
	  		$mtsw_children_terms[$id] = array_filter( $mtsw_children_terms[$id], array( $this, 'is_children_term_allowed' ) );
	  	$this->allowed_children_terms = $mtsw_children_terms;
	  }
	  return $this->allowed_children_terms;
  }

  /**
	*	Return the term id of the allowed children term
	*
	* @param array $term (array of the initial position and term object)
	* @return int
	*/
  private function get_allowed_children_term_id( $term ) {
  	return $term['term']->term_id;
  }

  /**
	*	Return the right children term id : its id or parent id is included in the included parent term ids
	* else return 0
	*
	* @param object $term
	* @return int
	*/
  private function get_right_children_term_id( $term ) {
  	if ( !isset( $this->right_children_term_ids[$term->term_id] ) ) {
  		if ( in_array( $term->term_id, $this->included_parent_term_ids ) ) {
  			$this->right_children_term_ids[$term->term_id] = $term->term_id;
  		} else if ( $term->parent == 0 ) {
  			$this->right_children_term_ids[$term->term_id] = 0;
  		} else if ( in_array( $term->parent, $this->included_parent_term_ids ) ) {
  			$this->right_children_term_ids[$term->term_id] = $term->term_id;
  		} else {
  			$parent_term = get_term_by( 'id', $term->parent, $this->taxonomy );
  			$this->right_children_term_ids[$term->term_id] = $this->get_right_children_term_id( $parent_term );
  		}
  	}
  	return $this->right_children_term_ids[$term->term_id];
  }

	/**
	*	Return the non empty children term ids from the selected term ids
	*
	* @param array $selected_term_ids Array of the selected term ids
	* @return array of array Array of parent terms id associated with non empty children term ids
	*/
  function get_non_empty_children_term_ids( $selected_term_ids ) {
  	
  	$included_parent_term_ids = $this->included_parent_term_ids;
  	$allowed_children_terms = $this->get_allowed_children_terms();
  	$non_empty_children_term_ids = array();
  	$allowed_children_term_ids = array();
  	foreach ( $included_parent_term_ids as $included_parent_term_id ) {
  		$non_empty_children_term_ids[$included_parent_term_id] = $allowed_children_term_ids[$included_parent_term_id] = array_map( array( $this, 'get_allowed_children_term_id'), $allowed_children_terms[$included_parent_term_id] );
  	}

  	if ( !$selected_term_ids )
  		return $non_empty_children_term_ids;
  	
  	$selected_children_term_ids = array();
  	foreach ( $included_parent_term_ids as $included_parent_term_id ) {
  		$selected_children_term_ids[$included_parent_term_id] = array_intersect( $allowed_children_term_ids[$included_parent_term_id], $selected_term_ids );
  	}

  	foreach ( $included_parent_term_ids as $included_parent_term_id ) {

  		$list_term_ids = '';
			foreach ( $selected_children_term_ids as $parent_id => $children_ids ) {
	    	if ( ( $parent_id != $included_parent_term_id ) &&
	    			 ( $sub_list_term_ids = implode( ',', array_filter( $children_ids ) ) ) )
	    		$list_term_ids .= $sub_list_term_ids . ';';
	    }
			if ( $list_term_ids )
	    	$list_term_ids = substr( $list_term_ids, 0, -1);

  		if ( $list_term_ids ) {
  			$the_query = new WP_Query( array(
					'post_type' => $this->post_type,
					'taxonomy' => $this->taxonomy,
					'terms' => $list_term_ids,
					'search_type' => $this->def_search_type,
					'order' => $this->order) );
  			$query_post_term_ids = array();
  			foreach ( $the_query->posts as $post ) {
  				$query_post_term_ids = array_merge( $query_post_term_ids, array_map( array( $this, 'get_right_children_term_id'), get_the_terms( $post->ID, $this->taxonomy ) ) ); 
  			}
  			$query_post_term_ids = array_unique( $query_post_term_ids );
  			$non_empty_children_term_ids[$included_parent_term_id] = array_intersect( $non_empty_children_term_ids[$included_parent_term_id], $query_post_term_ids );
  		}
  		
  	}

  	return $non_empty_children_term_ids;
  }

}