<?php
/**
 * Adds Multi Rating Pro post meta custom fields
 * 
 * @param unknown $object
 * @param unknown $field_name
 * @param unknown $request
 */
function mrp_rest_api_custom_fields( $object, $field_name, $request ) {
	
	$post_id = $object[ 'id' ];
	$rating_forms = MRP_Multi_Rating_API::get_rating_forms();
	
	$custom_fields = array();
	foreach ( $rating_forms as $rating_form ) {
		$rating_form_id = $rating_form['rating_form_id'];
		
		$rating_result = get_post_meta( $post_id, MRP_Multi_Rating::RATING_RESULTS_POST_META . '_' . $rating_form_id );
		if ( isset( $rating_result ) ) {
			$custom_fields[MRP_Multi_Rating::RATING_RESULTS_POST_META . '_' . $rating_form_id] = $rating_result;
		}
	}
	
	$custom_fields['rating_form'] = MRP_Utils::get_rating_form( $object['id'] );
	
	return $custom_fields;
}
