<?php

/**
 * MTSW Class
 */
class MTSW {

	/**
  *
  * @var MTSW
  */
  private static $_instance;

  /**
  *
  * @var MTSW_global_options
  */
  public $global_options;

	/**
	* @var array Array of the public Post Types with at least one public hierarchical taxonomy
	*/
	private $mtsw_post_types;

	/**
	* @var array Array of the public Post Types slugs with at least one public hierarchical taxonomy
	*/
	private $mtsw_post_type_slugs;

	/**
	* @var array of array Array of the public Post Types slugs associated with an array of its public
	* 										hierarchicals taxonomies
	*/
	private $mtsw_taxonomies;

	/**
	* @var array of array Array of the public Post Types slugs associated with an array of it public
	*											hierarchicals taxonomies slugs
	*/
	private $mtsw_taxonomy_slugs;

	/**
	* @var array of array Array of the public hierarchicals taxonomies slugs associated 
	*											with an array of parent term ids with at least one child term
	*/
	private $mtsw_default_parent_term_ids = array();

	/**
	* @var array of array Array of the public hierarchicals taxonomies slugs associated 
	*											with an array of parent terms with at least one child term
	*/
	private $mtsw_default_parent_terms = array();

	/**
	* @var array of array of array Array of the public hierarchicals taxonomies slugs associated
	*															 with an array of parent terms ids with at least 
	*															 one child term id associated with their children
	*/
	private $mtsw_default_children_term_ids = array();

	/**
	* @var array of array of array Array of the public hierarchicals taxonomies slugs associated
	*															 with an array of parent terms ids with at least one child term
	*															 associated with their children
	*/
	private $mtsw_default_children_terms = array();

	/**
	*	Construct
	*/
	private function __construct() {

		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// all custom-post et taxonomies must have been created
		// and linked together before 'load_term' is called
		add_action( 'init', array( $this, 'load_term' ), 20 );

		add_action( 'plugins_loaded', array( $this, 'liste_plugin_load_text_domain' ) );
		add_action( 'widgets_init', create_function( '', 'return register_widget("MTSW_widget");') );

		add_filter( 'query_vars', array( $this, 'terms_queryvars' ) );
		add_action( 'parse_query', array( $this, 'parse' ) );
		add_filter( 'posts_where', array( $this, 'where' ), 20, 2 );

		add_action( 'generate_rewrite_rules', array( $this, 'add_rewrite_rules' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'mtsw_enqueue' ) );
	
		add_action( 'admin_menu', array( $this, 'admin_add_page' ) );
		add_action( 'admin_init', array( $this, 'settings_api_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'mtsw_admin_enqueue' ) );
		add_action( 'wp_ajax_post_type_change', array( $this, 'post_type_change_callback' ) );
		add_action( 'wp_ajax_taxonomy_change', array( $this, 'taxonomy_change_callback' ) );
		
		add_shortcode( 'mtsw', array( $this, 'shortcode_handler' ) );

		$this->global_options = new MTSW_global_options( get_option('mtsw_form') );
	}

	/**
  * Get the single instance
  *
  * @return MTSW
  */
	public static function getInstance() {
	  if( true === is_null( self::$_instance ) )
	    self::$_instance = new self();

	  return self::$_instance;
	}

	/**
	*	Getter
	*
	* @return array $mtsw_post_types
	*/
	public function get_mtsw_post_types() {
		if ( !isset( $this->mtsw_post_types ) ) {
			$this->init_mtsw_properties();
		}
		return $this->mtsw_post_types;
	}

	/**
	*	Getter
	*
	* @return array $mtsw_post_type_slugs
	*/
	public function get_mtsw_post_type_slugs() {
		if ( !isset( $this->mtsw_post_type_slugs ) ) {
			$this->init_mtsw_properties();
		}
		return $this->mtsw_post_type_slugs;
	}

	/**
	*	Getter
	*
	* @return array of array $mtsw_taxonomies
	*/
	public function get_mtsw_taxonomies() {
		if ( !isset( $this->mtsw_taxonomies ) ) {
			$this->init_mtsw_properties();
		}
		return $this->mtsw_taxonomies;
	}

	/**
	*	Getter
	*
	* @return array of array $mtsw_taxonomy_slugs
	*/
	public function get_mtsw_taxonomy_slugs() {
		if ( !isset( $this->mtsw_taxonomy_slugs ) ) {
			$this->init_mtsw_properties();
		}
		return $this->mtsw_taxonomy_slugs;
	}

	/**
	*	Getter
	*
	* @param string $taxonomy_slug
	* @return array of array $mtsw_default_parent_term_ids
	*/
	public function get_mtsw_default_parent_term_ids( $taxonomy_slug ) {
		if ( !isset( $this->mtsw_default_parent_term_ids[$taxonomy_slug] ) ) {
			$this->init_mtsw_properties();
		}
		return $this->mtsw_default_parent_term_ids[$taxonomy_slug];
	}

	/**
	* Test if a term id is in $mtsw_default_parent_term_ids
	*
	* @param string $taxonomy_slug
	* @param integer $term_id
	* @return boolean
	*/
	public function in_mtsw_default_parent_term_ids( $taxonomy_slug, $term_id ) {
		return in_array( $term_id, $this->get_mtsw_default_parent_term_ids( $taxonomy_slug ) );
	}

	/**
	*	Getter
	*
	* @param string $taxonomy_slug
	* @return array of array $mtsw_default_parent_terms
	*/
	public function get_mtsw_default_parent_terms( $taxonomy_slug ) {
		if ( !isset( $this->mtsw_default_parent_terms[$taxonomy_slug] ) ) {
			$this->init_mtsw_properties();
		}
		return $this->mtsw_default_parent_terms[$taxonomy_slug];
	}

	/**
	*	Getter
	*
	* @param string $taxonomy_slug
	* @return array of array $mtsw_default_children_term_ids
	*/
	public function get_mtsw_default_children_term_ids( $taxonomy_slug ) {
		if ( !isset( $this->mtsw_default_children_term_ids[$taxonomy_slug] ) ) {
			$this->init_mtsw_properties();
		}
		return $this->mtsw_default_children_term_ids[$taxonomy_slug];
	}

	/**
	* Test if a term id is in $mtsw_default_children_term_ids
	*
	* @param string $taxonomy_slug
	* @param integer $term_id
	* @return boolean
	*/
	public function in_mtsw_default_children_term_ids( $taxonomy_slug, $term_id ) {
		$mtsw_default_children_term_ids = $this->get_mtsw_default_children_term_ids( $taxonomy_slug );
		$merge_mtsw_default_children_term_id = array();
		foreach ( $mtsw_default_children_term_ids as $id => $children_term_ids ) {
			foreach ( $children_term_ids as $children_term_id )
				$merge_mtsw_default_children_term_id[] = $children_term_id;
		}
		return in_array( $term_id, $merge_mtsw_default_children_term_id );
	}

	/**
	*	Getter
	*
	* @param string $taxonomy_slug
	* @return array of array $mtsw_default_children_terms
	*/
	public function get_mtsw_default_children_terms( $taxonomy_slug ) {
		if ( !isset( $this->mtsw_default_children_terms[$taxonomy_slug] ) ) {
			$this->init_mtsw_properties();
		}
		return $this->mtsw_default_children_terms[$taxonomy_slug];
	}

	/**
	 * Retrieves children of taxonomy as Term IDs.
	 *
	 * @param string $taxonomy Taxonomy Name
	 * @return array returns children as Term IDs.
	 */
	private function get_term_hierarchy($taxonomy) {

		$children = array();
		$terms = get_terms($taxonomy, array('get' => 'all', 'orderby' => 'id', 'fields' => 'id=>parent'));
		foreach ( $terms as $term_id => $parent ) {
			if ( $parent > 0 )
				$children[$parent][] = $term_id;
		}

		return $children;
	}

	/**
	*	Initialize terms properties
	*
	* @param string $taxonomy_slug
	* @return boolean true if there's at least one parent and one child term
	*/
	private function init_mtsw_terms_properties( $taxonomy_slug ) {
		$taxonomy = get_taxonomy( $taxonomy_slug );
		if ( $children = $this->get_term_hierarchy( $taxonomy_slug ) ) {
			$this->mtsw_default_parent_term_ids[$taxonomy_slug] = array_keys( $children );
			$args = array(
				'hide_empty' => false,
				'include' => $this->mtsw_default_parent_term_ids[$taxonomy_slug],
				'parent' => 0);
			$this->mtsw_default_parent_terms[$taxonomy_slug] = get_terms( $taxonomy_slug, $args );
			$this->mtsw_default_children_term_ids[$taxonomy_slug] = array();
			$this->mtsw_default_children_terms[$taxonomy_slug] = array();
			foreach( $this->mtsw_default_parent_terms[$taxonomy_slug] as $term ) {
				$this->mtsw_default_children_term_ids[$taxonomy_slug][$term->term_id] = $children[$term->term_id];
				$args = array(
				'hide_empty' => false,
				'include' => $children[$term->term_id]);
				$this->mtsw_default_children_terms[$taxonomy_slug][$term->term_id] = get_terms( $taxonomy_slug, $args );
			}
			return (boolean)($this->mtsw_default_children_terms[$taxonomy_slug]);
		} else {
			return false;
		}
	}

	/**
	*	Initialize properties
	*/
	private function init_mtsw_properties() {

		$this->mtsw_post_types = array();
		$this->mtsw_post_type_slugs = array();
		$this->mtsw_taxonomies = array();
		$this->mtsw_taxonomy_slugs = array();

		$public_post_types = get_post_types( array('public' => true), 'objects' );

		if ( $public_post_types ) {

			foreach ( $public_post_types as $public_post_type ) {
				$taxonomies = array();
				$taxonomies_names = array();
				$post_type_taxonomies = get_object_taxonomies( $public_post_type->name, 'objects' );

				foreach ( $post_type_taxonomies as $post_type_taxonomy ) {
					if ( $post_type_taxonomy->public &&
							 $post_type_taxonomy->hierarchical &&
							 $this->init_mtsw_terms_properties( $post_type_taxonomy->name ) ) {
						$taxonomies[] = $post_type_taxonomy;
						$taxonomies_names[] = $post_type_taxonomy->name;
					}
				}

				if ( $taxonomies ) {
					$this->mtsw_post_types[] = $public_post_type;
					$this->mtsw_post_type_slugs[] = $public_post_type->name;
					$this->mtsw_taxonomies[$public_post_type->name] = $taxonomies;
					$this->mtsw_taxonomy_slugs[$public_post_type->name] = $taxonomies_names;
				}
			}

		}
	}

	/**
	 * Refresh WordPress rewrite rule cache to include the plugin rewrite rules
	 */
	function activate() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/**
	*	Build a list of term ids from an array of children term ids
	*
	* @param array of array Array of parent children term ids of children term ids
	* @return string
	*/
	private function list_term_ids( $children_term_ids ) {
		$list_term_ids = '';

		foreach ( $children_term_ids as $parent_id => $children_ids ) {
    	if ( $sub_list_term_ids = implode( ',', array_filter( $children_ids ) ) )
    		$list_term_ids .= $sub_list_term_ids . ';';
    }

    if ($list_term_ids)
	    	$list_term_ids = substr( $list_term_ids, 0, -1);

	  return $list_term_ids;
	}

	/**
	*	Build the url from $mtsw_options
	* if no url return ""
	*
	* @param MTSW_options
	* @return string
	*/
	private function url( $mtsw_options ) {
		$list_term_ids = $this->list_term_ids( $mtsw_options->children_term_ids );

		$wp_permalink_structure = get_option('permalink_structure');
		if ( $wp_permalink_structure != '' )
			$url = get_bloginfo('url') . '/' . $mtsw_options->post_type . '/' . $mtsw_options->taxonomy . '/terms/';
		else
			$url = get_bloginfo('url') . '/?post_type=' . $mtsw_options->post_type . '&taxonomy=' . $mtsw_options->taxonomy . '&terms=';
		
		if ( $list_term_ids ) {

			if ( $wp_permalink_structure != '' )
				$url .= $list_term_ids . '/search_type/' . $mtsw_options->def_search_type . '/order/' . $mtsw_options->order;
			else
				$url .= $list_term_ids . '&search_type=' . $mtsw_options->def_search_type . '&order=' . $mtsw_options->order;

    } else if ( $mtsw_options->blank_search_type != 'none' ) { // Blank search

      if ( $wp_permalink_structure != '' )
				$url .= 'all/search_type/' . $mtsw_options->def_search_type . '/order/' .  $mtsw_options->order;
			else
				$url .= 'all&search_type=' . $mtsw_options->def_search_type . '&order=' . $mtsw_options->order;

		} else {

			$url = "";

		}

		return $url;

	}

	/**
	 * Rewrite the URL by adding the variable 'terms'
	 * 
	 * @global string $wpmt_search_vars search type 'any' or 'all'
	 */
	function load_term() {       
	  if ( !empty( $_POST['mtsw-form'] ) ) {

	  	$mtsw_options = new MTSW_options( $_POST['mtsw-form'] );

	  	$url = $this->url( $mtsw_options );

	  	if ( $url ) {
	  		wp_redirect( $url );
	  		exit();
	  	} else {
	  		unset( $_POST );
	  	}
	    
	  } 
	}

	/**
	 * Add translation files from 'mtsw' domain
	 */
	function liste_plugin_load_text_domain() {
		load_plugin_textdomain('mtsw', false, 'multiple-term-selection-widget/languages/' );
	}

	/**
	 * Get Wordpress to parse URL for the variables 'terms' and 'search_type'
	 * 
	 * @param array $qvars query vars
	 * @return array query vars
	 */
	function terms_queryvars( $qvars ) {
		$qvars[] = 'terms';
		$qvars[] = 'search_type';
		return $qvars;
	}

	/**
	 * Modify the Wordpress parse query to take account of the query vars 'terms' and 'search_type'
	 * 
	 * @param WP_Query $vars
	 */
	function parse( $vars ) {
	  if ( !empty( $vars->query_vars['terms'] ) ) {
			$taxonomy = $vars->get('taxonomy');
	    $terms = $vars->get('terms');
	    $type = $vars->get('search_type');
	    $order = $vars->get('order');
			
			if ( $terms == 'all' ) {
				$tax_query = array(
					'relation' => 'AND',
					array(
						'taxonomy' => $taxonomy,
						'terms' => array(),
						'include_children' => true,
						'field' => 'term_id',
						'operator' => 'AND') );
			} else {
				$tax_query = array( 'relation' => strtoupper($type) );
				$term_groups = explode( ';', $terms );
				foreach ( $term_groups as $term_group ) {
					$tax_query[] = array(
							'taxonomy' => $taxonomy,
							'terms' => explode( ',', $term_group ),
							'include_children' => true,
							'field' => 'term_id',
							'operator' => 'AND');
				}
			}
			$vars->set('tax_query', $tax_query);
	        
	    if ( $order == 'title' ) {
        $vars->set( 'orderby', 'title' );
        $vars->set( 'order', 'asc' );
	    }
			
	  	$vars->is_home = false;
	   	$vars->is_archive = true;
	    $vars->is_post_type_archive = true;
			$vars->set('taxonomy', '');
			$vars->set('term', '');
	  }
	}

	/**
	 * Modify the Wordpress where query to take account of the query vars 'terms'
	 * 
	 * @param $where
	 * @return string $where
	 */
	function where( $where, $query ) {

		if ( !empty( $query->query_vars['terms'] ) ) {
			$mtsw_post_type_slugs = $this->get_mtsw_post_type_slugs();
			if ( !in_array( $query->query_vars['post_type'], $mtsw_post_type_slugs ) )
				return $where;

			global $wpdb;
			$matches = array();
			if ( preg_match( '/^(.*)( AND \( \(.+SELECT COUNT\(1\).+FROM ' . $wpdb->term_relationships . '.*AND object_id = ' . $wpdb->posts . '\.ID.+\) = \d+ \))(.*)$/s', $where, $matches ) ) {
				$mtsw_where = array();		
	  		$mtsw_taxonomy_slugs = $this->get_mtsw_taxonomy_slugs();
				$tax_queries = $query->tax_query;
				foreach( $tax_queries->queries as $tax_query ) {
					if ( !in_array( $tax_query['taxonomy'], $mtsw_taxonomy_slugs[$query->query_vars['post_type']]) )
						return $where;
					$mtsw_terms_where = array();
					$tax_query['terms'] = array_unique( (array) $tax_query['terms'] );
					foreach ( $tax_query['terms'] as $term ) {
						$terms = array();
						if ( $tax_query['include_children'] )
							$terms = get_term_children( $term, $tax_query['taxonomy'] );
						$terms[] = $term;
						$terms = implode( ',', array_map( 'intval', $terms ) );
						$terms = $wpdb->get_col( "
							SELECT term_taxonomy_id
							FROM $wpdb->term_taxonomy
							WHERE taxonomy = '{$tax_query['taxonomy']}'
							AND term_id IN ($terms)
						" );
						$terms = implode( ',', $terms );
						$mtsw_terms_where[] = "(
							SELECT COUNT(1)
							FROM $wpdb->term_relationships
							WHERE term_taxonomy_id IN ($terms)
							AND object_id = $wpdb->posts.ID
						) > 0";
					}
					$mtsw_where[] = ' ( ' . implode( ' OR ', $mtsw_terms_where ) . ' ) ';
				}
				$relation = $tax_queries->relation;
				if ( !in_array( $relation, array( 'OR', 'AND' ) ) )
					$relation = 'AND';			
				$mtsw_where = ' AND ( ' . implode( " $relation ", $mtsw_where ) . ' )';
				$where = $matches[1] . $mtsw_where . $matches[3];
			}
		}
		return $where;
	}

	/**
	 * Add rewrite rules for the variables 'terms' and 'search_type'
	 * 
	 * @param WP_Rewrite $wp_rewrite
	 */
	function add_rewrite_rules( $wp_rewrite ) {
		$new_rules = array( 
			'(.+?)/(.+?)/terms/(.+?)/search_type/(.+?)/order/(.+?)/page/(.+?)/?$' => 'index.php?post_type=' . $wp_rewrite->preg_index(1) . '&taxonomy=' . $wp_rewrite->preg_index(2) . '&terms=' .
				$wp_rewrite->preg_index(3).'&search_type=' . $wp_rewrite->preg_index(4).'&order='.$wp_rewrite->preg_index(5).'&paged='. $wp_rewrite->preg_index(6),
			'(.+?)/(.+?)/terms/(.+?)/search_type/(.+?)/order/(.+?)/?$' => 'index.php?post_type=' . $wp_rewrite->preg_index(1) . '&taxonomy=' . $wp_rewrite->preg_index(2) . '&terms=' .
				$wp_rewrite->preg_index(3).'&search_type=' . $wp_rewrite->preg_index(4).'&order='.$wp_rewrite->preg_index(5),     
			'(.+?)/(.+?)/terms/(.+?)/page/(.+?)/?$' => 'index.php?post_type=' . $wp_rewrite->preg_index(1) . '&taxonomy=' . $wp_rewrite->preg_index(2) . '&terms=' .
				$wp_rewrite->preg_index(3).'&paged=' . $wp_rewrite->preg_index(4),
			'(.+?)/(.+?)/terms/(.+?)/?$' => 'index.php?post_type=' . $wp_rewrite->preg_index(1) . '&taxonomy=' . $wp_rewrite->preg_index(2) . '&terms=' .
				$wp_rewrite->preg_index(3));
	  
		$wp_rewrite->rules = array_merge( $new_rules, $wp_rewrite->rules );	   
	}

	/**
	 * Script enqueue
	 */
	function mtsw_enqueue() {
		wp_enqueue_style( 'mtsw-css', MTSW_URL_STYLES . '/mtsw.css');
		if ( $this->global_options->select2 ) {
			wp_enqueue_style( 'mtsw-select2-css', MTSW_URL_STYLES . '/select2.css');
			wp_enqueue_script( 'mtsw-select2-script', MTSW_URL_SCRIPTS . '/select2.min.js', array( 'jquery' ), '3.4.5' );
			$locale = substr( get_bloginfo( 'language' ), 0, 2);
			if ( file_exists( MTSW_PATH . '/js/select2_locale_' . $locale . '.js' ) )
				wp_enqueue_script( 'mtsw-select2-locale-script', MTSW_URL_SCRIPTS . '/select2_locale_' . $locale . '.js', array( 'jquery', 'mtsw-select2-script' ), '3.4.5' );
			wp_enqueue_script( 'mtsw-script', MTSW_URL_SCRIPTS . '/mtsw.js', array( 'jquery', 'mtsw-select2-script' ) );
			wp_localize_script( 'mtsw-script', 'mtsw_object', array(
				'mtsw_select2_css' => $this->global_options->mtsw_select2_css ) );
		}
	}

	/**
	 * Add the settings page to the admin menu
	 */
	function admin_add_page() {
		add_options_page( __('Multiple Term Selection Widget', 'mtsw'), __('Multiple Term Selection Widget', 'mtsw'), 'administrator', 'mtsw_settings', array( $this, 'options_page') );		
	}

	/**
	 * Settings Page
	 */
	function options_page() {
		require_once( MTSW_PATH_VIEWS . '/admin_form.php' );
	}

	/**
	 * Settings Initialisation
	 */
	function settings_api_init() {
		register_setting( 'mtsw', 'mtsw_form' );
		add_settings_section( 'mtsw_form', __('Global Settings', 'mtsw'), '', 'mtsw' );
		add_settings_field( 'mtsw_form', '', array( $this, 'mtsw_form_default' ), 'mtsw', 'mtsw_form');
		register_setting( 'mtsw_default', 'mtsw_default_form' );
		add_settings_section( 'mtsw_default_form', __('Default Shortcode Settings', 'mtsw'), array( $this, 'shortcode_text' ), 'mtsw_default' );
		add_settings_field( 'mtsw_default_form', '', array( $this, 'mtsw_form_default_widget' ), 'mtsw_default', 'mtsw_default_form');
	}

	/**
	 * Settings Fields
	 */
	function mtsw_form_default() {
		$mtsw_global_options = new MTSW_global_options( get_option('mtsw_form') );

		require_once(MTSW_PATH_VIEWS . '/admin_global_settings.php');
	}

	/**
	 * Settings Fields of default widget
	 */
	function mtsw_form_default_widget() {
		global $mtsw;
		$mtsw_options = new MTSW_widget_options( get_option('mtsw_default_form') );

		require_once(MTSW_PATH_VIEWS . '/admin_settings_default_widget.php');
	}

	/**
	 * Text for the default Shortcode Settings
	 */
	function shortcode_text() {
		echo '<p>' . __('Use the shortcode [mtsw] to generate this default form on any page you wish.', 'mtsw') . '</p>';
	}

	/**
	 * Admin.js admin enqueue
	 */
	function mtsw_admin_enqueue($hook) {
		if ( in_array( $hook, array( 'settings_page_mtsw_settings', 'widgets.php' ) ) ) {
			wp_enqueue_style( 'mtsw-admin-css', MTSW_URL_STYLES . '/admin.css');
			wp_enqueue_script( 'mtsw-admin-script', MTSW_URL_SCRIPTS . '/admin.js', array('jquery', 'jquery-ui-sortable') );
		}
	}

	/**
	 * Admin.js ajax callback
	 */
	function post_type_change_callback() {
		$mtsw_post_type_slugs = $this->get_mtsw_post_type_slugs();
		if ( isset($_POST['post_type']) &&
				 in_array( $_POST['post_type'], $mtsw_post_type_slugs ) ) {
			foreach ($this->mtsw_taxonomies[$_POST['post_type']] as $taxonomy) : ?>
				<option value="<?php echo $taxonomy->name; ?>">
					<?php echo $taxonomy->labels->singular_name; ?>
				</option>
			<?php endforeach;
		}

		die();
	}

	/**
	 * Admin.js ajax callback
	 */
	function taxonomy_change_callback() {
		$mtsw_taxonomy_slugs = $this->get_mtsw_taxonomy_slugs();
		if ( isset( $_POST['post_type'] ) &&
				 isset( $_POST['taxonomy'] ) &&
				 in_array( $_POST['taxonomy'], $mtsw_taxonomy_slugs[$_POST['post_type']] ) &&
				 isset( $_POST['attr_pre_name'] ) ) { ?>
			<legend class="screen-reader-text">
				<span><?php _e('Included Terms', 'mtsw'); ?></span>
			</legend>
			<ul class="ui-sortable">
			<?php
				$mtsw_default_parent_terms = $this->get_mtsw_default_parent_terms( $_POST['taxonomy'] );
				$mtsw_default_children_terms = $this->get_mtsw_default_children_terms( $_POST['taxonomy'] );
				foreach ( $mtsw_default_parent_terms as $default_parent_term ) : ?>
				<li class="ui-state-default ui-state-disabled">
					<label>
						<input name="<?php echo $_POST['attr_pre_name']; ?>[included_parent_term_ids][]" type="checkbox" value="<?php echo $default_parent_term->term_id; ?>"/>
							<?php echo $default_parent_term->name; ?>
					</label>
					<fieldset class="included_children_terms" id="included_children_terms_<?php echo $default_parent_term->term_id; ?>" style="display:none;">
						<legend class="screen-reader-text">
							<span><?php printf( __('Included Children Terms of %s', 'mtsw'), $default_parent_term->name ); ?></span>
						</legend>
						<ul class="ui-sortable">
						<?php
							$pos = 0;
							foreach ( $mtsw_default_children_terms[$default_parent_term->term_id] as $default_children_term ) : ?>
							<li class="ui-state-default ui-state-disabled" data-position="<?php echo $pos; ?>">
								<label>
									<input name="<?php echo $_POST['attr_pre_name']; ?>[included_children_term_ids][]" type="checkbox" value="<?php echo $default_children_term->term_id; ?>"/>
									<?php echo $default_children_term->name; ?>
								</label>
							</li>
					<?php $pos++; endforeach; ?>
						</ul>
					</fieldset>
				</li>
			<?php endforeach; ?>
			</ul>
			<input name="<?php echo $_POST['attr_pre_name']; ?>[excluded_children_term_ids]" type="hidden" value="" /> <?php
		}

		die();
	}

	/**
	 * MTSW Shortcode
	 * 
	 * @param array $atts
	 * @param string $content
	 * @param string $code
	 */
	function shortcode_handler( $atts, $content=null, $code="" ) {
		$instance = get_option('mtsw_default_form');
		$instance = shortcode_atts( $instance, $atts );
		$args = apply_filters( 'mtsw_shortcode_args', array(
			'before_widget' => '',
			'after_widget' => '',
      'before_title' => '<h3>', 
      'after_title' => '</h3>') );
		 
		$mtsw_widget = new MTSW_widget();
		return $mtsw_widget->prepare_mtsw_form( $args, $instance );
	}
} ?>