<?php
/**
 * Handles post submission and updating of the existing post.
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
 * Handles "submit" and "Update" actions.
 */
class Post_Submission_Handler {

	/**
	 * Class self boot
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	private function setup() {
		add_action( 'bblpro_actions', array( $this, 'handle' ) );
	}

	/**
	 * Handle form submission
	 */
	public function handle() {

		$old_status = '';

		if ( bblpro_is_edit_page() ) {
			$post = bblpro_get_current_editable_post();

			if ( ! $post ) {
				return;
			}

			$old_status = $post->post_status;

			if ( isset( $_GET['is_new'] ) ) {
				$this->add_update_notice( $post->ID, true );
			} elseif ( isset( $_GET['updated'] ) ) {
				$this->add_update_notice( $post->ID, false );
			}
		}

		// form id is required.
		if ( empty( $_POST['bbl_form_id'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( wp_unslash( $_POST['_bbl_form_nonce'] ), 'bbl_edit_post' ) ) {
			return;
		}

		$form_id = absint( $_POST['bbl_form_id'] );

		if ( ! $form_id ) {
			return;
		}

		$form = bblpro_get_form( $form_id );

		if ( ! $form ) {
			return;
		}

		$title   = isset( $_POST['bbl_post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['bbl_post_title'] ) ) : __( 'Untitled', 'buddyblog-pro' );
		$content = isset( $_POST['bbl_post_content'] ) ? wp_kses_post( wp_unslash( $_POST['bbl_post_content'] ) ) : '';
		$excerpt = isset( $_POST['bbl_post_excerpt'] ) ? wp_kses_post( wp_unslash( $_POST['bbl_post_excerpt'] ) ) : '';

		$default_status = bblpro_form_get_post_status( $form_id );
		$post_status    = $default_status;
		$post_type      = bblpro_form_get_post_type( $form_id );

		// if all is well, let us create/update the post.
		$post_data = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'post_type'    => $post_type,
		);

		$errors  = buddyblog_pro()->errors;
		$error   = false;
		$post_id = 0;

		if ( isset( $_POST['bbl_post_id'] ) ) {
			$post_id = absint( $_POST['bbl_post_id'] );
		}
		// if post id is given, It is an edit operation.
		$post = null;

		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id );

			if ( ! $post ) {
				$errors->add( 'invalid_post', __( 'Invalid action.', 'buddyblog-pro' ) );
				$error = true;
			} elseif ( ! bblpro_user_can_edit_post( get_current_user_id(), $post_id ) ) {
				$errors->add( 'access_denied', __( 'Access denied.', 'buddyblog-pro' ) );
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
				$errors->add( 'access_denied', __( 'Access denied.', 'buddyblog-pro' ) );
				$error = true;
			}
		}

		if ( $error ) {
			return;
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
			// add error notice?
			return;
		}

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			if ( $post ) {
				buddyblog_pro()->notices->add( 'error', __( 'Unable to update post.', 'buddyblog-pro' ) );
			} else {
				buddyblog_pro()->notices->add( 'error', __( 'Unable to save post.', 'buddyblog-pro' ) );
			}

			return;
		}

		// post saved/updated successfully
		// update terms.
		bblpro_post_update_terms( $post_id, bblpro_form_get_taxonomies( $form_id ), isset( $_POST['tax_input'] ) ? wp_unslash( $_POST['tax_input'] ) : array() );

		bblpro_post_update_custom_fields( $form_id, $post_id, bblpro_form_get_custom_fields( $form_id ), isset( $_POST['bbl_custom_field'] ) ? wp_unslash( $_POST['bbl_custom_field'] ) : array() );
		// Save form id with the post.
		// allows us to understand meta field types when retrieving.
		bblpro_post_update_form_id( $post_id, $form_id );

		// if status changed to submitted form.
		if ( $post_status === $default_status && $old_status !== $default_status ) {
			// the post has been published/submitted.
			do_action( 'bblpro_post_submitted', $post_id, $form_id, $post_data );

			$redirect = add_query_arg( array( 'is_new' => 1 ), bblpro_get_post_edit_url( $post_id ) );
			$redirect = apply_filters( 'bblpro_post_submitted_redirect_url', $redirect, $post_id, $form_id, $post_data );
		} else {
			// if we are here, Post is updated not published(It could be an update to draft post or already published post) .
			do_action( 'bblpro_post_updated', $post_id, $form_id, $post_data );

			$redirect = add_query_arg( array( 'updated' => 1 ), bblpro_get_post_edit_url( $post_id ) );
			$redirect = apply_filters( 'bblpro_post_updated_redirect_url', $redirect, $post_id, $form_id, $post_data );
		}

		if ( $redirect ) {
			bp_core_redirect( $redirect );
		}
	}

	/**
	 * Add update notice.
	 *
	 * @param int  $post_id post is.
	 * @param bool $is_new is new.
	 */
	private function add_update_notice( $post_id, $is_new = false ) {

		$view_link = sprintf( '<span class="bbl-post-view-link-published"><a href="%s">%s</a></span>', get_permalink( $post_id ), __( 'view', 'buddyblog-pro' ) );

		if ( ! $is_new ) {
			/* translators: %s : post view link */
			$message = sprintf( __( 'Post updated. %s', 'buddyblog-pro' ), $view_link );
		} else {
			/* translators: %s : post view link */
			$message = sprintf( __( 'Post saved. %s', 'buddyblog-pro' ), $view_link );
		}

		buddyblog_pro()->notices->add( 'success', $message );
	}
}

