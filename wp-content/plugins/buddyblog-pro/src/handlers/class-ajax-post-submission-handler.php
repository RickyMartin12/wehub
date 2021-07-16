<?php
/**
 * Handles Submission of post via ajax(if enabled)
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
 * Handles Submission of post via ajax(if enabled)
 */
class Ajax_Post_Submission_Handler {

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
		add_action( 'wp_ajax_bblpro_submit_post', array( $this, 'save_post' ) );
	}

	/**
	 * Post thumbnail via ajax.
	 */
	public function save_post() {

		$error_messages = array(
			'invalid_post'   => array( 'global' => _x( 'Invalid post.', 'Post form validation message', 'buddyblog-pro' ) ),
			'invalid_action' => array( 'global' => _x( 'Invalid action.', 'Post form validation message', 'buddyblog-pro' ) ),

		);
		// form id is required.
		if ( empty( $_POST['bbl_form_id'] ) ) {
			wp_send_json_error( $error_messages['invalid_post'] );
		}

		if ( empty( $_POST['_bbl_form_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['_bbl_form_nonce'] ), 'bbl_edit_post' ) ) {
			wp_send_json_error( $error_messages['invalid_action'] );
		}

		$form_id = absint( $_POST['bbl_form_id'] );

		if ( ! $form_id ) {
			wp_send_json_error( $error_messages['invalid_action'] );
		}

		$form = bblpro_get_form( $form_id );

		if ( ! $form ) {
			wp_send_json_error( $error_messages['invalid_action'] );
		}

		$title   = isset( $_POST['bbl_post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['bbl_post_title'] ) ) : _x( 'Untitled', 'Title if the post title is not specifid in form', 'buddyblog-pro' );
		$content = isset( $_POST['bbl_post_content'] ) ? wp_kses_post( wp_unslash( $_POST['bbl_post_content'] ) ) : '';
		$excerpt = isset( $_POST['bbl_post_excerpt'] ) ? wp_kses_post( wp_unslash( $_POST['bbl_post_excerpt'] ) ) : '';

		$default_status = bblpro_form_get_post_status( $form_id );
		$old_status     = $default_status;
		$post_status    = $default_status;
		$post_type      = bblpro_form_get_post_type( $form_id );
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
				$old_status             = $post->post_status;
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
			$send_errors = array_merge( array( 'global' => _x( 'Unable to submit. Please complete the form.', 'Post form validation message', 'buddyblog-pro' ) ), $errors->errors );
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
				),
				true
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

		/*
		// enforce unique slug.
		if ( $post_data['post_title'] ) {
			$post_data['post_name'] = wp_unique_post_slug(
				sanitize_title( $post_data['post_title'] ),
				isset( $post_data['ID'] ) ? $post_data['ID'] : 0,
				$post_data['post_status'],
				$post_data['post_type'],
				0
			);
		}
		 */

		// validate
		// Before we insert/update post, let us validate(we are validating late to  allow custom code have as much details as possible).
		$errors = bblpro_validate_form_data( $form_id, $_POST, $post_data );

		if ( $errors && $errors->has_errors() ) {
			$send_errors = array_merge( array( 'global' => _x( 'Unable to submit. Please complete the form.', 'Post form validation message', 'buddyblog-pro' ) ), $errors->errors );
			wp_send_json_error( $send_errors );
		}

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error(
				array(
					'global' => _x( 'Unable to submit. Please try again later.', 'Post form validation message', 'buddyblog-pro' ),
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

		$post_url = get_permalink( $post_id );
		// if status changed to submitted form.
		if ( $post_status === $default_status && $old_status !== $default_status ) {
			// the post has been published/submitted.
			do_action( 'bblpro_post_submitted', $post_id, $form_id, $post_data );

			$redirect_url = add_query_arg( array( 'is_new' => 1 ), bblpro_get_post_edit_url( $post_id ) );
			$redirect_url = apply_filters( 'bblpro_post_submitted_redirect_url', $redirect_url, $post_id, $form_id, $post_data );

			if ( apply_filters( 'bblpro_show_view_link_on_submission', false, $post_id ) ) {
				/* translators: %s : post view link */
				$message = sprintf( _x( 'Post submitted successfully. <a href="%s" target="_blank">View Post</a>.', 'Successful post submission message', 'buddyblog-pro' ), $post_url );
			} else {
				$message = _x( 'Post submitted successfully.', 'Successful post submission message', 'buddyblog-pro' );
			}
		} else {
			// if we are here, Post is updated not published(It could be an update to draft post or already published post) .
			do_action( 'bblpro_post_updated', $post_id, $form_id, $post_data );

			$redirect_url = add_query_arg( array( 'updated' => 1 ), bblpro_get_post_edit_url( $post_id ) );
			$redirect_url = apply_filters( 'bblpro_post_updated_redirect_url', $redirect_url, $post_id, $form_id, $post_data );

			if ( apply_filters( 'bblpro_show_view_link_on_update', false, $post_id ) ) {
				/* translators: %s : post view link */
				$message = sprintf( _x( 'Post updated successfully. <a href="%s" target="_blank">View</a>.', 'Successful post update message', 'buddyblog-pro' ), get_permalink( $post_id ) );
			} else {
				$message = _x( 'Post updated successfully.', 'Successful post update message', 'buddyblog-pro' );
			}
		}

		wp_send_json_success(
			array(
				'global' => $message,
				'url'    => $redirect_url,
			)
		);
	}

}
