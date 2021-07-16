<?php
/**
 * BuddyBlog Pro Form edit page helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin;

use BuddyBlog_Pro\Admin\Metaboxes\BBL_Meta_Box;
use BuddyBlog_Pro\Admin\Metaboxes\Custom_Fields_Meta_Box;
use BuddyBlog_Pro\Admin\Metaboxes\Form_Settings_Meta_Box;
use BuddyBlog_Pro\Admin\Metaboxes\Meta_Display_Meta_Box;
use BuddyBlog_Pro\Admin\Metaboxes\Post_Fields_Meta_Box;
use BuddyBlog_Pro\Admin\Metaboxes\Taxonomy_Meta_Box;
use BuddyBlog_Pro\Admin\Metaboxes\Workflow_Meta_Box;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Class Form_Admin_Helper
 */
class Form_Admin_Helper {

	/**
	 * Metaboxes
	 *
	 * @var BBL_Meta_Box[]
	 */
	private $metaboxes = array();

	/**
	 * Constructor
	 */
	private function __construct() {
		Form_Admin_Clone_Helper::boot();
		Form_Admin_Import_Export_Helper::boot();

		$this->metaboxes['settings']      = new Form_Settings_Meta_Box();
		$this->metaboxes['taxonomy']      = new Taxonomy_Meta_Box();
		$this->metaboxes['post_fields']   = new Post_Fields_Meta_Box();
		$this->metaboxes['custom_fields'] = new Custom_Fields_Meta_Box();
		$this->metaboxes['meta_display']  = new Meta_Display_Meta_Box();
		$this->metaboxes['workflow']      = new Workflow_Meta_Box();
	}

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
		// save post.
		add_action( 'save_post', array( $this, 'save_settings' ) );

		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'bblpro_custom_fields_admin_options', array( $this, 'custom_field_options' ) );
	}

	/**
	 * Register metaboxes.
	 *
	 * @param string $post_type post type.
	 */
	public function register_meta_boxes( $post_type ) {

		$form_post_type = bblpro_get_form_post_type();

		if ( $form_post_type !== $post_type ) {
			return;
		}

		// Register settings meta box.
		if ( ! empty( $this->metaboxes['settings'] ) ) {
			add_meta_box(
				'bbl-core-options-meta-box',
				__( 'Settings', 'buddyblog-pro' ),
				array(
					$this->metaboxes['settings'],
					'render',
				),
				$form_post_type
			);
		}

		if ( ! empty( $this->metaboxes['taxonomy'] ) ) {
			add_meta_box(
				'bbl-post-taxonomy-meta-box',
				__( 'Taxonomies', 'buddyblog-pro' ),
				array(
					$this->metaboxes['taxonomy'],
					'render',
				),
				$form_post_type
			);
		}


		if ( ! empty( $this->metaboxes['post_fields'] ) ) {
			add_meta_box(
				'bbl-post-fields-meta-box',
				__( 'Post Fields', 'buddyblog-pro' ),
				array(
					$this->metaboxes['post_fields'],
					'render',
				),
				$form_post_type
			);
		}

		if ( ! empty( $this->metaboxes['custom_fields'] ) ) {

			add_meta_box(
				'bbl-cf-fields-meta-box',
				__( 'Custom Fields', 'buddyblog-pro' ),
				array(
					$this->metaboxes['custom_fields'],
					'render',
				),
				$form_post_type
			);
		}
		if ( ! empty( $this->metaboxes['meta_display'] ) ) {
			add_meta_box(
				'bbl-core-meta-display-meta-box',
				__( 'Post Meta Display Control', 'buddyblog-pro' ),
				array(
					$this->metaboxes['meta_display'],
					'render',
				),
				$form_post_type
			);
		}

		if ( ! empty( $this->metaboxes['workflow'] ) ) {
			add_meta_box(
				'bbl-core-workflow-meta-box',
				__( 'Workflow', 'buddyblog-pro' ),
				array(
					$this->metaboxes['workflow'],
					'render',
				),
				$form_post_type
			);
		}
	}

	/**
	 * Save form configurations.
	 *
	 * @param int $post_id post id.
	 */
	public function save_settings( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$post = get_post( $post_id );

		if ( bblpro_get_form_post_type() !== $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['_buddyblog-pro-form-admin-nonce'] ) ) {
			return;
		}

		// verify nonce.
		if ( ! wp_verify_nonce( wp_unslash( $_POST['_buddyblog-pro-form-admin-nonce'] ), 'buddyblog-pro-form-admin-nonce' ) ) {
			return;
		}

		// check if the user can update?
		if ( ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			return;
		}

		// Delegate saving.
		foreach ( $this->metaboxes as $metabox ) {
			$metabox->save( $post );
		}
	}

	/**
	 * Render custom fields options.
	 *
	 * @param int $form_id form id.
	 */
	public function custom_field_options( $form_id ) {
		$registered_field_types = bblpro_get_custom_field_types();

		foreach ( $registered_field_types as $field_type => $label ) {
			$field_type_object = bblpro_get_field_type_object( $field_type );

			if ( ! $field_type_object || ! $field_type_object->supports( 'settings' ) ) {
				continue;
			}

			echo "<div class='bbl-cf-field-options-extra bbl-cf-field-options-{$field_type}'>";
			do_action( 'bblpro_before_admin_field_settings', $field_type_object, $form_id );
			$field_type_object->admin_field_settings_markup();
			do_action( 'bblpro_after_admin_field_settings', $field_type_object, $form_id );
			echo '</div>';
		}
	}
}
