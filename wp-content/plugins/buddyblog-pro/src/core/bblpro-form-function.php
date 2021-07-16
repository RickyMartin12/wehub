<?php
/**
 * Form related functions
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

use BuddyBlog_Pro\Core\Terms_Checklist_Walker;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Get the form.
 *
 * @param int $form_id form id.
 *
 * @return WP_Post|null
 */
function bblpro_get_form( $form_id ) {

	if ( ! $form_id || ! bblpro_is_valid_form( $form_id ) ) {
		return null;
	}

	return get_post( $form_id );
}

/**
 * Get current form being processed.
 *
 * @return WP_Post|null
 */
function bblpro_get_current_form() {
	return bblpro_get_form_for_post_type( bblpro_get_current_post_type() );
}

/**
 * Get id of the current form being processed.
 *
 * @return int
 */
function bblpro_get_current_form_id() {
	$form = bblpro_get_current_form();

	return $form ? $form->ID : 0;
}

/**
 * Is it a valid form?
 *
 * @param int $form_id form id.
 *
 * @return bool
 */
function bblpro_is_valid_form( $form_id ) {

	$form = get_post( $form_id );

	return $form && 'publish' === $form->post_status;
}

/**
 * Get the associated form for the given post type.
 *
 * @param string $post_type post type.
 *
 * @return WP_Post|null
 */
function bblpro_get_form_for_post_type( $post_type ) {

	if ( ! $post_type ) {
		return null;
	}

	$form_id = bblpro_get_option( "{$post_type}_form_id", 0 );

	return bblpro_get_form( $form_id );
}

/**
 * Get post type supported by this form.
 *
 * @param int $form_id id.
 *
 * @return string|null
 */
function bblpro_form_get_post_type( $form_id ) {
	return get_post_meta( $form_id, '_buddyblog_post_type', true );
}

/**
 * Get post status supported by this form.
 *
 * @param int $form_id id.
 *
 * @return string|null
 */
function bblpro_form_get_post_status( $form_id ) {
	return get_post_meta( $form_id, '_buddyblog_post_status', true );
}

/**
 * Checks if post visibility change by author is allowed?
 *
 * @param int $form_id form id.
 *
 * @return bool
 */
function bblpro_form_enable_post_visibility_control( $form_id ) {
	return (bool) get_post_meta( $form_id, '_buddyblog_enable_post_visibility', true );
}

/**
 * Check if comment status change by author is allowed.
 *
 * @param int $form_id form id.
 *
 * @return bool
 */
function bblpro_form_enable_comment_status_control( $form_id ) {
	return (bool) get_post_meta( $form_id, '_buddyblog_allow_custom_comment_status', true );
}

/**
 * Get default comment status.
 *
 * @param int $form_id form id.
 *
 * @return string
 */
function bblpro_form_get_default_comment_status( $form_id ) {
	return get_post_meta( $form_id, '_buddyblog_comment_status', true );
}

/**
 * Does the form support post thumbnail?
 *
 * @param int $form_id form id.
 *
 * @return bool
 */
function bblpro_form_supports_post_thumbnail( $form_id ) {

	$core_fields = bblpro_form_get_core_fields( $form_id );

	return isset( $core_fields['thumbnail'] );
}

/**
 * Checks if we should use rich text editor for the main content.
 *
 * @param int $form_id form id.
 *
 * @return bool
 */
function bblpro_form_supports_rich_text_editor( $form_id ) {

	$core_fields = bblpro_form_get_core_fields( $form_id );

	return isset( $core_fields['post_content'] ) && isset( $core_fields['post_content']['use_editor'] ) && $core_fields['post_content']['use_editor'];
}

/**
 * Does the content editor support media?
 *
 * @param int $form_id form id.
 *
 * @return bool
 */
function bbpro_form_editor_supports_media( $form_id ) {
	return true;
}
/**
 * Saves custom fields associated with the form.
 *
 * @param int   $form_id form id.
 * @param array $fields field ids.
 *
 * @return bool|int
 */
function bblpro_form_set_custom_fields( $form_id, $fields ) {
	return update_post_meta( $form_id, '_buddyblog_custom_fields', $fields );
}

/**
 * Get all custom fields registered(including our hidden field for identifying buddyblog post).
 *
 * @param int $form_id form id.
 *
 * @todo cache the settings to optimize.
 *
 * @return array
 */
function bblpro_form_get_custom_fields( $form_id ) {

	$fields = get_post_meta( $form_id, '_buddyblog_custom_fields', true );

	if ( empty( $fields ) ) {
		$fields = array();
	}

	$fields['_is_buddyblog_post'] = array(
		'type'    => 'hidden',
		'key'     => '_is_buddyblog_post',
		'label'   => '',
		'default' => 1,
	);

	return $fields;
}

/**
 * Get the custom field details for the given key.
 *
 * @param int    $form_id form id.
 * @param string $key custom fiel meta key.
 *
 * @return array
 */
function bblpro_form_get_custom_field( $form_id, $key ) {

	$fields = bblpro_form_get_custom_fields( $form_id );

	return isset( $fields[ $key ] ) ? $fields[ $key ] : array();
}

/**
 * Check if the given field type is multi valued.
 *
 * @param string $field_type field type.
 *
 * @return bool
 */
function bblpro_form_is_multi_valued_field_type( $field_type ) {

	$object = bblpro_get_field_type_object( $field_type );

	if ( ! $object ) {
		return null;
	}

	return $object->supports( 'multiple' );
}

/**
 * Is it multi valued field.
 *
 * @param int    $form_id form id.
 * @param string $field_name field name(meta key).
 *
 * @return string
 */
function bblpro_form_is_multi_valued_field( $form_id, $field_name ) {
	return bblpro_form_is_multi_valued_field_type( bblpro_form_get_custom_field_type( $form_id, $field_name ) );
}

/**
 * Get the custom field type.
 *
 * @param int    $form_id form id.
 * @param string $field_name field name(meta key).
 *
 * @return string
 */
function bblpro_form_get_custom_field_type( $form_id, $field_name ) {

	$field = bblpro_form_get_custom_field( $form_id, $field_name );

	if ( empty( $field ) ) {
		return null;
	}

	return $field['type'];
}

/**
 * Is taxonomy enabled for the form.
 *
 * @param int $form_id form id.
 *
 * @return bool
 */
function bblpro_form_has_taxonomy_enabled( $form_id ) {
	return (bool) get_post_meta( $form_id, '_buddyblog_enable_taxonomy', true );
}

/**
 * Get taxonomies.
 *
 * @param int $form_id form id.
 *
 * @return array
 */
function bblpro_form_get_taxonomies( $form_id ) {
	$taxonomies = get_post_meta( $form_id, '_buddyblog_enabled_taxonomies', true );

	if ( empty( $taxonomies ) ) {
		$taxonomies = array();
	}

	return $taxonomies;
}

function bblpro_form_enable_term_creation( $form_id, $taxonomy ) {

}

function bblpro_form_get_default_term_id( $form_id, $taxonomy ) {

}

/**
 * Get an array of supported core fields by this form.
 *
 * @param int $form_id form id.
 *
 * @return array
 */
function bblpro_form_get_core_fields( $form_id ) {

	$fields = get_post_meta( $form_id, '_buddyblog_core_fields', true );

	if ( empty( $fields ) ) {
		return array();
	}

	return $fields;
}

/**
 * Saves core fields associated with the form.
 *
 * @param int   $form_id form id.
 * @param array $fields field ids.
 *
 * @return bool|int
 */
function bblpro_form_set_core_fields( $form_id, $fields ) {
	return update_post_meta( $form_id, '_buddyblog_core_fields', $fields );
}

/**
 * Validate a form for required fields.
 *
 * @param int   $form_id form id.
 * @param array $data data.
 * @param array $prepared_post_data data prepared for inserting/updating post.
 *
 * @return WP_Error
 */
function bblpro_validate_form_data( $form_id, $data = array(), $prepared_post_data = array() ) {

	$defaults = array(
		'bbl_post_title'   => '',
		'bbl_post_content' => '',
		'bbl_post_excerpt' => '',
	);

	$errors = buddyblog_pro()->errors;

	if ( empty( $data ) ) {
		$errors->add( 'empty_data', _x( 'Empty.', 'Post form validation message', 'buddyblog-pro' ) );

		return $errors;
	}

	$data = wp_parse_args( $data, $defaults );
	// core fields.
	// we will manually validate each core fields.
	$core_fields = bblpro_form_get_core_fields( $form_id );

	// validate post title.
	if ( ! empty( $core_fields['post_title'] ) ) {
		if ( empty( $data['bbl_post_title'] ) && $core_fields['post_title']['is_required'] ) {
			$errors->add( 'bbl_post_title', _x( 'Title is required.', 'Post form validation message', 'buddyblog-pro' ) );
		}
	}

	// validate post content.
	if ( ! empty( $core_fields['post_content'] ) ) {
		if ( empty( $data['bbl_post_content'] ) && $core_fields['post_content']['is_required'] ) {
			$errors->add( 'bbl_post_content', _x( 'Content is required.', 'Post form validation message', 'buddyblog-pro' ) );
		}
	}

	// validate post excerpt.
	if ( ! empty( $core_fields['post_excerpt'] ) ) {
		if ( empty( $data['bbl_post_excerpt'] ) && $core_fields['post_excerpt']['is_required'] ) {
			$errors->add( 'bbl_post_excerpt', _x( 'Excerpt is required.', 'Post form validation message', 'buddyblog-pro' ) );
		}
	}

	// should we validate taxonomy? Not now.
	// custom fields.
	$custom_fields = bblpro_form_get_custom_fields( $form_id );

	$cf_data = isset( $data['bbl_custom_field'] ) ? $data['bbl_custom_field'] : array();
	// make sure the required field is present.
	foreach ( $custom_fields as $key => $custom_field ) {
		if ( empty( $custom_field['is_required'] ) ) {
			continue;
		}

		$val = isset( $cf_data[ $key ] ) ? $cf_data[ $key ] : null;
		if ( $val || '0' === $val ) {
			continue;
		}

		/* translators: %s: field label */
		$errors->add( 'bbl_cf_' . $key, sprintf( _x( '%s is required.', 'Post form validation message', 'buddyblog-pro' ), $custom_field['label'] ) );
	}

	// validate data using field type.
	foreach ( $custom_fields as $key => $custom_field ) {

		$field_type_object = bblpro_get_field_type_object( $custom_field['type'] );
		// Not a registered type, we mark the check failed.
		if ( ! $field_type_object ) {
			/* translators: %s : Custom Field type label */
			$errors->add( 'bbl_cf_' . $key, sprintf( _x( '%s is not a registered type.', 'Post form validation message', 'buddyblog-pro' ), $field_type_object->label ) );
			continue;
		}

		$val       = isset( $cf_data[ $key ] ) ? $cf_data[ $key ] : null;
		$validated = $field_type_object->validate_value( $val, $custom_field, $form_id, $prepared_post_data['ID'] );
		// did validation failed, if yes, ingest the error.
		if ( is_wp_error( $validated ) ) {
			$errors->add( 'bbl_cf_' . $key, $validated->get_error_message() );
		}
	}

	return apply_filters( 'bblpro_validation_errors', $errors, $data, $prepared_post_data, $form_id );
}

/**
 * Render form.
 *
 * @param int|WP_Post $form form id or object.
 * @param WP_Post     $post to be editing.
 */
function bblpro_render_form( $form, $post = null ) {

	if ( ! bblpro_is_valid_form( $form ) ) {
		return;
	}

	$form_id = get_post( $form )->ID;
	$post_id = 0;

	if ( ! $post ) {
		$post = bblpro_get_default_post_to_edit( bblpro_get_current_post_type(), true );
	}

	if ( $post && $post->ID ) {
		$post_id = $post->ID;
	}

	echo '<div class="bbl-edit-form-panel">';

	// global notices.
	bblpro_print_notices();
	// should we check for form access?
	// Let us leave it to handler.
	do_action( 'bblpro_form_before_form_fields', $form_id, $post  );

	// settings section?
	bblpro_form_render_post_thumbnails( $form_id, $post, $_POST );

	bblpro_form_render_core_fields( $form_id, $post, $_POST );

	bblpro_form_render_taxonomies( $form_id, $post, isset( $_POST['tax_input'] ) ? wp_unslash( $_POST['tax_input'] ) : array() );

	bblpro_form_render_custom_fields( $form_id, $post, isset( $_POST['bbl_custom_field'] ) ? wp_unslash( $_POST['bbl_custom_field'] ) : array() );

	do_action( 'bblpro_form_after_form_fields', $form_id, $post );

	bblpro_form_render_post_settings( $form_id, $post, $_POST );

	echo "<input type='hidden' name='bbl_form_id' value='{$form_id}' />";
	echo "<input type='hidden' name='bbl_post_id' id='bbl_post_id' value='{$post_id}' />";
	wp_nonce_field( 'bbl_edit_post', '_bbl_form_nonce' );

	if ( $post_id ) {
		wp_nonce_field( 'update-post_' . $post_id, '_bbl_update_post_nonce' );
	}
	echo '</div>'; // end of edit panel .
}

/**
 * Print global notices.
 */
function bblpro_print_notices() {

	$notices     = buddyblog_pro()->notices;
	$notice_type = '';
	$message     = '';

	if ( $notices->get_error_message( 'success' ) ) {
		$notice_type = 'success';
		$message     = $notices->get_error_message( 'success' );
	} elseif ( $notices->get_error_message( 'error' ) ) {
		$notice_type = 'error';
		$message     = $notices->get_error_message( 'error' );
	}

	if ( $notice_type && $message ) {
		$notice_type = esc_attr( $notice_type );
		echo "<div class='bbl-global-notices bbl-{$notice_type}-notices'>" . $message . '</div>';
	}
}

/**
 * Render core fields.
 *
 * @param int     $form_id form id.
 * @param WP_Post $post post being edited.
 * @param array   $data posted form data.
 */
function bblpro_form_render_core_fields( $form_id, $post = null, $data = array() ) {

	$core_fields = bblpro_form_get_core_fields( $form_id );

	if ( empty( $core_fields ) ) {
		return;
	}

	do_action( 'bblpro_form_before_core_fields', $form_id, $post, $data );

	echo "<div class='bbl-edit-section bbl-edit-section-core-fields'>";

	// core fields.
	foreach ( $core_fields as $name => $field ) {
		$val = isset( $data[ 'bbl_' . $name ] ) ? $data[ 'bbl_' . $name ] : null;
		if ( is_null( $val ) && $post ) {
			//$key = 'post_' . $name; // post_title, post_content.
			$val = isset( $post->{$name} ) ? $post->{$name} : '';
		}

		echo bblpro_form_get_core_field_markup( $form_id, $field, $val );
	}

	do_action( 'bblpro_form_core_fields', $form_id, $post, $data );

	echo '</div>';
	do_action( 'bblpro_form_after_core_fields', $form_id, $post, $data );
}

/**
 * Render core fields.
 *
 * @param int     $form_id form id.
 * @param WP_Post $post post being edited.
 * @param array   $data posted form data.
 */
function bblpro_form_render_post_settings( $form_id, $post = null, $data = array() ) {

	$comment_status = isset( $data['bbl_comment_status'] ) ? $data['bbl_comment_status'] : null;

	if ( ! $comment_status ) {
		$comment_status = $post ? $post->comment_status : bblpro_form_get_default_comment_status( $form_id );
	}
	do_action( 'bblpro_form_before_post_settings', $form_id, $post, $data );
	?>
 <div class='bbl-edit-section bbl-edit-section-settings'>

		<?php if ( bblpro_form_enable_comment_status_control( $form_id ) ): ?>
			<label>
				<?php _e( 'Comment status', 'buddyblog-pro' ); ?>
				<select name="bbl_comment_status">
					<option value="open" <?php selected( 'open', $comment_status ); ?>><?php _e( 'Open', 'buddyblog-pro' ); ?></option>
					<option value="closed" <?php selected( 'closed', $comment_status ); ?>><?php _e( 'Closed', 'buddyblog-pro' ); ?></option>
				</select>
			</label>
		<?php endif; ?>
		<?php do_action( 'bblpro_form_post_settings', $form_id, $post, $data );?>
	</div>

	<?php
	do_action( 'bblpro_form_after_post_settings', $form_id, $post, $data );
}

/**
 * Render post thumbnail field.
 *
 * @param int     $form_id form id.
 * @param WP_Post $post post being edited.
 * @param array   $data custom fields posted data(form submitted).
 */
function bblpro_form_render_post_thumbnails( $form_id, $post = null, $data = array() ) {

	if ( ! bblpro_form_supports_post_thumbnail( $form_id ) || ! $post ) {
		return;
	}

	$core_fields = bblpro_form_get_core_fields( $form_id );
	$field       = isset( $core_fields['thumbnail'] ) ? $core_fields['thumbnail'] : array();

	if ( ! function_exists( '_wp_post_thumbnail_html' ) ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';
	}
	if ( ! function_exists( 'get_upload_iframe_src' ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
	}

	remove_all_filters( 'admin_post_thumbnail_html' );

	?>

	<div id="postimagediv">
		<div class="inside">
			<?php
			$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			echo _wp_post_thumbnail_html( $thumbnail_id, $post->ID );
			?>
		</div>
		<?php if ( ! empty( $field['placeholder'] ) ) : ?>
			<div class="bbl-field-description bbl-field-post-thumbnail-description">
				<?php echo wpautop( wp_kses_data( $field['placeholder'] ) ); ?>
			</div>
		<?php endif; ?>
	</div>

	<?php
	// always force to load media script if thumbnail is enabled.
	wp_enqueue_media( array( 'post' => $post ) );
}

/**
 * Render custom fields.
 *
 * @param int     $form_id form id.
 * @param WP_Post $post post being edited.
 * @param array   $data custom fields posted data(form submitted).
 */
function bblpro_form_render_custom_fields( $form_id, $post = null, $data = array() ) {

	$custom_fields = bblpro_form_get_custom_fields( $form_id );

	if ( empty( $custom_fields ) ) {
		return;
	}

	$post_id = $post && $post->ID ? $post->ID : 0;

	echo "<div class='bbl-edit-section bbl-edit-section-custom-fields'>";

	// custom fields.
	$editor_fields = array();

	foreach ( $custom_fields as $custom_field ) {
		if ( empty( $custom_field ) ) {
			continue;
		}

		$custom_field_type_object = bblpro_get_field_type_object( $custom_field['type'] );

		if ( ! $custom_field_type_object ) {
			continue;
		}

		$key = $custom_field['key'];

		if ( 'editor' === $custom_field['type'] || 'tinymce' == $custom_field['type'] ) {
			$editor_fields[] = $key;
		}


		$val = isset( $data[ $key ] ) ? $data[ $key ] : null;

		if ( is_null( $val ) && $post_id ) {
			$val = $custom_field_type_object->get_field_data_raw( $post_id, $key );
		}
		echo bblpro_form_get_custom_field_markup( $form_id, $custom_field, $val );
	}

	$edit_field_keys = esc_attr( join( ',', $editor_fields ) );
	echo "<input type='hidden' value='{$edit_field_keys}' id='bbl-custom-field-type-editor-ids' name='bbl-custom-field-type-editor-ids'/>";
	do_action( 'bblpro_form_custom_fields' );

	echo '</div>';
}

/**
 * Render taxonomies.
 *
 * @param int     $form_id form id.
 * @param WP_Post $post post being edited.
 * @param array   $data posted form data.
 */
function bblpro_form_render_taxonomies( $form_id, $post = null, $data = array() ) {

    $enable_taxonomy    = bblpro_form_has_taxonomy_enabled( $form_id );
	$enabled_taxonomies = bblpro_form_get_taxonomies( $form_id );

	// taxonomy.
	if ( $enable_taxonomy && $enabled_taxonomies ) {
		echo "<div class='bbl-edit-section bbl-edit-section-taxonomy-fields'>";

		foreach ( $enabled_taxonomies as $tax => $tax_settings ) {

		    if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}

			bblpro_render_taxonomy_field( $form_id, $tax_settings, $post, $data );
		}

		do_action( 'bblpro_form_taxonomy_fields' );
		echo '</div>';
	}
}

/**
 * Render core field.
 *
 * @param int   $form_id form id.
 * @param array $field field details.
 * @param mixed $val fiel value.
 *
 * @return string
 */
function bblpro_form_get_core_field_markup( $form_id, $field, $val = null ) {

	if ( ! is_array( $field ) || empty( $field['key'] ) ) {
		return '';
	}

	$error = buddyblog_pro()->errors->get_error_message( $field['key'] );

	switch ( $field['key'] ) {

		case 'post_title':
			$content = bblpro_form_get_field_markup(
				array(
					'name'        => 'bbl_post_title',
					'label'       => _x( 'Title', 'Post title form field label', 'buddyblog-pro' ),
					'class'       => 'bbl-post-title',
					'key'         => 'post_title',
					'type'        => 'text',
					'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : _x( 'Post title', 'Post title form field placeholder', 'buddyblog-pro' ),
					'value'       => $val,
					'is_required' => isset( $field['is_required'] ) ? $field['is_required'] : false,
					'error'       => $error,
				)
			);
			break;

		case 'post_content':
			$allow_upload = bblpro_form_supports_rich_text_editor( $form_id ) && bblpro_user_can_upload( get_current_user_id(), bblpro_form_get_post_type( $form_id ) );

			$supports_editor = bblpro_form_supports_rich_text_editor( $form_id );
			$editor          = $supports_editor ? 'editor' : 'textarea';

			if ( $supports_editor ) {
				$editor = blpro_get_supported_editor_type( $field['use_editor'] );
			}

			$content = bblpro_form_get_field_markup(
				array(
					'name'         => 'bbl_post_content',
					'label'        => _x( 'Content', 'Post content form field label', 'buddyblog-pro' ),
					'class'        => 'bbl-post-content',
					'key'          => 'post_content',
					'type'         => $editor,
					'allow_upload' => $allow_upload,
					'quicktags'    => true,
					'placeholder'  => isset( $field['placeholder'] ) ? $field['placeholder'] : _x( 'Post content', 'Post content form field placeholder', 'buddyblog-pro' ),
					'value'        => $val,
					'is_required'  => isset( $field['is_required'] ) ? $field['is_required'] : false,
					'error'        => $error,
				)
			);
			break;

		case 'post_excerpt':
			$allow_upload = $field['use_editor'] && bblpro_user_can_upload( get_current_user_id(), bblpro_form_get_post_type( $form_id ) );

			$supports_editor = ! empty( $field['use_editor'] );
			$editor          = $supports_editor ? 'editor' : 'textarea';

			if ( $supports_editor ) {
				$editor = blpro_get_supported_editor_type( $field['use_editor'] );
			}

			$content = bblpro_form_get_field_markup(
				array(
					'name'         => 'bbl_post_excerpt',
					'label'        => _x( 'Excerpt', 'Post content form field label', 'buddyblog-pro' ),
					'class'        => 'bbl-post-excerpt',
					'key'          => 'post_excerpt',
					'type'         => $editor,
					'allow_upload' => $allow_upload,
					'quicktags'    => true,
					'placeholder'  => isset( $field['placeholder'] ) ? $field['placeholder'] : _x( 'Post excerpt', 'Post excerpt form field placeholder', 'buddyblog-pro' ),
					'value'        => $val,
					'is_required'  => isset( $field['is_required'] ) ? $field['is_required'] : false,
					'error'        => $error,
				)
			);
			break;

		case 'thumbnail':
			$content = '';
			break;

		default:
			$content = '';
			break;
	}

	return $content;
}

/**
 * Render taxonomy field.
 *
 * @param int     $form_id form id.
 * @param array   $tax_settings tax settings.
 * @param WP_Post $post Post object.
 * @param array   $data data array.
 */
function bblpro_render_taxonomy_field( $form_id, $tax_settings, $post = null, $data = array() ) {

	$view = isset( $tax_settings['view'] )
			&& in_array( $tax_settings['view'], array( 'checkbox', 'radio' ), true )
		? $tax_settings['view']
		: 'checkbox';

	$taxonomy = $tax_settings['taxonomy'];
	$post_id  = $post && $post->ID ? $post->ID : 0;

	if ( isset( $data[ $taxonomy ] ) ) {
		$tax_settings['selected'] = $data[ $taxonomy ];
	} elseif ( $post_id ) {
		$tax_settings['selected'] = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
	} else {
		$tax_settings['selected'] = array();
	}

	echo "<div class='bbl-edit-section-terms bbl-edit-section-terms-{$taxonomy}'>";

	if ( 'checkbox' === $view ) {
		bblpro_form_terms_checklist( $tax_settings );
	} else {
		bblpro_form_terms_dropdown( $tax_settings );
	}

	echo '</div>';
}

/**
 * Render a custom field.
 *
 * @param int   $form_id form id.
 * @param array $field field details.
 * @param mixed $val Value.
 *
 * @return string
 */
function bblpro_form_get_custom_field_markup( $form_id, $field, $val = null ) {

	$field_types = bblpro_get_custom_field_types();

	$type = isset( $field['type'] ) ? $field['type'] : '';
	// Not a valid type?
	if ( ! $type || empty( $field_types[ $type ] ) ) {
		return '';
	}

	$key   = esc_attr( $field['key'] );
	$error = buddyblog_pro()->errors->get_error_message( 'bbl_cf_' . $key );

	// ensure core settings.
	$field['label']       = isset( $field['label'] ) ? $field['label'] : '';
	$field['name']        = "bbl_custom_field[$key]";
	$field['key']         = $key;
	$field['placeholder'] = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['is_required'] = isset( $field['is_required'] ) ? $field['is_required'] : false;
	$field['default']     = isset( $field['default'] ) ? $field['default'] : null;
	$field['error']       = $error;
	$field['value']       = $val;
	$field['options']     = bblpro_prepare_meta_options( $field );

	return bblpro_form_get_field_markup( $field );
}

/**
 * Get a form field markup for front end rendering.
 *
 * @param array $args args.
 *
 * @return string|null
 */
function bblpro_form_get_field_markup( $args = array() ) {

	if ( empty( $args['name'] ) ) {
		return null;
	}

	$args['name']     = trim( $args['name'] );// input name.
	$args['id']       = isset( $args['id'] ) ? $args['id'] : sanitize_title( $args['name'] );
	$type             = isset( $args['type'] ) ? $args['type'] : '';

	$args['required'] = isset( $args['is_required'] ) && $args['is_required'] ? '<span class="bbl-required">*</span>' : '';
	$current_value    = isset( $args['value'] ) ? $args['value'] : null;
	$key = isset( $args['key'] ) ? esc_attr( $args['key'] ) : $args['name'];

	//if ( $current_value && ! is_array( $current_value ) ) {
	//	$current_value = esc_attr( $current_value );
	//}

	$default = isset( $args['default'] ) ? $args['default'] : null;

	if ( is_null( $current_value ) ) {
		$current_value = $default;
	}

	$args['value'] = $current_value;

	$field_type_object = bblpro_get_field_type_object( $type );

	if ( ! $field_type_object ) {
		return '';
	}

	ob_start();
	$field_type_object->edit_field_markup( $args );

	$input = ob_get_clean();
	/*
	// @todo let us add attributes filters in future.
	switch ( $type ) {

		case 'image':
		case 'file':
			$input = "<label class='bbl-field-label bbl-field-label-type-{$type} bbl-field-label-field-{$id}'>{$label} {$required} <input type='file' class='bbl-file-input'  id='{$id}' name='{$name}'/></label>";
			if ( $current_value ) {
				$input .= "<div class='bblpro-file-attachments'>";
				$input .= "<a href='" . esc_url( $current_value ) . "'>" . wp_basename( $current_value ) . '</a>';
				$input .= "<label><input type='checkbox' value='1' name='{$name}_file_delete' >" . _x( 'Delete', 'File delete checkbox label', 'buddyblog-pro' ) . '</label>';
				$input .= '</div>';
			}

			break;
		default:
			$input = '';
	}
	*/

	$markup = '';

	$id = $args['id'];

	if ( ! empty( $input ) ) {
		$type   = esc_attr( $type );
		$class  = isset( $args['class'] ) ? esc_attr( $args['class'] ) : '';
		$markup = "<div class='bbl-form-field-container bbl-form-field-type-{$type}-container bbl-form-field-{$id}-container $class' data-field-key='{$key}'>";
		if ( ! empty( $args['error'] ) ) {
			$markup .= "<div class='bbl-form-field-error bbl-form-field-type-{$type}-error bbl-form-field-{$id}'>" . esc_html( $args['error'] ) . '</div>';
		}
		$markup .= "<div class='bbl-form-field bbl-form-field-{$type} '>" . $input . '</div>';
		$markup .= '</div>';
	}

	return $markup; // return html.
}

/**
 * Render taxonomy terms as checklist.
 *
 * @param array $list_args list args.
 */
function bblpro_form_terms_checklist( $list_args = array() ) {

	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats'        => false,
		'popular_cats'         => false,
		'walker'               => null,
		'include'              => array(),
		'taxonomy'             => 'category',
		'checked_ontop'        => true,
		'name'                 => '',
	);

	$list_args = wp_parse_args( $list_args, $defaults );

	if ( empty( $walker ) || ! is_a( $walker, 'Walker' ) ) {
		$walker = new Terms_Checklist_Walker(); // custom walker.
	}

	$descendants_and_self = (int) $list_args['descendants_and_self'];

	$taxonomy      = $list_args['taxonomy'];
	$selected_cats = $list_args['selected'];
	$popular_cats  = $list_args['popular_cats'];
	$include       = $list_args['include'];
	$checked_ontop = $list_args['checked_ontop'];


	$args = array( 'taxonomy' => $taxonomy );

	$tax = get_taxonomy( $taxonomy );

	$args['disabled'] = false;// allow everyone to assign the tax !current_user_can($tax->cap->assign_terms);.

	if ( is_array( $selected_cats ) ) {
		$args['selected_cats'] = $selected_cats;
	} else {
		$args['selected_cats'] = array();
	}

	if ( is_array( $popular_cats ) ) {
		$args['popular_cats'] = $popular_cats;
	} else {
		$args['popular_cats'] = get_terms(
			$taxonomy,
			array(
				'fields'       => 'ids',
				'orderby'      => 'count',
				'order'        => 'DESC',
				'number'       => 10,
				'hierarchical' => false,
			)
		);
	}

	if ( ! empty( $list_args['name'] ) ) {
		$args['name'] = $list_args['name'];
	}

	if ( $descendants_and_self ) {
		$categories = (array) get_terms(
			$taxonomy,
			array(
				'child_of'     => $descendants_and_self,
				'hierarchical' => 0,
				'hide_empty'   => false,
			)
		);
		$self       = get_term( $descendants_and_self, $taxonomy );

		array_unshift( $categories, $self );

	} else {
		$categories = (array) get_terms(
			$taxonomy,
			array(
				'get'        => 'all',
				'include'    => $include,
				'hide_empty' => false,
			)
		);
	}
	echo "<div class='bblpro-tax-checkbox bblpro-tax-{$taxonomy}-checkbox'><h5>{$tax->labels->singular_name}</h5>" ;

	echo "<ul class='bblpro-tax-check-list'>";

	if ( $checked_ontop ) {
		// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache).
		$checked_categories = array();
		$keys               = array_keys( $categories );

		foreach ( $keys as $k ) {

			if ( in_array( $categories[ $k ]->term_id, $args['selected_cats'] ) ) {
				$checked_categories[] = $categories[ $k ];
				unset( $categories[ $k ] );
			}
		}

		// Put checked cats on top.
		echo call_user_func_array( array( $walker, 'walk' ), array( $checked_categories, 0, $args ) );
	}

	// Then the rest of them.
	echo call_user_func_array( array( $walker, 'walk' ), array( $categories, 0, $args ) );

	echo '</ul>';
	echo '</div>';
}

/**
 * Render taxonomy terms as dropdown.
 *
 * @param array $args args.
 *
 * @return string
 */
function bblpro_form_terms_dropdown( $args ) {

	$defaults = array(
		'show_option_all' => 1,
		'selected'        => 0,
		'hide_empty'      => false,
		'echo'            => false,
		'include'         => false,
		'hierarchical'    => true,
		'select_label'    => false,
		'show_label'      => true,
		'child_of'        => false,
		'orderby'         => 'ID',
		'order'           => 'ASC',
		'taxonomy'        => '',
		'name'            => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$show_option_all = $args['show_option_all'];
	$selected        = $args['selected'];
	$hide_empty      = $args['hide_empty'];
	$echo            = $args['echo'];
	$include         = $args['include'];
	$hierarchical    = $args['hierarchical'];
	$select_label    = $args['select_label'];

	$show_label = $args['show_label'];
	$child_of   = $args['child_of'];
	$orderby    = $args['orderby'];
	$order      = $args['order'];

	$name = $args['name'];

	$taxonomy = $args['taxonomy'];

	$excluded = false;

	if ( is_array( $selected ) ) {
		$selected = array_pop( $selected ); // in dd, we don't allow multiple values at the moment.
	}

	if ( ! empty( $include ) ) {
		$excluded = array_diff(
			(array) get_terms(
				$taxonomy,
				array(
					'fields'     => 'ids',
					'get'        => 'all',
					'hide_empty' => false,
				)
			),
			$include
		);
	}

	$tax = get_taxonomy( $taxonomy );

	if ( $show_option_all ) {

		if ( ! $select_label ) {
			/* translators: %s : taxonomy label */
			$show_option_all = sprintf( _x( 'Select %s', 'Terms dropdown label', 'buddyblog-pro' ), $tax->labels->singular_name );
		} else {
			$show_option_all = $select_label;
		}
	}

	if ( empty( $name ) ) {
		$name = 'tax_input[' . $taxonomy . ']';
	}

	$info = wp_dropdown_categories(
		array(
			'taxonomy'        => $taxonomy,
			'hide_empty'      => $hide_empty,
			'name'            => $name,
			'id'              => 'bp-simple-post-' . $taxonomy,
			'selected'        => $selected,
			'show_option_all' => $show_option_all,
			'echo'            => false,
			'exclude'         => $excluded,
			'hierarchical'    => $hierarchical,
			'child_of'        => $child_of,
			'orderby'         => $orderby,
			'order'           => $order,
		)
	);

	$html = '';
	if ( $show_label ) {
		$html .= "<div class='bblpro-tax-dd bblpro-tax-{$taxonomy}-dd'><h3>{$tax->labels->singular_name}</h3>" . $info . "</div>";
	}

	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}

	return '';
}
