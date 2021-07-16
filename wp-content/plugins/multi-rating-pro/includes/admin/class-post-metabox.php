<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Post metabox class
 */
class MRP_Post_Metabox {

	/**
	 * Constructor
	 */
	function __construct() {

		if ( current_user_can( 'mrp_manage_ratings' ) ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		}
		add_action( 'save_post', array( $this, 'save_post_meta' ) );

	}

	/**
	 * Adds the meta box container
	 */
	public function add_meta_box( $post_type ) {

		$current_screen = get_current_screen();
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() || method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return;
		}

		add_meta_box( 'mrp_meta_box', __( 'Multi Rating Pro', 'multi-rating-pro' ), array( $this, 'display_meta_box_content' ), $post_type, 'side', 'default' );

	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_post_meta( $post_id ) {

		if ( ! function_exists('get_current_screen')) {
			return;
		}
		$current_screen = get_current_screen();
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() || method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return;
		}

		if ( ! isset( $_POST['mrp_meta_box_nonce_action'] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST['mrp_meta_box_nonce_action'], 'mrp_meta_box_nonce' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$rating_form_id = $_POST['rating-form-id'];

		$rating_form_position = $_POST['rating-form-position'];
		$rating_results_position = $_POST['rating-results-position'];
		$structured_data_type = $_POST['mrp-structured-data-type'];

		$allow_anonymous = $_POST['allow-anonymous'];
		if ( $allow_anonymous !== "" ) { // note ("" == false) = true
			$allow_anonymous = ( $allow_anonymous == 'true' ) ? 'true' : 'false';
		}

		// Update the meta field.
		update_post_meta( $post_id, MRP_Multi_Rating::RATING_FORM_ID_POST_META, $rating_form_id );
		update_post_meta( $post_id, MRP_Multi_Rating::RATING_FORM_POSITION_POST_META, $rating_form_position );
		update_post_meta( $post_id, MRP_Multi_Rating::RATING_RESULTS_POSITION_POST_META, $rating_results_position );
		update_post_meta( $post_id, MRP_Multi_Rating::ALLOW_ANONYMOUS_POST_META, $allow_anonymous );
		update_post_meta( $post_id, MRP_Multi_Rating::STRUCTURED_DATA_TYPE_POST_META, $structured_data_type );
	}


	/**
	 * Displays the meta box content
	 *
	 * @param WP_Post $post The post object.
	 */
	public function display_meta_box_content( $post ) {

		wp_nonce_field( 'mrp_meta_box_nonce', 'mrp_meta_box_nonce_action' );

		$rating_form_id = get_post_meta( $post->ID, MRP_Multi_Rating::RATING_FORM_ID_POST_META, true );
		$rating_form_position = get_post_meta( $post->ID, MRP_Multi_Rating::RATING_FORM_POSITION_POST_META, true );
		$rating_results_position = get_post_meta( $post->ID, MRP_Multi_Rating::RATING_RESULTS_POSITION_POST_META, true );
		$allow_anonymous = get_post_meta( $post->ID, MRP_Multi_Rating::ALLOW_ANONYMOUS_POST_META, true );
		$structured_data_type = get_post_meta( $post->ID, MRP_Multi_Rating::STRUCTURED_DATA_TYPE_POST_META, true );
		?>

		<p><label for="rating-form-id"><?php _e( 'Rating Form', 'multi-rating-pro' ); ?></label></p>
		<p><?php mrp_rating_form_select( $rating_form_id, true, null, 'rating-form-id', 'rating-form-id', 'widefat' ); ?></p>

		<p><label for="allow-anonymous"><?php _e( 'Allow Anonymous Ratings', 'multi-rating-pro' ); ?></label></p>
		<p><select name="allow-anonymous" class="widefat">
				<option value="" <?php selected( '', $allow_anonymous, true ); ?>><?php _e( 'Use default settings', 'multi-rating-pro' ); ?></option>
				<option value="true" <?php selected( 'true', $allow_anonymous, true ); ?>><?php _e( 'Yes', 'multi-rating-pro' ); ?></option>
				<option value="false" <?php selected( 'false', $allow_anonymous, true ); ?>><?php _e( 'No', 'multi-rating-pro' ); ?></option>
			</select>
		</p>

		<hr style="margin-top: 1em; margin-bottom:1em;" />

		<p><strong><?php _e( 'Auto Placement Settings', 'multi-rating-pro' ); ?></strong></p>

		<p><label for="rating-form-position"><?php _e( 'Rating Form Position', 'multi-rating-pro' ); ?></label></p>
		<p><?php mrp_rating_form_position_select( $rating_form_position, 'widefat' ); ?>
		
		<p><label for="rating-results-position"><?php _e( 'Rating Results Position', 'multi-rating-pro' ); ?></label></p>
		<p><?php mrp_rating_results_position_select( $rating_results_position, 'widefat' ); ?></p>

		<hr style="margin-top: 1em; margin-bottom:1em;" />

		<p><strong><?php _e( 'Structured Data Type', 'multi-rating-pro' ); ?></strong></p>
		<p><label for="mrp-structured-data-type"><?php _e( 'Create New Type', 'multi-rating-pro' ); ?></label></p>
		<p>
			<select class="widefat" name="mrp-structured-data-type">
				<option value="" <?php selected( '', $structured_data_type, true );?>></option>
				<option value="Book" <?php selected( 'Book', $structured_data_type, true );?>><?php _e( 'Book', 'multi-rating-pro' ); ?></option>
				<option value="Course" <?php selected( 'Course', $structured_data_type, true );?>><?php _e( 'Course', 'multi-rating-pro' ); ?></option>
				<option value="CreativeWorkSeason" <?php selected( 'CreativeWorkSeason', $structured_data_type, true );?>><?php _e( 'CreativeWorkSeason', 'multi-rating-pro' ); ?></option>
				<option value="CreativeWorkSeries" <?php selected( 'CreativeWorkSeries', $structured_data_type, true );?>><?php _e( 'CreativeWorkSeries', 'multi-rating-pro' ); ?></option>
				<option value="Episode" <?php selected( 'Episode', $structured_data_type, true );?>><?php _e( 'Episode', 'multi-rating-pro' ); ?></option>
				<option value="Event" <?php selected( 'Event', $structured_data_type, true );?>><?php _e( 'Event', 'multi-rating-pro' ); ?></option>
				<option value="Game" <?php selected( 'Game', $structured_data_type, true );?>><?php _e( 'Game', 'multi-rating-pro' ); ?></option>
				<option value="HowTo" <?php selected( 'HowTo', $structured_data_type, true );?>><?php _e( 'HowTo', 'multi-rating-pro' ); ?></option>
				<option value="LocalBusiness" <?php selected( 'LocalBusiness', $structured_data_type, true );?>><?php _e( 'LocalBusiness', 'multi-rating-pro' ); ?></option>
				<option value="MediaObject" <?php selected( 'MediaObject', $structured_data_type, true );?>><?php _e( 'MediaObject', 'multi-rating-pro' ); ?></option>
				<option value="Movie" <?php selected( 'Movie', $structured_data_type, true );?>><?php _e( 'Movie', 'multi-rating-pro' ); ?></option>
				<option value="MusicPlaylist" <?php selected( 'MusicPlaylist', $structured_data_type, true );?>><?php _e( 'MusicPlaylist', 'multi-rating-pro' ); ?></option>
				<option value="MusicRecording" <?php selected( 'MusicRecording', $structured_data_type, true );?>><?php _e( 'MusicRecording', 'multi-rating-pro' ); ?></option>
				<option value="Organization" <?php selected( 'Organization', $structured_data_type, true );?>><?php _e( 'Organization', 'multi-rating-pro' ); ?></option>
				<option value="Product" <?php selected( 'Product', $structured_data_type, true );?>><?php _e( 'Product', 'multi-rating-pro' ); ?></option>
				<option value="Recipe" <?php selected( 'Recipe', $structured_data_type, true );?>><?php _e( 'Recipe', 'multi-rating-pro' ); ?></option>
				<option value="SoftwareApplication" <?php selected( 'SoftwareApplication', $structured_data_type, true );?>><?php _e( 'SoftwareApplication', 'multi-rating-pro' ); ?></option>
			</select>
			<span class="mrp-help"><?php _e( 'Schema.org item type for post. If you have the WordPress SEO or WooCommerce plugins adding structured data for the type already, do not set. Note some types may require additional structured data.', 'multi-rating-pro' ); ?></span>
		</p>
		<?php
	}
}
