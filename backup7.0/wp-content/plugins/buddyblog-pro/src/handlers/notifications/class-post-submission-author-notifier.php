<?php
/**
 * Post submission Author notifier
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
 * Notifies user when they submit their post(publish themselves).
 */
class Post_Submission_Author_Notifier {

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
		add_action( 'bblpro_post_submitted', array( $this, 'notify' ), 10, 2 );
	}

	/**
	 * Notifies author on publish.
	 *
	 * @param int $post_id post id.
	 * @param int $form_id form id.
	 */
	public function notify( $post_id, $form_id ) {

		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		$notify_author = get_post_meta( $form_id, '_buddyblog_notify_author_on_submit', true );

		if ( ! $notify_author ) {
			return;// no need to notify author.
		}

		$author = get_user_by( 'id', $post->post_author );

		// this should always pass.
		if ( ! $author ) {
			return;
		}

		$subject = get_post_meta( $form_id, '_buddyblog_submit_author_notification_subject', true );
		$message = get_post_meta( $form_id, '_buddyblog_submit_author_notification_message', true );

		if ( empty( $subject ) || empty( $message ) ) {
			return;// subject and message not specified.
		}

		$subject = bblpro_parse_tokens( $subject, $post );
		$message = bblpro_parse_tokens( $message, $post );

		$email = $author->user_email;

		wp_mail( $email, $subject, $message );
	}
}
