<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$bp_components = array(
	'members'  => esc_html__( 'Members', 'bp-lock' ),
	'groups'   => esc_html__( 'Groups', 'bp-lock' ),
	'activity' => esc_html__( 'Site Wide Activity', 'bp-lock' ),
);

$locked_bp_components = get_option( 'bp-components', true );

if ( BPLOCK_IS_MULTISITE && BPLOCK_IS_BP_NETWORK_ACTIVE ) {
	$bp_components['blogs'] = esc_html__( 'Blogs (Sites)', 'bp-lock' );
}
?>
<div class="wbcom-tab-content">
<form action="options.php" method="POST">
	<?php
	settings_fields( 'bp-components' );
	do_settings_sections( 'bp-components' );
	?>
	<div class="wrap">
		<h3><?php esc_html_e( 'BuddyPress Components Settings', 'bp-lock' ); ?></h3>
		<div class='bplock-bp-components-settings-container'>
			<table class="form-table">
				<tbody>
					<!-- BuddyPress COMPONENTS LIST -->
					<tr>
						<th scope="row"><label for="bplock-bp-components-list"><?php esc_html_e( 'BuddyPress Components', 'bp-lock' ); ?></label></th>
						<td>
							<select id="bplock-bp-components-list" name="bp-components[]" multiple>
								<?php if ( ! empty( $bp_components ) ) { ?>
									<?php foreach ( $bp_components as $slug => $bp_component ) { ?>
										<?php $selected = ( is_array( $locked_bp_components ) && in_array( $slug, $locked_bp_components ) ) ? 'selected' : ''; ?>
										<option value="<?php echo esc_attr( $slug ); ?>" <?php echo esc_html( $selected ); ?>><?php echo esc_html( $bp_component ); ?></option>
									<?php } ?>
								<?php } ?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the buddypress components that you wish to restrict access for logged out users.', 'bp-lock' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
			<!-- <p class="submit"> -->
				<?php // wp_nonce_field( 'bplock-bp-components', 'bplock-bp-components-settings-nonce' ); ?>
				<!-- <input type="submit" name="bplock-bp-components-settings-submit" class="button button-primary" value="<?php // esc_html_e( 'Save Changes', 'bp-lock' ); ?>"> -->
			<!-- </p> -->
		</div>
	</div>
</form>
</div>
