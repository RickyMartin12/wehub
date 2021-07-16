<?php
/**
 * Edit Post template page
 *
 * @package    BuddyBlog_Pro
 * @subpackage Templates/default/default
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

?>
<div class="bblpro-form-wrapper">

	<form class="standard-form bblpro-post-form" method="post" action="" enctype="multipart/form-data">
		<?php
		// Render form.
		bblpro_render_form( bblpro_get_current_form(), bblpro_get_current_editable_post() );
		?>

		<div class="bbl-submit-form-panel">
			<?php if ( bblpro_is_post_status( bblpro_get_current_editable_post(), 'publish' ) ) : // published post. ?>
				<?php if ( bblpro_user_can_draft_post( bp_loggedin_user_id(), bblpro_get_current_editable_post_id() ) ) : ?>
					<div class="bbl-submission-button-wrapper bbl-revert-draft-button-wrapper">
						<input type="button" id="bbl-revert-draft-button" value="<?php esc_attr_e( 'Revert to Draft', 'buddyblog-pro' ); ?>"/>
						<span class="bblpro-ajax-loader bblpro-ajax-loader-hidden"><img src="<?php echo esc_url( bblpro_locate_asset( 'assets/images/ajax-loader.gif' ) ); ?>"></span>
					</div>
				<?php endif; ?>

			<?php else : ?>
				<?php if ( bblpro_is_draft_button_enabled( bp_loggedin_user_id(), bblpro_get_current_post_type() ) ) : ?>
					<div class="bbl-submission-button-wrapper bbl-draft-button-wrapper">
						<input type="button" id="bbl-draft-button" value="<?php esc_attr_e( 'Save Draft', 'buddyblog-pro' ); ?>"/>
						<span class="bblpro-ajax-loader bblpro-ajax-loader-hidden"><img src="<?php echo esc_url( bblpro_locate_asset( 'assets/images/ajax-loader.gif' ) ); ?>"></span>
					</div>
				<?php endif; ?>

			<?php endif; ?>

			<div class='bbl-submission-button-wrapper bbl-submit-button-wrapper'>
				<input id="bbl-edit-submit-button" type="submit" value="<?php echo esc_attr( bblpro_get_submit_button_label( bblpro_get_current_post_type(), bblpro_get_current_editable_post() ) ); ?>"/>
			</div>
		</div><!--end of form submit panel -->

	</form>

</div><!-- end of form wrapper -->
