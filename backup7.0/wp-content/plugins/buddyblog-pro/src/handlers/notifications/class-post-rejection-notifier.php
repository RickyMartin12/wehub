<?php
/**
 * Post approval Author notifier
 *
 * @package    BuddyBlog_Pro
 * @subpackage Handlers\Notifications
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Handlers\Notifications;

// Do not allow direct access over web.

defined( 'ABSPATH' ) || exit;

/**
 * Notifies author when a their post is approved.
 */
class Post_Rejection_Notifier {

	/**
	 * Boot itself
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup.
	 */
	private function setup() {
		add_action( 'bblpro_post_rejected', array( $this, 'notify' ) );
	}

	/**
	 * Notify on approval.
	 *
	 * @param int $post_id post id.
	 */
	public function notify( $post_id ) {

		$form_id = bblpro_post_get_form_id( $post_id );

		if ( ! $form_id ) {
			return;
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		$notify_author = get_post_meta( $form_id, '_buddyblog_notify_author_on_rejection', true );

		if ( ! $notify_author ) {
			return; // no need to notify author.
		}

		$subject = get_post_meta( $form_id, '_buddyblog_rejected_author_notification_subject', true );
		$message = get_post_meta( $form_id, '_buddyblog_rejected_author_notification_message', true );

		if ( empty( $subject ) || empty( $message ) ) {
			return;// subject and message not specified.
		}

		$author = get_user_by( 'id', $post->post_author );
		if ( ! $author ) {
			return;
		}

		$email   = $author->user_email;
		$subject = bblpro_parse_tokens( $subject, $post );
		$message = bblpro_parse_tokens( $message, $post );


		wp_mail( $email, $subject, $message );
	}
}
