<?php

/**
 * 
 * REST API controller for rating forms
 * @author dpowney
 *
 */
class MRP_REST_API_Rating_Forms extends MRP_REST_API_Common {
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		add_filter( 'mrp_rest_api_rating_forms_sanitize_params', array( $this, 'sanitize_parameters' ), 10, 2 );
		parent::__construct();
		
	}
	
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		
		$version = '1';
		$namespace = 'mrp/v' . $version;
		$base = 'rating-forms';
		
		register_rest_route( $namespace, '/' . $base, array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'            => array(
		
						),
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
		
		$rating_forms = MRP_Multi_Rating_API::get_rating_forms();

		$temp = array();
		foreach ($rating_forms as $key => $value) {
			$value['id'] = $value['rating_form_id'];
			unset($value['rating_form_id']);
		    array_push($temp, $value);
		}
		
		return new WP_REST_Response( $temp, 200 );
	}
	
	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
	
		$parameters = $request->get_url_params();;
	
		$rating_form = MRP_Multi_Rating_API::get_rating_form( $parameters['id'] );
	
		return new WP_REST_Response( $rating_form, 200 );
	}
		
}