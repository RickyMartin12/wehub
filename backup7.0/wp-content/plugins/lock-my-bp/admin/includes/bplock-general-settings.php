<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// global $bplock;

$general_settings = get_option( 'bplock_general_settings', true );

// print_r( $general_settings );

$lock_bp_components = ( isset( $general_settings['bplock-bp-components'] ) && $general_settings['bplock-bp-components'] == 'on' ) ? 'checked' : '';

$lock_wp_pages = ( isset( $general_settings['bplock-pages'] ) && $general_settings['bplock-pages'] == 'on' ) ? 'checked' : '';

$lock_cpt = ( isset( $general_settings['bplock-custom-post-types'] ) && $general_settings['bplock-custom-post-types'] == 'on' ) ? 'checked' : '';

$locked_content = ( isset( $general_settings['locked_content'] ) ) ? $general_settings['locked_content'] : '';

$lr_form = ( isset( $general_settings['lr-form'] ) ) ? $general_settings['lr-form'] : 'plugin_form';

$cfc_style = ( 'plugin_form' === $lr_form || 'page_redirect' === $lr_form ) ? 'display:none;' : '';

$custom_form_content = ( isset( $general_settings['custom_form_content'] ) ) ? $general_settings['custom_form_content'] : '';

$pages = get_pages();
foreach ( $pages as $page ) {
	$wp_pages[ $page->ID ] = $page->post_title;
}
?>
<div class="wbcom-tab-content">
<form action="options.php" method="POST">
	<?php
	settings_fields( 'bplock_general_settings' );
	do_settings_sections( 'bplock_general_settings' );
	?>
	<div class="wrap">
		<h3><?php esc_html_e( 'General Settings', 'bp-lock' ); ?></h3>
		<div class='bplock-general-settings-container'>
			<table class="form-table">
				<tbody>
					<!-- LOCK BuddyPress COMPONENTS, IF BP ACTIVE -->
					<?php if ( BPLOCK_IS_MULTISITE && BPLOCK_IS_BP_NETWORK_ACTIVE ) { ?>
						<tr>
							<th scope="row"><label for="bplock-bp-components"><?php esc_html_e( 'Lock BuddyPress Components', 'bp-lock' ); ?></label></th>
							<td>
								<label class="wb-switch">
									<input type="checkbox" id="bplock-bp-components" name="bplock_general_settings[bplock-bp-components]" <?php echo esc_html( $lock_bp_components ); ?>>
									<span class="wb-slider wb-round"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Switch above option on, if you want to restrict access for BuddyPress Components.', 'bp-lock' ); ?></p>
							</td>
						</tr>
					<?php } elseif ( BPLOCK_IS_BP_ACTIVE ) { ?>
						<tr>
							<th scope="row"><label for="bplock-bp-components"><?php esc_html_e( 'Lock BuddyPress Components', 'bp-lock' ); ?></label></th>
							<td>
								<label class="wb-switch">
									<input type="checkbox" id="bplock-bp-components" name="bplock_general_settings[bplock-bp-components]" <?php echo esc_html( $lock_bp_components ); ?>>
									<span class="wb-slider wb-round"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Switch above option on, if you want to to restrict access BuddyPress Components.', 'bp-lock' ); ?></p>
							</td>
						</tr>
					<?php } ?>

					<!-- LOCK CUSTOM POST TYPES -->
					<!-- <tr>
						<th scope="row"><label for="bplock-custom-post-types"><?php // esc_html_e( 'Lock Custom Post Types', 'bp-lock' ); ?></label></th>
						<td>
							<label class="wb-switch">
								<input type="checkbox" id="bplock-custom-post-types" name="bplock_general_settings[bplock-custom-post-types]" <?php // echo $lock_cpt; ?>>
								<span class="wb-slider wb-round"></span>
							</label>
							<p class="description"><?php // esc_html_e( 'Switch above option on, if you want to restrict access for Custom Post Types.', 'bp-lock' ); ?></p>
						</td>
					</tr> -->

					<!-- LOCK PAGES -->
					<tr>
						<th scope="row"><label for="bplock-pages"><?php esc_html_e( 'Lock Pages', 'bp-lock' ); ?></label></th>
						<td>
							<label class="wb-switch">
								<input type="checkbox" id="bplock-pages" name="bplock_general_settings[bplock-pages]" <?php echo esc_html( $lock_wp_pages ); ?>>
								<span class="wb-slider wb-round"></span>
							</label>
							<p class="description"><?php esc_html_e( 'Switch above option on, if you want to restrict access for WordPress Pages.', 'bp-lock' ); ?></p>
						</td>
					</tr>

					<!-- Login/Registration form -->
					<tr>
						<th scope="row"><label for="bplock-pages"><?php esc_html_e( 'Login Registration Form', 'bp-lock' ); ?></label></th>
						<!-- <td>
							<input id="plugin_form" type="radio" class="lr-form" data-id="plugin_form" name="bplock_general_settings[lr-form]" value="plugin_form" <?php echo checked( 'plugin_form', $lr_form ); ?>>
							<label for="plugin_form"><?php esc_html_e( 'Use plugin login/registration form', 'bp-lock' ); ?></label>
							<input id="custom_form" type="radio" class="lr-form" data-id="custom_form" name="bplock_general_settings[lr-form]" value="custom_form" <?php echo checked( 'custom_form', $lr_form ); ?>>
							<label for="custom_form"><?php esc_html_e( 'Custom Form', 'bp-lock' ); ?></label>
						</td> -->
						<td>
						<input id="plugin_form" type="radio" class="lr-form" data-id="plugin_form" name="bplock_general_settings[lr-form]" value="plugin_form" <?php echo checked( 'plugin_form', $lr_form ); ?>>
						<label for="plugin_form"><?php esc_html_e( "Use Plugin's Template", 'bp-lock' ); ?></label>
						<input id="custom_form" type="radio" class="lr-form" data-id="custom_form" name="bplock_general_settings[lr-form]" value="custom_form" <?php echo checked( 'custom_form', $lr_form ); ?>>
						<label for="custom_form"><?php esc_html_e( '3rd Party Plugin Shortcode', 'bp-lock' ); ?></label>
						<input id="page_redirect" type="radio" class="lr-form" data-id="page_redirect" name="bplock_general_settings[lr-form]" value="page_redirect" <?php echo checked( 'page_redirect', $lr_form ); ?>>
						<label for="page_redirect"><?php esc_html_e( 'Redirect to page', 'bp-lock' ); ?></label>
					</td>
					</tr>

					<!-- Custom form textarea -->
					<tr class="custom_form" style="<?php echo esc_attr( $cfc_style ); ?>">
						<th scope="row"><label for="bplock-txtarea"><?php esc_html_e( 'Custom form content', 'bp-lock' ); ?></label></th>
						<td>
							<input type="text" id="custom_form_content" name="bplock_general_settings[custom_form_content]" value="<?php echo esc_attr( $custom_form_content ); ?>">
							<p class="description"><?php echo esc_html__( 'Please insert a login/registration form shortcode.', 'bp-lock' ); ?></p>
						</td>
					</tr>

					<?php

					if ( 'page_redirect' === $lr_form ) {
						$style = '';
					} else {
						$style = 'display:none;';
					}
					?>

						<tr id="logout_redirect_page" style="<?php echo esc_attr( $style ); ?>">
							<th scope="row"><label for="blogname"><?php esc_html_e( 'Select Page to redirect', 'bp-lock' ); ?></label></th>
							<td>
								<select id="blpro-page-list" name="bplock_general_settings[logout_redirect_page]">
									<?php if ( ! empty( $wp_pages ) ) { ?>
										<?php foreach ( $wp_pages as $pgid => $wp_page ) { ?>
											<?php $selected = ( ! empty( $general_settings['logout_redirect_page'] ) && $pgid == $general_settings['logout_redirect_page'] ) ? 'selected' : ''; ?>
									<option value="<?php echo esc_attr( $pgid ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $wp_page ); ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							<p class="description" id="tagline-description"><?php esc_html_e( 'Select the pages you wish to redirect the restricted user roles. These pages will be locked for logged out users.', 'bp-lock' ); ?>
							</p>
							</td>
						</tr>

					<!-- DISPLAY CONTENT -->

					<?php
					if ( 'page_redirect' === $lr_form ) {
						$style = 'display:none;';
					} else {
						$style = '';
					}
					?>
					<tr style="<?php echo esc_attr( $style ); ?>">
						<th scope="row"><label for="bplock-display-content"><?php esc_html_e( 'Custom restriction message', 'bp-lock' ); ?></label></th>
						<td>
							<?php
							$options = array(
								'textarea_rows' => 5,
								'textarea_name' => 'bplock_general_settings[locked_content]',
							);
							?>
							<?php
							if ( empty( $locked_content ) ) {
								$locked_content = apply_filters( 'bploc_default_locked_message', esc_html__( 'Hey Member! Thanks for checking this page out -- however, it’s restricted to logged members only. If you’d like to access it, please login to the website.', 'bp-lock' ) );
							}
							wp_editor( $locked_content, 'bplock-locked-content', $options );
							?>
							<p class="description"><?php esc_html_e( 'Above message will be displayed on the protected pages.', 'bp-lock' ); ?></p>
						</td>
					</tr>

				</tbody>
			</table>
			<?php submit_button(); ?>
			<p class="submit">
				<?php // wp_nonce_field( 'bplock-general', 'bplock-general-settings-nonce'); ?>
				<!-- <input type="submit" name="bplock-general-settings-submit" class="button button-primary" value="<?php // esc_html_e('Save Changes', 'bp-lock'); ?>"> -->
			</p>
		</div>
	</div>
</form>
</div>
