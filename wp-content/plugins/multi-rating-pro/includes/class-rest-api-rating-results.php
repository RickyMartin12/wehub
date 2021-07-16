<?php

/**
 * 
 * REST API controller for rating results
 * @author dpowney
 *
 */
class MRP_REST_API_Rating_Results extends MRP_REST_API_Common {
	
	/**
	 * Constructor
	 */
	function __construct() {
		add_filter( 'mrp_rest_api_rating_results_sanitize_params', array( $this, 'sanitize_parameters' ), 10, 2 );
		parent::__construct();
	}
	
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		
		$version = '1';
		$namespace = 'mrp/v' . $version;
		$base = 'rating-results';
		
		register_rest_route( $namespace, '/' . $base, array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'            => array(
								'rating_form_id' =>  array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'limit' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'taxonony' => array(
										'validate_callback' => array( $this, 'is_not_empty_value' )
								),
								'term_id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'sort_by' => array(
										'validate_callback' => array( $this, 'is_not_empty_value' )
								),
								'rating_item_ids' => array(
										'validate_callback' => array( $this, 'is_numeric_array_values' )
								),
								'user_roles' => array(
										'validate_callback' => array( $this, 'is_string_array_values' )
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
								'rating_entry_ids' => array(
										'validate_callback' => array( $this, 'is_numeric_array_values' )
								),
								'post_ids' => array(
										'validate_callback' => array( $this, 'is_numeric_array_values' )
								),
								'post_ids' => array(
										'validate_callback' => array( $this, 'is_entry_status_value' )
								),
								'approved_comments_only' => array(
										'validate_callback' => array( $this, 'is_boolean_value' )
								),
								'published_posts_only' => array(
										'validate_callback' => array( $this, 'is_boolean_value' )
								),
								'post_id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'user_id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
								'comments_only' => array(
										'validate_callback' => array( $this, 'is_boolean_value' )
								)
						)
				)
		) );
		
	}
	
	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		
		$allowed_parameters = array( 'taxonomy', 'term_id', 'limit', 'rating_form_id', 'sort_by',
				'rating_item_ids', 'user_roles', 'rating_entry_ids', 'to_date', 'from_date',
				'offset', 'post_ids', 'entry_status', 'approved_comments_only',	'published_posts_only',
				'post_id', 'user_id', 'comments_only' );
		
				$parameters = apply_filters( 'mrp_rest_api_rating_results_sanitize_params', $request->get_query_params(), $allowed_parameters );
		
		$rating_result_list = MRP_Multi_Rating_API::get_rating_result_list( $parameters );
		
		return new WP_REST_Response( $rating_result_list, 200 );
	}
		
}