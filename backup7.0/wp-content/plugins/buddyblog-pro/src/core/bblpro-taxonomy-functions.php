<?php
/**
 * Update term data.
 *
 * @param int   $post_id post id.
 * @param array $tax_settings tax settings.
 * @param array $term_data term data.
 */
function bblpro_post_update_terms( $post_id, $tax_settings, $term_data = array() ) {

	if ( empty( $tax_settings ) ) {
		return;
	}

	// If we have some taxonomy info
	// Tax_slug=>tax_options set for that taxonomy while registering the form.
	foreach ( $tax_settings as $tax => $tax_options ) {
		$selected_terms = array();
		// Get all selected terms, may be array, depends on whether a dd or checklist.
		if ( isset( $term_data[ $tax ] ) ) {
			$selected_terms = (array) $term_data[ $tax ];
		}

		// Check if include is given when the form was registered and this is a subset of include.
		if ( ! empty( $tax_options['include'] ) ) {

			$allowed = $tax_options['include']; // This is an array.
			// Check a diff of selected vs include.
			$is_fake = array_diff( $selected_terms, $allowed );

			if ( ! empty( $is_fake ) ) {
				continue;
			} //we have fake input vales, do not store
		}

		/**
		 * If we are here, everything is fine.
		 * it can still be empty, if the user has not selected anything and nothing was given
		 * post to all the allowed terms
		 */
		if ( empty( $selected_terms ) && isset( $tax_options['include'] ) ) {
			$selected_terms = $tax_options['include'];
		}

		// Update the taxonomy/post association.
		if ( ! empty( $selected_terms ) ) {
			$selected_terms = array_map( 'intval', $selected_terms );
			wp_set_object_terms( $post_id, $selected_terms, $tax );
		}
	} // End of the loop.


}