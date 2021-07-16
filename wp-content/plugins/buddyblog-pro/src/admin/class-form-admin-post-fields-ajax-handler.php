<?php
/**
 * BuddyBlog From Post Fields(core fields) ajax handler for Create/Edit form page
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
 * Handle form actions via ajax
 */
class Form_Admin_Post_Fields_Ajax_Handler {

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
		add_action( 'wp_ajax_bblogpro_form_add_post_field', array( $this, 'add_field' ) );
		add_action( 'wp_ajax_bblogpro_form_sort_post_fields', array( $this, 'sort_fields' ) );
		add_action( 'wp_ajax_bblogpro_form_delete_post_field', array( $this, 'delete_field' ) );
		add_filter( 'bblpro_core_field_settings', array( $this, 'extra_settings' ), 10, 3 );
	}

	/**
	 * Add core field.
	 */
	public function add_field() {

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'bblogpro_add_form' ) || ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		$field_key = isset( $_POST['type'] ) ? trim( wp_unslash( $_POST['type'] ) ) : '';
		$form_id   = isset( $_POST['form_id'] ) ? absint( wp_unslash( $_POST['form_id'] ) ) : 0;
		$form      = get_post( $form_id );

		if ( empty( $field_key ) || empty( $form ) || bblpro_get_form_post_type() !== $form->post_type ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		// if we are here, let us update the settings.
		$settings = bblpro_form_get_core_fields( $form_id );

		if ( empty( $settings ) ) {
			$settings = array();
		}

		if ( isset( $settings[ $field_key ] ) ) {
			// Can not add the field twice.
			wp_send_json_error(
				array(
					'message' => __( 'Can not add the field twice.', 'buddyblog-pro' ),
					'fields'  => array( $field_key ),
				)
			);
		}

		$is_required = empty( $_POST['is_required'] ) ? 0 : 1;
		$placeholder = empty( $_POST['placeholder'] ) ? '' : strip_tags( wp_unslash( $_POST['placeholder'] ) );

		$field_setting = apply_filters(
			'bblpro_core_field_settings',
			array(
				'key'         => $field_key,
				'type'        => $field_key, // should we?
				'label'       => '',
				'default'     => '',
				'placeholder' => $placeholder,
				'is_required' => $is_required,
			),
			$field_key,
			$form
		);
		// add field to the array.
		$settings[ $field_key ] = $field_setting;

		$settings = array_filter( $settings );

		bblpro_form_set_core_fields( $form_id, $settings );

		$this->send_table( $form_id, __( 'Added.', 'buddyblog-pro' ) );
	}

	/**
	 * Sort core fields.
	 */
	public function sort_fields() {

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'bblogpro_add_form' ) || ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		$fields  = isset( $_POST['fields'] ) ? $_POST['fields'] : array();
		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$form    = get_post( $form_id );

		if ( empty( $fields ) || empty( $form ) || bblpro_get_form_post_type() !== $form->post_type ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		// if we are here, let us update the settings.
		$settings = bblpro_form_get_core_fields( $form_id );

		if ( empty( $settings ) ) {
			$settings = array();
		}

		$sorted_fields = array();

		$field_types = bblpro_get_core_field_types();

		foreach ( $fields as $field ) {
			if ( ! isset( $settings[ $field ] ) || ! isset( $field_types[ $field ] ) ) {
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

		bblpro_form_set_core_fields( $form_id, $sorted_fields );

		$this->send_table( $form_id, __( 'Updated.', 'buddyblog-pro' ) );
	}

	/**
	 * Add core field.
	 */
	public function delete_field() {

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'bblogpro_add_form' ) || ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		$field_key = isset( $_POST['field'] ) ? trim( $_POST['field'] ) : '';
		$form_id   = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$form      = get_post( $form_id );

		if ( empty( $field_key ) || empty( $form ) || bblpro_get_form_post_type() !== $form->post_type ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid Request.', 'buddyblog-pro' ),
					'fields'  => array(),
				)
			);
		}

		// if we are here, let us update the settings.
		$settings = bblpro_form_get_core_fields( $form_id );

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

		bblpro_form_set_core_fields( $form_id, $settings );

		$this->send_table( $form_id, __( 'Deleted.', 'buddyblog-pro' ) );
	}

	/**
	 * Save extra settings for the "Content" field.
	 *
	 * @param array    $settings settings.
	 * @param string   $key field name/type(are same).
	 * @param \WP_Post $form form.
	 *
	 * @return mixed
	 */
	public function extra_settings( $settings, $key, $form ) {

		if ( 'post_content' === $key || 'post_excerpt' == $key ) {
			$settings['use_editor'] = empty( $_POST['bbl-content-use-editor'] ) ? '' : esc_html( $_POST['bbl-content-use-editor'] );
		}

		return $settings;
	}

	/**
	 * Sends core fields list.
	 *
	 * @param int    $form_id form id.
	 * @param string $message message.
	 */
	private function send_table( $form_id, $message ) {

		ob_start();

		bblpro_print_core_fields_table( $form_id );
		$content = ob_get_clean();

		wp_send_json_success(
			array(
				'content' => $content,
				'message' => $message,
			)
		);
	}

}
