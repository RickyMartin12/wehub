<?php
/**
 * BuddyBlog Pro Form Workflow metabox
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin/Metaboxes
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin\Metaboxes;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Workflow meta box helper.
 */
class Workflow_Meta_Box extends BBL_Meta_Box {

	/**
	 * Saves workflow meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function save( $post ) {

		$post_id = $post->ID;

		// admin notification on submission.
		update_post_meta( $post_id, '_buddyblog_post_status', $this->input( 'post-status', 'publish' ) );
		update_post_meta( $post_id, '_buddyblog_notify_admin_on_submit', $this->input( 'notify-admin-on-submit', 0 ) );

		update_post_meta( $post_id, '_buddyblog_submit_admin_notification_subject', $this->input( 'submit-admin-notification-subject', '' ) );
		update_post_meta( $post_id, '_buddyblog_submit_admin_notification_message', $this->input( 'submit-admin-notification-message', '' ) );

		// author notification on submission.
		update_post_meta( $post_id, '_buddyblog_notify_author_on_submit', $this->input( 'notify-author-on-submit', 0 ) );

		update_post_meta( $post_id, '_buddyblog_submit_author_notification_subject', $this->input( 'submit-author-notification-subject', '' ) );
		update_post_meta( $post_id, '_buddyblog_submit_author_notification_message', $this->input( 'submit-author-notification-message', '' ) );

		// approval.
		// update_post_meta( $post_id, '_buddyblog_approved_post_status', $this->input( 'approved-post-status', 'publish' ) );

		update_post_meta( $post_id, '_buddyblog_notify_author_on_approval', absint( $this->input( 'notify-author-on-approval', 1 ) ) );

		update_post_meta( $post_id, '_buddyblog_approved_author_notification_subject', $this->input( 'approved-author-notification-subject', '' ) );
		update_post_meta( $post_id, '_buddyblog_approved_author_notification_message', $this->input( 'approved-author-notification-message', '' ) );


		// Rejection.
		//update_post_meta( $post_id, '_buddyblog_rejected_post_status', $this->input( 'rejected-post-status', 'trash' ) );

		update_post_meta( $post_id, '_buddyblog_notify_author_on_rejection', absint( $this->input( 'notify-author-on-rejection', 1 ) ) );

		update_post_meta( $post_id, '_buddyblog_rejected_author_notification_subject', $this->input( 'rejected-author-notification-subject', '' ) );
		update_post_meta( $post_id, '_buddyblog_rejected_author_notification_message', $this->input( 'rejected-author-notification-message', '' ) );
	}

	/**
	 * Renders Workflow meta box.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function render( $post = null ) {

		$post    = get_post( $post );
		$form_id = $post->ID;

		$notify_admin = get_post_meta( $form_id, '_buddyblog_notify_admin_on_submit', true );

		if ( '' === $notify_admin ) {
			$notify_admin = 1;
		}

		$submit_admin_notification_subject = get_post_meta( $form_id, '_buddyblog_submit_admin_notification_subject', true );

		if ( '' === $submit_admin_notification_subject ) {
			$submit_admin_notification_subject = 'A new [post_type_singular_name] was published on [site_name]';
		}

		$submit_admin_notification_message = get_post_meta( $form_id, '_buddyblog_submit_admin_notification_message', true );

		if ( '' === $submit_admin_notification_message ) {
			$submit_admin_notification_message = <<<EOT
Hello,
A new [post_type_singular_name] [post_title] was submitted by [author_display_name].
You can view the post here.
Link: [post_url]
Author: [author_display_name]
Author Link: [author_url]

Regards
[site_name] Team
EOT;
		}

		$notify_author_on_submit = get_post_meta( $form_id, '_buddyblog_notify_author_on_submit', true );

		if ( ! $notify_author_on_submit ) {
			$notify_author_on_submit = 0;
		}

		$submit_author_notification_subject = get_post_meta( $form_id, '_buddyblog_submit_author_notification_subject', true );

		if ( '' === $submit_author_notification_subject ) {
			$submit_author_notification_subject = 'Your [post_type_singular_name] submission on [site_name] is successful';
		}

		$submit_author_notification_message = get_post_meta( $form_id, '_buddyblog_submit_author_notification_message', true );

		if ( '' === $submit_author_notification_message ) {
			$submit_author_notification_message = <<<EOT
Hello [author_first_name],
Thank you for submitting your [post_type_singular_name] [post_title].

You can view the post here.
View: [post_url]
Site Link: [site_url]

Regards
[site_name] Team
EOT;
		}

		$notify_author_on_approval = get_post_meta( $form_id, '_buddyblog_notify_author_on_approval', true );

		if ( ! $notify_author_on_approval ) {
			$notify_author_on_approval = 1;
		}

		$notify_author_on_rejection = get_post_meta( $form_id, '_buddyblog_notify_author_on_rejection', true );

		if ( ! $notify_author_on_rejection ) {
			$notify_author_on_rejection = 0;
		}

		$approved_author_notification_subject = get_post_meta( $form_id, '_buddyblog_approved_author_notification_subject', true );

		if ( '' === $approved_author_notification_subject ) {
			$approved_author_notification_subject = 'Congratulations! Your [post_type_singular_name] submission on [site_name] is live now!';
		}

		$approved_author_notification_message = get_post_meta( $form_id, '_buddyblog_approved_author_notification_message', true );

		if ( '' === $approved_author_notification_message ) {
			$approved_author_notification_message = <<<EOT
Hello [author_first_name],
Thank you for submitting your [post_type_singular_name] [post_title].

Your post is live now. 

View: [post_url]
Site Link: [site_url]

Regards
[site_name] Team
EOT;
		}

		// $approval_next_status = get_post_meta( $form_id, '_buddyblog_approved_post_status', true );


		$rejected_author_notification_subject = get_post_meta( $form_id, '_buddyblog_rejected_author_notification_subject', true );

		if ( '' === $rejected_author_notification_subject ) {
			$rejected_author_notification_subject = 'Sorry! Your [post_type_singular_name] submission on [site_name] could not be approved!';
		}

		$rejected_author_notification_message = get_post_meta( $form_id, '_buddyblog_rejected_author_notification_message', true );

		if ( '' === $rejected_author_notification_message ) {
			$rejected_author_notification_message = <<<EOT
Hello [author_first_name],
Thank you for submitting your [post_type_singular_name] [post_title].

We regret to inform that we are unable to approve your post currently. 

You may edit and resubmit the post for approval from your account again.
 
View: [post_url]
Edit: [edit_post_url]
Site Link: [site_url]

Regards
[site_name] Team
EOT;
		}

		$rejection_next_status = get_post_meta( $form_id, '_buddyblog_rejected_post_status', true );

		// Next status to use after rejection.
		if ( ! $rejection_next_status ) {
			$rejection_next_status = 'trash';
		}
		?>

        <div class="bblpro-section-post-submission-settings bblpro-meta-box-settings">

            <div class="bbl-form-submission-fields">
                <h3><?php _e( 'Form submission', 'buddyblog-pro' ); ?></h3>

                <div class="bbl-row bbl-row-post-submission-status-settings">
                    <label class="bbl-label bbl-col-left bbl-label-post-submission-settings">
						<?php _e( 'On submission', 'buddyblog-pro' ); ?>
                    </label>

                    <div class="bbl-col-right">
						<?php $this->selectbox(
							array(
								'name'     => 'bbl-input-post-status',
								'options'  => array(
									'publish' => __( 'Publish Post', 'buddyblog-pro' ),
									'pending' => __( 'Submit post for review', 'buddyblog-pro' ),
								),
								'selected' => get_post_meta( $form_id, '_buddyblog_post_status', true ),
							)
						);
						?>
                    </div>
                </div> <!-- end post status row -->

                <fieldset class="bbl-section-sub bbl-section-submission-email-admin">
                    <legend><?php _e( 'Form submission admin notification', 'buddyblog-pro' ); ?></legend>
                    <div class="bbl-row bbl-row-post-submission-notification-settings">

                        <label class="bbl-label bbl-col-left bbl-label-post-submission-notification-settings">
			                <?php _e( 'Notify Admin on Post submission', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <label>
                                <input type="radio" name="bbl-input-notify-admin-on-submit" value="1" <?php checked( 1, $notify_admin ); ?> /><?php _e( 'Yes', 'buddyblog-pro' ); ?>
                            </label>
                            <label>
                                <input type="radio" name="bbl-input-notify-admin-on-submit" value="0" <?php checked( 0, $notify_admin ); ?> /><?php _e( 'No', 'buddyblog-pro' ); ?>
                            </label>
                        </div>
                    </div> <!-- end row admin notification on submission -->

                    <div class="bbl-row">
                        <label class="bbl-label bbl-col-left bbl-label-subject">
							<?php _e( 'Subject', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <input type="text" name="bbl-input-submit-admin-notification-subject" placeholder="<?php esc_attr_e( 'Email subject.', 'buddyblog-pro' ); ?>" value="<?php echo esc_attr( $submit_admin_notification_subject ); ?>"/>
                        </div>
                    </div> <!-- end row -->

                    <div class="bbl-row">
                        <label class="bbl-label bbl-col-left bbl-label-message">
							<?php _e( 'Message', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <textarea rows="10" cols="80" name="bbl-input-submit-admin-notification-message"><?php echo esc_textarea( $submit_admin_notification_message ); ?></textarea>
                        </div>
                    </div> <!-- end row -->

                </fieldset><!-- end .bbl-section-published-email-admin -->

                <fieldset class="bbl-section-sub bbl-section-submission-email-author">
                    <legend><?php _e( 'Form submission author notification', 'buddyblog-pro' ); ?></legend>
                    <div class="bbl-row bbl-row-post-submission-notification-settings">

                        <label class="bbl-label bbl-col-left bbl-label-post-submission-notification-settings">
			                <?php _e( 'Notify Author on Post submission', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <label>
                                <input type="radio" name="bbl-input-notify-author-on-submit" value="1" <?php checked( 1, $notify_author_on_submit ); ?> /><?php _e( 'Yes', 'buddyblog-pro' ); ?>
                            </label>
                            <label>
                                <input type="radio" name="bbl-input-notify-author-on-submit" value="0" <?php checked( 0, $notify_author_on_submit ); ?> /><?php _e( 'No', 'buddyblog-pro' ); ?>
                            </label>
                        </div>
                    </div> <!-- end row admin notification on submission -->

                    <div class="bbl-row">
                        <label class="bbl-label bbl-col-left bbl-label-subject">
							<?php _e( 'Subject', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <input type="text" name="bbl-input-submit-author-notification-subject" placeholder="<?php esc_attr_e( 'Email subject.', 'buddyblog-pro' ); ?>" value="<?php echo esc_attr( $submit_author_notification_subject ); ?>"/>
                        </div>
                    </div> <!-- end row -->

                    <div class="bbl-row">
                        <label class="bbl-label bbl-col-left bbl-label-message">
							<?php _e( 'Message', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <textarea rows="10" cols="80" name="bbl-input-submit-author-notification-message"><?php echo esc_textarea( $submit_author_notification_message ); ?></textarea>
                        </div>
                    </div> <!-- end row -->

                </fieldset><!-- end .bbl-section-published-email-admin -->

            </div><!-- end of submission fields -->

			<div class="bbl-form-approval-fields">
				<h3><?php _e( 'Post Approval', 'buddyblog-pro' ); ?></h3>

                <fieldset class="bbl-section-sub bbl-section-approval-email-author">
                    <legend><?php _e( 'Post approval author notification', 'buddyblog-pro' ); ?></legend>
                    <div class="bbl-row bbl-row-post-submission-notification-settings">

                        <label class="bbl-label bbl-col-left bbl-label-post-submission-notification-settings">
							<?php _e( 'Notify Author on post approval', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <label>
                                <input type="radio" name="bbl-input-notify-author-on-approval" value="1" <?php checked( 1, $notify_author_on_approval ); ?> /><?php _e( 'Yes', 'buddyblog-pro' ); ?>
                            </label>
                            <label>
                                <input type="radio" name="bbl-input-notify-author-on-approval" value="0" <?php checked( 0, $notify_author_on_approval ); ?> /><?php _e( 'No', 'buddyblog-pro' ); ?>
                            </label>
                        </div>
                    </div> <!-- end row admin notification on submission -->

					<div class="bbl-row">
						<label class="bbl-label bbl-col-left bbl-label-subject">
							<?php _e( 'Subject', 'buddyblog-pro' ); ?>
						</label>

						<div class="bbl-col-right">
							<input type="text" name="bbl-input-approved-author-notification-subject" placeholder="<?php esc_attr_e( 'Email subject.', 'buddyblog-pro' ); ?>" value="<?php echo esc_attr( $approved_author_notification_subject ); ?>"/>
						</div>
					</div> <!-- end row -->

					<div class="bbl-row">
						<label class="bbl-label bbl-col-left bbl-label-message">
							<?php _e( 'Message', 'buddyblog-pro' ); ?>
						</label>

						<div class="bbl-col-right">
							<textarea rows="10" cols="80" name="bbl-input-approved-author-notification-message"><?php echo esc_textarea( $approved_author_notification_message ); ?></textarea>
						</div>
					</div> <!-- end row -->

				</fieldset><!-- end .bbl-section-published-email-admin -->

			</div>

			<div class="bbl-form-rejection-fields">
				<h3><?php _e( 'Post Rejection', 'buddyblog-pro' ); ?></h3>

				<fieldset class="bbl-section-sub bbl-section-rejection-email-author">
                    <legend><?php _e( 'Post rejection author notification', 'buddyblog-pro' ); ?></legend>
                    <div class="bbl-row bbl-row-post-rejection-notification-settings">

                        <label class="bbl-label bbl-col-left bbl-label-post-submission-notification-settings bbl-col-left">
							<?php _e( 'Notify Author on post rejection', 'buddyblog-pro' ); ?>
                        </label>

                        <div class="bbl-col-right">
                            <label>
                                <input type="radio" name="bbl-input-notify-author-on-rejection" value="1" <?php checked( 1, $notify_author_on_rejection ); ?> /><?php _e( 'Yes', 'buddyblog-pro' ); ?>
                            </label>
                            <label>
                                <input type="radio" name="bbl-input-notify-author-on-rejection" value="0" <?php checked( 0, $notify_author_on_rejection ); ?> /><?php _e( 'No', 'buddyblog-pro' ); ?>
                            </label>
                        </div>
                    </div> <!-- end row admin notification on submission -->

					<div class="bbl-row">
						<label class="bbl-label bbl-col-left bbl-label-subject">
							<?php _e( 'Subject', 'buddyblog-pro' ); ?>
						</label>

						<div class="bbl-col-right">
							<input type="text" name="bbl-input-rejected-author-notification-subject" placeholder="<?php esc_attr_e( 'Email subject.', 'buddyblog-pro' ); ?>" value="<?php echo esc_attr( $rejected_author_notification_subject ); ?>"/>
						</div>
					</div> <!-- end row -->

					<div class="bbl-row">
						<label class="bbl-label bbl-col-left bbl-label-message">
							<?php _e( 'Message', 'buddyblog-pro' ); ?>
						</label>

						<div class="bbl-col-right">
							<textarea rows="10" cols="80" name="bbl-input-rejected-author-notification-message"><?php echo esc_textarea( $rejected_author_notification_message ); ?></textarea>
						</div>
					</div> <!-- end row -->

				</fieldset><!-- end .bbl-section-rejected-email-author -->

			</div>

		</div><!-- end section -->
		<?php
	}

}
