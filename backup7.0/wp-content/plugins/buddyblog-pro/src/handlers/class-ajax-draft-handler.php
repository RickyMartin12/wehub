<?php
/**
 * Handles Saving Drat, Reverting to draft actions
 *
 * @package    BuddyBlog_Pro
 * @subpackage Handlers
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Handlers;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Handles Saving Draft, Reverting to draft actions.
 */
class Ajax_Draft_Handler {

	/**
	 * Boot itself
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	private function setup() {
		add_action( 'wp_ajax_bblpro_save_draft', array( $this, 'save_draft' ) );
		add_action( 'wp_ajax_bblpro_revert_to_draft', array( $this, 'revert_as_draft' ) );
	}

	/**
	 * Post thumbnail via ajax.
	 */
	public function save_draft() {

		$errors_messages = array(
			'invalid_post'   => array( 'global' => _x( 'Invalid post.', 'Post form validation message', 'buddyblog-pro' ) ),
			'invalid_action' => array( 'global' => _x( 'Invalid action.', 'Post form validation message', 'buddyblog-pro' ) ),

		);
		// form id is required.
		if ( empty( $_POST['bbl_form_id'] ) ) {
			wp_send_json_error( $errors_messages['invalid_post'] );
		}

		if ( empty( $_POST['_bbl_form_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['_bbl_form_nonce'] ), 'bbl_edit_post' ) ) {
			wp_send_json_error( $errors_messages['invalid_action'] );
		}


		$form_id = absint( $_POST['bbl_form_id'] );

		if ( ! $form_id ) {
			wp_send_json_error( $errors_messages['invalid_action'] );
		}

		$form = bblpro_get_form( $form_id );

		if ( ! $form ) {
			wp_send_json_error( $errors_messages['invalid_action'] );
		}

		$title   = isset( $_POST['bbl_post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['bbl_post_title'] ) ) : _x( 'Untitled', 'Post title if not specified in form', 'buddyblog-pro' );
		$content = isset( $_POST['bbl_post_content'] ) ? wp_kses_post( wp_unslash( $_POST['bbl_post_content'] ) ) : '';
		$excerpt = isset( $_POST['bbl_post_excerpt'] ) ? wp_kses_post( wp_unslash( $_POST['bbl_post_excerpt'] ) ) : '';

		$post_status = bblpro_form_get_post_status( $form_id );
		$post_type   = bblpro_form_get_post_type( $form_id );
		// if all is well, let us create/update the post.
		$post_data = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'post_type'    => $post_type,
		);

		$error   = false;
		$errors  = new \WP_Error();
		$post_id = 0;

		if ( isset( $_POST['bbl_post_id'] ) ) {
			$post_id = absint( $_POST['bbl_post_id'] );
		}
		// if post id is given, It is an edit operation.
		$post = null;

		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );

			if ( ! $post ) {
				$errors->add( 'invalid_post', _x( 'Invalid action.', 'Post form validation message', 'buddyblog-pro' ) );
				$error = true;
			} elseif ( ! bblpro_user_can_edit_post( get_current_user_id(), $post_id ) ) {
				$errors->add( 'access_denied', _x( 'Access denied.', 'Post form validation message', 'buddyblog-pro' ) );
				$error = true;
			} else {
				$post_data['post_date'] = $post->post_date;
				// if it is not a published post, reset publish date.
				if ( 'publish' !== $post->post_status ) {
					$post_data['post_date'] = current_time( 'mysql' );
				}
			}
		} else {
			// check the create permission.
			if ( ! bblpro_user_can_create_post( get_current_user_id(), $post_type ) ) {
				$errors->add( 'access_denied', _x( 'Access denied.', 'Post form validation message', 'buddyblog-pro' ) );
				$error = true;
			}
		}

		if ( $error ) {
			$send_errors = array_merge( array( 'global' => _x( 'Unable to save draft. Please complete the form.', 'Post form validation message', 'buddyblog-pro' ) ), $errors->errors );
			wp_send_json_error( $send_errors );
		}

		// Thumbnail?
		if ( ! empty( $_POST['_thumbnail_id'] ) ) {
			$post_data['_thumbnail_id'] = absint( $_POST['_thumbnail_id'] );
		}

		// comment status default.
		$comment_status = $post ? $post->comment_status : bblpro_form_get_default_comment_status( $form_id );

		// comment status update?
		if ( bblpro_form_enable_comment_status_control( $form_id ) && isset( $_POST['bbl_comment_status'] ) && bblpro_is_valid_comment_status( $form_id, wp_unslash( $_POST['bbl_comment_status'] ) ) ) {
			$comment_status = wp_unslash( $_POST['bbl_comment_status'] );
		}

		// if it is a new post and we don't have any comment status, let us close the comment.
		if ( empty( $comment_status ) && ! $post ) {
			$comment_status = 'closed'; // User has not checked it.
		}

		if ( $comment_status ) {
			$post_data['comment_status'] = $comment_status;
		}

		if ( ! empty( $post ) ) {
			$post_data['ID']          = $post_id;
			$post_status              = in_array(
				$post->post_status,
				array(
					'auto-draft',
					'draft',
				)
			) ? $post_status : $post->post_status;
			$post_data['post_author'] = $post->post_author;
		} else {
			$post_data['post_author'] = get_current_user_id();
		}

		if ( bblpro_form_enable_post_visibility_control( $form_id ) && ! empty( $_POST['bbl_post_visibility'] ) && 'publish' === $post_status ) {
			switch ( $_POST['bbl_post_visibility'] ) {
				case 'private':
					$post_data['post_status'] = 'private';
					break;
			}
		} else {
			// save post status.
			$post_data['post_status'] = $post_status;
		}

		// only allow draft for new post(not published/pending etc).
		if ( ! $post || in_array( $post->post_status, array( 'draft', 'inherit', 'auto-draft' ), true ) ) {
			$post_data['post_status'] = 'draft';
		}

		// validate
		// Before we insert/update post, let us validate(we are validating late to  allow custom code have as much details as possible).
		$errors = bblpro_validate_form_data( $form_id, $_POST, $post_data );

		if ( $errors && $errors->has_errors() ) {
			$send_errors = array_merge( array( 'global' => _x( 'Unable to save draft. Please complete the form.', 'Post form validation message', 'buddyblog-pro' ) ), $errors->errors );
			wp_send_json_error( $send_errors );
		}

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error(
				array(
					'global' => _x( 'Unable to save draft.', 'Post form validation message', 'buddyblog-pro' ),
				)
			);
		}

		// post saved/updated successfully
		// update terms.
		bblpro_post_update_terms( $post_id, bblpro_form_get_taxonomies( $form_id ), isset( $_POST['tax_input'] ) ? wp_unslash( $_POST['tax_input'] ) : array() );

		bblpro_post_update_custom_fields( $form_id, $post_id, bblpro_form_get_custom_fields( $form_id ), isset( $_POST['bbl_custom_field'] ) ? wp_unslash( $_POST['bbl_custom_field'] ) : array() );
		// Save form id with the post.
		// allows us to understand meta field types when retrieving.
		bblpro_post_update_form_id( $post_id, $form_id );

		do_action( 'bblpro_post_draft_saved', $post_id );

		if ( apply_filters( 'bblpro_show_preview_link_on_draft_save', false, $post_id ) ) {
			/* translators: %s : post draft preview link */
			$message = sprintf( _x( 'Draft saved. <a href="%s" target="_blank">Preview Post</a>.', 'Draft Preview post link', 'buddyblog-pro' ), get_permalink( $post_id ) );
		} else {
			$message = _x( 'Draft saved.', 'Draft saved message', 'buddyblog-pro' );
		}

		wp_send_json_success(
			array(
				'global' => $message,
			)
		);
	}

	/**
	 * Reverts post to draft.
	 */
	public function revert_as_draft() {

		$errors_messages = array(
			'invalid_post'   => array( 'global' => _x( 'Invalid post.', 'Post form validation message', 'buddyblog-pro' ) ),
			'invalid_action' => array( 'global' => _x( 'Invalid action.', 'Post form validation message', 'buddyblog-pro' ) ),
		);
		// form id is required.
		if ( empty( $_POST['bbl_form_id'] ) ) {
			wp_send_json_error( $errors_messages['invalid_post'] );
		}

		if ( empty( $_POST['_bbl_form_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['_bbl_form_nonce'] ), 'bbl_edit_post' ) ) {
			wp_send_json_error( $errors_messages['invalid_action'] );
		}

		$post_id = 0;

		if ( isset( $_POST['bbl_post_id'] ) ) {
			$post_id = absint( $_POST['bbl_post_id'] );
		}
		// if post id is given, It is an edit operation.
		$post = null;

		// is post id is not given, invalid request, must provide post id.
		if ( empty( $post_id ) ) {
			wp_send_json_error( array( 'global' => _x( 'Invalid action.', 'Post form validation message', 'buddyblog-pro' ) ) );
		}

		$post = get_post( $post_id );

		$errors = new \WP_Error();

		if ( ! $post ) {
			$errors->add( 'invalid_post', _x( 'Invalid action.', 'Post form validation message', 'buddyblog-pro' ) );
		} elseif ( ! bblpro_user_can_draft_post( get_current_user_id(), $post_id ) ) {
			$errors->add( 'access_denied', _x( 'Access denied.', 'Post form validation message', 'buddyblog-pro' ) );
		}

		if ( $errors->has_errors() ) {
			$send_errors = array_merge( array( 'global' => _x( 'Unable to revert to draft.', 'Post form validation message', 'buddyblog-pro' ) ), $errors->errors );
			wp_send_json_error( $send_errors );
		}

		$pos_data = array(
			'ID'          => $post_id,
			'post_status' => 'draft',
		);

		// if we are here, set post as draft.
		$updated = wp_update_post( $pos_data, true );

		if ( $updated && is_wp_error( $updated ) ) {
			$send_errors = array_merge( array( 'global' => _x( 'Unable to revert to draft.', 'Draft revert error message', 'buddyblog-pro' ) ), $updated->errors );
			wp_send_json_error( $send_errors );
		}

		do_action( 'bblpro_post_reverted_to_draft', $post_id );
		// if we are here, all good, send the redirect url.
		wp_send_json_success(
			array(
				'global' => _x( 'Reverted to draft successfully!', 'Draft revert successful message', 'buddyblog-pro' ),
				'url'    => bblpro_get_post_edit_url( $post_id ),
			)
		);
	}
}
