<?php

/**
 * 
 * REST API controller for custom fields
 * @author dpowney
 *
 */
class MRP_REST_API_Custom_Fields extends MRP_REST_API_Common {
	
	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		
		$version = '1';
		$namespace = 'mrp/v' . $version;
		$base = 'custom-fields';
		
		register_rest_route( $namespace, '/' . $base, array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'            => array(
								'id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								)
						),
				)
		) );
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
				array(
						'methods'         => WP_REST_Server::READABLE,
						'callback'        => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'            => array(
								'id' => array(
										'validate_callback' => array( $this, 'is_numeric_value' )
								),
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
		
		$custom_fields = MRP_Multi_Rating_API::get_custom_fields();
		
		return new WP_REST_Response( $custom_fields, 200 );
	}
	
	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
	
		$parameters = $request->get_url_params();
		
		$custom_fields = MRP_Multi_Rating_API::get_custom_fields();
		
		if ( empty( $custom_fields ) ) {
			return new WP_REST_Response( 'no_custom_fields', __( 'No custom fields', 'mrp-rest-api' ), array( 'status' => 404 ) );
		}
	
		$custom_field = isset( $custom_fields[$parameters['id']] ) ? $custom_fields[$parameters['id']] : null;
	
		return new WP_REST_Response( $custom_field, 200 );
	}
		
}