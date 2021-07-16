<?php

/**
 * 
 * REST API controller for rating entries
 * @author dpowney
 *
 */
class MRP_REST_API_Rating_Entries extends MRP_REST_API_Common {
	
	/**
	 * Constructor
	 */
	function __construct() {
		add_filter( 'mrp_rest_api_rating_entries_sanitize_params', array( $this, 'sanitize_parameters' ), 10, 2 );
		parent::__construct();
	}
	
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		
		$version = '1';
		$namespace = 'mrp/v' . $version;
		$base = 'rating-entries';
		
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
								),
								'approved_comments_only' => array(
										'validate_callback' => array( $this, 'is_boolean_value' )
								),
								'published_posts_only' => array(
										'validate_callback' => array( $this, 'is_boolean_value' )
								),
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
		
		$allowed_parameters = array( 'taxonomy', 'term_id', 'limit', 'rating_form_id', 'sort_by', 'post_id',
				'rating_item_ids', 'user_roles', 'rating_entry_ids', 'user_id', 'to_date', 'from_date',
				'offset', 'post_ids', 'entry_status', 'approved_comments_only', 'published_posts_only' );
		
		$parameters = apply_filters( 'mrp_rest_api_rating_entries_sanitize_params', $request->get_query_params(), $allowed_parameters );
		
		$rating_entry_result_list = MRP_Multi_Rating_API::get_rating_entry_result_list( $parameters );
		
		$rating_entries = array();
		foreach ( $rating_entry_result_list['rating_results'] as $index => $rating_entry_result ) {
			
			$rating_entry = MRP_Multi_Rating_API::get_rating_entry( array( 'rating_entry_id' => $rating_entry_result['rating_entry_id'] ) );
			unset($rating_entry_result['post_id']);
			unset($rating_entry_result['rating_form_id']);
			unset($rating_entry_result['rating_entry_id']);
			unset($rating_entry_result['user_id']);
			unset($rating_entry_result['entry_date']);
			$rating_entry['rating_result'] = $rating_entry_result;
			$rating_entries[$index] = $rating_entry;
		}
		
		return new WP_REST_Response( $rating_entries, 200 );
	}
	
	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
	
		$parameters = $request->get_url_params();
		$rating_entry_id = $parameters['id'];
	
		$rating_entry = MRP_Multi_Rating_API::get_rating_entry( array( 'rating_entry_id' => $rating_entry_id ) );
		
		if ( empty( $rating_entry ) ) {
			return new WP_REST_Response( 'invalid_rating_entry_id', __( 'Invalid rating entry id', 'mrp-rest-api' ), array( 'status' => 404 ) );
		}
		
		$rating_entry_result = MRP_Multi_Rating_API::get_rating_entry_result( array( 'rating_entry_id' => $rating_entry_id ) );
		unset($rating_entry_result['post_id']);
		unset($rating_entry_result['rating_form_id']);
		unset($rating_entry_result['rating_entry_id']);
		unset($rating_entry_result['user_id']);
		unset($rating_entry_result['entry_date']);
		
		return new WP_REST_Response( $rating_entry, 200 );
	}
		
}