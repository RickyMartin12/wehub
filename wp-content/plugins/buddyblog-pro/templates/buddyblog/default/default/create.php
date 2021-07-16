<?php
/**
 * Create Post template page
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
		<?php bblpro_render_form( bblpro_get_current_form() ); ?>

		<div class="bbl-submit-form-panel">
			<?php if ( bblpro_is_draft_button_enabled( get_current_user_id(), bblpro_get_current_post_type() ) ) : ?>
				<div class="bbl-submission-button-wrapper bbl-draft-button-wrapper">
					<input type="button" id="bbl-draft-button" value="<?php esc_attr_e( 'Save Draft', 'buddyblog-pro' ); ?>"/>
					<span class="bblpro-ajax-loader bblpro-ajax-loader-hidden"><img src="<?php echo esc_url( bblpro_locate_asset( 'assets/images/ajax-loader.gif' ) ); ?>"></span>
				</div>
			<?php endif; ?>

			<div class='bbl-submission-button-wrapper bbl-submit-button-wrapper'>
				<input id="bbl-submit-button" type="submit" value="<?php echo esc_attr( bblpro_get_submit_button_label( bblpro_get_current_post_type() ) ); ?>"/>
			</div>
		</div><!-- end of submit form panel -->

	</form>

</div><!-- end of form wrapper -->
