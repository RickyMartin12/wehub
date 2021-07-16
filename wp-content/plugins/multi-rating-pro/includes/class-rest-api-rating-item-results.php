<?php

/**
 * 
 * REST API controller for rating item results
 * @author dpowney
 *
 */
class MRP_REST_API_Rating_Item_Results extends MRP_REST_API_Common {
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		add_filter( 'mrp_rest_api_rating_item_results_sanitize_params', array( $this, 'sanitize_parameters' ), 10, 2 );
		parent::__construct();
		
	}
	
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		
		$version = '1';
		$namespace = 'mrp/v' . $version;
		$base = 'rating-item-results';
		
		register_rest_route( $namespace, '/' . $base, array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'            => array(
								'post_id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'rating_form_id' =>  array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'limit' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'taxonomy' => array(
										'validate_callback' => array( $this, 'is_not_empty_value' )
								),
								'term_id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'rating_entry_ids' => array(
										'validate_callback' => array( $this, 'is_numeric_array_values' )
								),
								'sort_by' => array(
										'validate_callback' => array( $this, 'is_not_empty_value' )
								),
								'user_roles' => array(
										'validate_callback' => array( $this, 'is_string_array_values' )
								),
								'rating_item_ids' => array(
										'validate_callback' => array( $this, 'is_numeric_array_values' )
								),
								'to_date' => array(
										'validate_callback' => array( $this, 'is_date_value' )
								),
								'from_date' => array(
										'validate_callback' => array( $this, 'is_date_value' )
								),
								'offset' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'post_ids' => array(
										'validate_callback' => array( $this, 'is_numeric_array_values' )
								),
								'user_id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'entry_status' => array(
										'validate_callback' => array( $this, 'is_entry_status_value' )
								)
						)
				)
		) );
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'            => array(
								'context'          => array(
										'default'      => 'view',
								),
						),
				) 
		));
		
	}
	
	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		
		$allowed_parameters = array( 'rating_form_id', 'post_id', 'rating_item_ids', 'user_roles', 
				'rating_entry_ids', 'entry_status', 'to_date', 'from_date', 'comments_only', 
				'taxonomy', 'term_id', 'post_ids', 'sort_by' );
		
		$parameters = apply_filters( 'mrp_rest_api_rating_item_results_sanitize_params', $request->get_query_params(), $allowed_parameters );
		
		$post_id = isset( $parameters['post_id'] ) ? $parameters['post_id'] : null;
		$rating_form_id = isset( $parameters['rating_form_id'] ) ? $parameters['rating_form_id'] : null;
		$rating_item_ids = isset( $parameters['rating_item_ids'] ) ? $parameters['rating_item_ids'] : null;
		
		// only cares about post_id, rating_form_id and rating_item_id
		$rating_items = MRP_Multi_Rating_API::get_rating_items( array(
				'post_id' => $post_id,
				'rating_form_id' => $rating_form_id,
				'rating_item_ids' => $rating_item_ids
		) );
		
		$rating_item_results = array();
		foreach ( $rating_items as $rating_item ) {
			
			$rating_item_result = $this->get_rating_item_result( $rating_item, $parameters );
			
			array_push( $rating_item_results, $rating_item_result );
		}
		
		// sort by
		if ( isset( $parameters['sort_by'] ) && $parameters['sort_by'] == 'highest_rated' ) {
			uasort( $rating_item_results, 'mrp_sort_highest_rated_rating_items' );
		}
		
		return new WP_REST_Response( $rating_item_results, 200 );
	}
	
	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
	
		$parameters = $request->get_url_params();
		$rating_item_id = $parameters['id'];
		
		$allowed_parameters = array( 'rating_form_id', 'post_id', 'user_roles',
				'rating_entry_ids', 'entry_status', 'to_date', 'from_date', 'comments_only',
				'taxonomy', 'term_id', 'post_ids' );
		
		$parameters = apply_filters( 'mrp_rest_api_rating_item_results_sanitize_params', $request->get_query_params(), $allowed_parameters );
		
		$rating_items = MRP_Multi_Rating_API::get_rating_items( array(
				'rating_item_ids' => $rating_item_id
		) );
		
		$rating_item_result = $this->get_rating_item_result( $rating_items[$rating_item_id], $parameters );
		
		return new WP_REST_Response( $rating_item_result, 200 );
	}
	
	/**
	 * Gets rating item result
	 * 
	 * @param unknown $rating_item 
	 * @param array $parameters
	 */
	function get_rating_item_result( $rating_item, $parameters ) {
		
		$rating_result = MRP_Multi_Rating_API::get_rating_item_result(
				array_merge( $parameters, array( 'rating_item' => $rating_item ) )
		);
		
		return $rating_result;
	}
		
}