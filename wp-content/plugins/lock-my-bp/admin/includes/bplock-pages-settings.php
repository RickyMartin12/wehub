<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $bplock;
$pages    = get_pages();
$bp_pages = '';
$wp_pages = array();
if ( BPLOCK_IS_BP_ACTIVE ) {
	$bp_pages = get_option( 'bp-pages' );
}
foreach ( $pages as $page ) {
	$wp_pages[ $page->ID ] = $page->post_title;
}
foreach ( $wp_pages as $pgid => $pg_title ) {
	if ( $bp_pages && in_array( $pgid, $bp_pages ) ) {
		// unset( $wp_pages[$pgid] );
	}
}

$locked_pages = get_option( 'bplock-pages', true );
?>
<div class="wbcom-tab-content">
<form action="options.php" method="POST">
	<?php
		settings_fields( 'bplock-pages' );
		do_settings_sections( 'bplock-pages' );
	?>
	<div class="wrap">
		<h3><?php esc_html_e( 'Pages Lock Settings', 'bp-lock' ); ?></h3>
		<div class='bplock-pages-settings-container'>
			<table class="form-table">
				<tbody>
					<!-- PAGES LIST -->
					<tr>
						<th scope="row"><label for="bplock-pages-list"><?php esc_html_e( 'WordPress Pages', 'bp-lock' ); ?></label></th>
						<td>
							<p class="wcpq-selection-tags">
								<a href="javascript:void(0);" id="bplock-select-all"><?php esc_html_e( 'Select All', 'bp-lock' ); ?></a> /
								<a href="javascript:void(0);" id="bplock-unselect-all"><?php esc_html_e( 'Unselect All', 'bp-lock' ); ?></a>
							</p>
							<select id="bplock-pages-list" name="bplock-pages[]" multiple>
								<?php if ( ! empty( $wp_pages ) ) { ?>
									<?php foreach ( $wp_pages as $pgid => $wp_page ) { ?>
										<?php $selected = ( is_array( $locked_pages ) && in_array( $pgid, $locked_pages ) ) ? 'selected' : ''; ?>
										<option value="<?php echo esc_attr( $pgid ); ?>" <?php echo esc_html( $selected ); ?>><?php echo esc_html( $wp_page ); ?></option>
									<?php } ?>
								<?php } ?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the WordPress pages that you wish to get locked for loggedout users.', 'bp-lock' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
			<!-- <p class="submit"> -->
				<?php // wp_nonce_field( 'bplock-pages', 'bplock-pages-settings-nonce'); ?>
				<!-- <input type="submit" name="bplock-pages-settings-submit" class="button button-primary" value="<?php // esc_html_e('Save Changes', 'bp-lock'); ?>"> -->
			<!-- </p> -->
		</div>
	</div>
</form>
</div>
