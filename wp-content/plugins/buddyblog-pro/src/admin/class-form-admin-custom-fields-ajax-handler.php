<?php
/**
 * BuddyBlog Pro Form Custom fields ajax handler.
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Handle form actions via ajax for managing custom fields.
 */
class Form_Admin_Custom_Fields_Ajax_Handler {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Register actions.
	 */
	private function setup() {
		add_action( 'wp_ajax_bblogpro_form_add_custom_field', array( $this, 'add_field' ) );
		add_action( 'wp_ajax_bblogpro_form_sort_custom_fields', array( $this, 'sort_fields' ) );
		add_action( 'wp_ajax_bblogpro_form_delete_custom_field', array( $this, 'delete_field' ) );
	}

	/**
	 * Add core field.
	 */
	public function add_field() {

		if ( ! wp_verify_nonce( wp_unslash( $_POST['_wpnonce'] ), 'bblogpro_add_form' ) || ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		// validate fields.
		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$form    = get_post( $form_id );

		if ( empty( $form ) || bblpro_get_form_post_type() !== $form->post_type ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}
		// Let us check if the key is valid new key?.
		$settings = get_post_meta( $form_id, '_buddyblog_custom_fields', true );

		if ( empty( $settings ) ) {
			$settings = array();
		}

		$key = ! empty( $_POST['key'] ) ? sanitize_key( wp_unslash( $_POST['key'] ) ) : sanitize_key( wp_unslash( $_POST['label'] ) );

		if ( isset( $settings[ $key ] ) ) {
			// Can not add the field twice.
			wp_send_json_error(
				array(
					'message' => __( 'Can not add the field with same key twice.', 'buddyblog-pro' ),
					'fields'  => array( $key ),
				)
			);
		}


		$errors = array();

		$type              = isset( $_POST['type'] ) ? wp_unslash( $_POST['type'] ) : '';
		$field_type_object = bblpro_get_field_type_object( $type );

		// make sure that type is given.
		if ( empty( $_POST['type'] ) ) {
			$errors['type'] = __( 'type is required.', 'buddyblog-pro' );
		}

		if ( $type && ! $field_type_object ) {
			// Invalid type error.
			/* translators: %s field type name */
			$errors['type'] = sprintf( __( '%s is not a valid/registered field type.', 'buddyblog-pro' ), $type );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Required field missing.', 'buddyblog-pro' ),
					'fields'  => $errors,
				)
			);
		}

		$sanitized = $field_type_object->sanitize_settings( $form_id, $_POST );
		$validated = $field_type_object->validate_settings( $form_id, $sanitized );

		if ( is_wp_error( $validated ) ) {
			$errors = $validated->errors;
		} else {
			$errors = array();
		}

		$errors = apply_filters( 'bblpro_custom_field_settings_validate', $errors, $sanitized, $form );

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Settings validation failed.', 'buddyblog-pro' ),
					'fields'  => $errors,
				)
			);
		}

		$field_settings = $field_type_object->prepare_settings( $form_id, $key, $sanitized );

		if ( is_wp_error( $field_settings ) ) {
			wp_send_json_error(
				array(
					'message' => $field_settings->get_error_message(),
					'fields'  => $field_settings->errors,
				)
			);
		}

		// add field settings to the array.
		$settings[ $key ] = apply_filters( 'bblpro_custom_field_settings', $field_settings, $type, $form );

		$settings = array_filter( $settings );

		bblpro_form_set_custom_fields( $form_id, $settings );

		$this->send_table( $form_id, __( 'Saved.', 'buddyblog-pro' ) );
	}

	/**
	 * Sends core fields list.
	 *
	 * @param int    $form_id form id.
	 * @param string $message message.
	 */
	private function send_table( $form_id, $message ) {

		ob_start();

		bblpro_print_custom_fields_table( $form_id );
		$content = ob_get_clean();

		wp_send_json_success(
			array(
				'content' => $content,
				'message' => $message,
			)
		);
	}

	/**
	 * Sort fields.
	 */
	public function sort_fields() {

		if ( ! wp_verify_nonce( wp_unslash( $_POST['_wpnonce'] ), 'bblogpro_add_form' ) || ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		$fields = isset( $_POST['fields'] ) ? array_map( 'wp_unslash', $_POST['fields'] ) : array();
		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;

		$form = get_post( $form_id );

		if ( empty( $fields ) || empty( $form ) || bblpro_get_form_post_type() !== $form->post_type ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		// if we are here, let us update the settings.
		$settings = get_post_meta( $form_id, '_buddyblog_custom_fields', true );

		if ( empty( $settings ) ) {
			$settings = array();
		}

		$sorted_fields = array();

		$field_types = bblpro_get_custom_field_types();

		foreach ( $fields as $field ) {

			if ( ! isset( $settings[ $field ] ) ) {
				continue;
			}

			$type = isset( $settings[ $field ]['type'] ) ? $settings[ $field ]['type'] : '';

			if ( ! $type || ! isset( $field_types[ $type ] ) ) {
				continue;
			}
			$sorted_fields[ $field ] = $settings[ $field ];
		}

		if ( empty( $sorted_fields ) ) {
			// Can not add the field twice.
			wp_send_json_error(
				array(
					'message' => __( 'Unable to update.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		bblpro_form_set_custom_fields( $form_id, $sorted_fields );

		$this->send_table( $form_id, __( 'Updated.', 'buddyblog-pro' ) );
	}

	/**
	 * Add core field.
	 */
	public function delete_field() {

		if ( ! wp_verify_nonce( wp_unslash( $_POST['_wpnonce'] ), 'bblogpro_add_form' ) || ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		$field_key = isset( $_POST['field'] ) ? trim( wp_unslash( $_POST['field'] ) ) : '';
		$form_id   = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;

		$form = get_post( $form_id );

		if ( empty( $field_key ) || empty( $form ) || bblpro_get_form_post_type() !== $form->post_type ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		// if we are here, let us update the settings.
		$settings = get_post_meta( $form_id, '_buddyblog_custom_fields', true );

		if ( empty( $settings ) ) {
			$settings = array();
		}

		if ( ! isset( $settings[ $field_key ] ) ) {
			// Can not add the field twice.
			wp_send_json_error(
				array(
					'message' => __( 'Can not remove the field.', 'buddyblog-pro' ),
					'fields'  => array( $field_key ),
				)
			);
		}

		// add field to the array.
		unset( $settings[ $field_key ] );
		$settings = array_filter( $settings );
		bblpro_form_set_custom_fields( $form_id, $settings );

		$this->send_table( $form_id, __( 'Deleted.', 'buddyblog-pro' ) );
	}
}
