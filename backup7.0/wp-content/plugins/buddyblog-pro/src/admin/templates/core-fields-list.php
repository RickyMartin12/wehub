<?php
/**
 * Fields List helper on the form edit page
 *
 * Lists core fields as table
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

// assume that $form_id is available.
$core_fields = bblpro_form_get_core_fields( $form_id );
if ( empty( $core_fields ) ) {
	return;
}


?>
<table id="bbl-post-field-fields" class="bbl-table widefat" data-form-id="<?php echo esc_attr( $form_id ); ?>">
    <thead>
    <tr>
        <th class="bbl-post-field-number">#</th>
        <th class="bbl-post-field-content"><?php _e( 'Content', 'buddyblog-pro' ); ?></th>
        <th class="bbl-post-field-delete"><?php _e( 'Delete', 'buddyblog-pro' ); ?></th>
    </tr>
    </thead>
    <tbody id="bbl-sortable-post-fields">
    <?php $count = 0;?>
	<?php foreach ( $core_fields as $field_type => $setting ) : $count++;?>
        <tr class="bbl-post-field-field-row" data-field-type="<?php echo esc_attr( $field_type ); ?>">
            <td class="bbl-post-field-number"><?php echo $count;?></td>
            <td class="bbl-post-field-content">
				<?php echo esc_html( bblpro_get_core_field_label( $field_type ) ); ?>
                <br />
	            <?php if ( ! empty( $setting['is_required'] ) ) : ?>

		            <?php _e( 'Required: Yes', 'buddyblog-pro' ); ?>
	            <?php else: ?>
		            <?php _e( 'Required: No', 'buddyblog-pro' ); ?>
	            <?php endif; ?>
	            <?php if ( 'post_content' === $field_type || 'post_excerpt' == $field_type ) : ?>
                    , <?php printf( __( 'Use Editor: %s', 'buddyblog-pro' ), empty( $setting['use_editor'] ) ? __( 'No', 'buddyblog-pro' ) : blpro_get_supported_editor_name( $setting['use_editor'] ) ); ?>
	            <?php endif; ?>
	            <?php if ( ! empty( $setting['placeholder'] ) ): ?>
                    <p><?php _e( 'Placeholder:', 'buddyblog-pro' ); ?> <?php echo wp_kses_data( $setting['placeholder'] ); ?></p>
	            <?php endif; ?>
            </td>
            <td class="bbl-post-field-delete">
                <a href="#" title="<?php echo esc_attr( __( 'Delete this item', 'buddyblog-pro' ) ); ?>"><span class="dashicons dashicons-trash"></span></a>
                <input type="hidden" name="bbl-post-field-selected-field[]" value="<?php echo esc_attr( $field_type ); ?>" class="bbl-post-field-selected-field">
            </td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>
