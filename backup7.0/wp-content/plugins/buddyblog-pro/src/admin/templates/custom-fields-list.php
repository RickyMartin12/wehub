<?php
/**
 * Fields List helper on the form edit page
 *
 * Lists custom fields as table
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
use BuddyBlog_Pro\Core\Fields\BBL_Field_Type_Invalid;

defined( 'ABSPATH' ) || exit;

// assume that $form_id is available.
$custom_fields = get_post_meta( $form_id, '_buddyblog_custom_fields', true );
if ( empty( $custom_fields ) ) {
	return;
}

?>
<table id="bbl-custom-fields-list" class="bbl-table widefat" data-form-id="<?php echo esc_attr( $form_id ); ?>">
    <thead>
    <tr>
        <th class="bblogpro-cf-number">#</th>
        <th class="bblogpro-cf-content"><?php _ex( 'Content', 'admin cusotm fields table header', 'buddyblog-pro' ); ?></th>
        <th class="bblogpro-cf-delete"><?php _ex( 'Delete', 'admin custom feields table header', 'buddyblog-pro' ); ?> </th>
    </tr>
    </thead>
    <tbody id="bbl-sortable-custom-fields">
	<?php $count = 0; ?>
	<?php foreach ( $custom_fields as $filed_key => $setting ) : $count ++;
		$filed_type_object = bblpro_get_field_type_object( $setting['type'] );
		if ( ! $filed_type_object ) {
			$filed_type_object = new BBL_Field_Type_Invalid();
		}
        ?>
        <tr class="bblogpro-custom-field-row" data-field-key="<?php echo esc_attr( $filed_key ); ?>">
            <td class="bblogpro-custom-number"><?php echo $count; ?></td>
            <td class="bblogpro-custom-content">
               <?php $filed_type_object->admin_fields_list_field_markup( $setting );?>
                <p>
		            <?php _e('Shortcode:', 'buddyblog-pro');?>
                    <span id="bbl-shortcode-meta-key-<?php echo esc_attr( $setting['key'] );?>"><?php $code = sprintf( '[bbl-meta key="%s"]', $setting['key']);
			            echo $code;
			            ?> </span>
                    <a href="#" class="bbl-shortcode-copy" title="<?php esc_attr_e( 'Copy shortcode', 'buddyblog-pro');?>" data-clipboard-target="#bbl-shortcode-meta-key-<?php echo esc_attr( $setting['key'] );?>"><img src="<?php echo esc_url(buddyblog_pro()->url.'src/admin/assets/images/copy.svg');?>" /></a>
                </p>
            </td>
            <td class="bbl-cf-delete">
                <a href="#" title="<?php echo esc_attr( __( 'Delete this field', 'buddyblog-pro' ) ); ?>"><span class="dashicons dashicons-trash"></span></a>
                <input type="hidden" class="bbl-cf-selected-field" name="bbl-cf-selected-field[]" value="<?php echo esc_attr( $filed_key ); ?>">
            </td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>
