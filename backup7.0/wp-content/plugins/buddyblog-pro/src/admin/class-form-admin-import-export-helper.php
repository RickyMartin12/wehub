<?php
/**
 * BuddyBlog Pro Form import export module helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh, Ravi Sharma
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Class Form_Admin_Import_Export_Helper
 */
class Form_Admin_Import_Export_Helper {

	/**
	 * Singleton.
	 *
	 * @var Form_Admin_Import_Export_Helper
	 */
	private static $instance = null;

	/**
	 * Flag set for form import status.
	 *
	 * @var bool
	 */
	private $form_imported = false;

	/**
	 * Setup the bootstrapper
	 *
	 * @return Form_Admin_Import_Export_Helper
	 */
	public static function boot() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Setup callbacks
	 */
	private function setup() {

		add_filter( 'post_row_actions', array( $this, 'add_export_link' ), 10, 2 );
		add_action( 'post_action_bbl_export_form', array( $this, 'export_form' ) );

		//add_action( 'manage_posts_extra_tablenav', array( $this, 'add_import_interface' ) );
		add_action( 'admin_notices', array( $this, 'add_import_interface' ), -1 );
		add_action( 'admin_init', array( $this, 'import_form' ) );

		add_action( 'admin_notices', array( $this, 'render_notice' ) );
	}

	/**
	 * Add export link as row action in forms list
	 *
	 * @param string[] $actions Actions.
	 * @param \WP_Post $post    Post object.
	 *
	 * @return array
	 */
	public function add_export_link( $actions, $post ) {

		if ( ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			return $actions;
		}

		if ( bblpro_get_form_post_type() !== $post->post_type ) {
			return $actions;
		}

		$post_type_object = get_post_type_object( $post->post_type );

		if ( ! $post_type_object ) {
			return $actions;
		}

		$action = 'bbl_export_form';

		// Not sure.
		$export_link = add_query_arg( 'action', $action, admin_url( sprintf( $post_type_object->_edit_link, $post->ID ) ) );

		$actions['bbl_export_form'] = sprintf(
			'<a href="%s" rel="bookmark">%s</a>',
			wp_nonce_url( $export_link, "$action-post_{$post->ID}" ),
			__( 'Export', 'buddyblog-pro' )
		);

		return $actions;
	}

	/**
	 * Export form
	 *
	 * @param int $post_id Post id.
	 */
	public function export_form( $post_id ) {

		if ( ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			return;
		}

		if ( ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'bbl_export_form-post_' . $post_id ) ) {
			return;
		}

		$form_data = bblpro_get_exportable_form_data( $post_id );

		if ( ! $form_data ) {
			return;
		}

		header( 'Content-disposition: attachment; filename=' . sanitize_file_name( $form_data['post_title'] ) . '.json' );
		header( 'Content-type: application/json' );
		echo wp_json_encode( $form_data );

		exit;
	}

	/**
	 * Add button to import forms
	 */
	public function add_import_interface() {

		if ( ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			return;
		}

		$screen_id = 'edit-' . bblpro_get_form_post_type();
		if ( $screen_id !== get_current_screen()->id ) {
			return;
		}

		?>
		<div id="bblpro-import-wrap">
			<button class="bblpro-import-action-link" id="bblpro-import-form-button"><?php _e( 'Import', 'buddyblog-pro' ); ?></button>

			<div class="bblpro-import-form-wrap deactive">
				<form name="bblpro-import-form" method="post" id="bblpro-import-form" enctype="multipart/form-data">
					<input type="file" name="bblpro-import-form-file">
					<?php wp_nonce_field( 'bblpro-import-form' ); ?>
					<input type="submit" name="bblpro-import-form-btn" class="button" value="<?php esc_attr_e( 'Import', 'buddyblog-pro' ); ?>" />
				</form>
			</div>
		</div>

		<style>
			#bblpro-import-wrap {
				margin: 20px 0;
			}
			.bblpro-import-form-wrap {
				margin: 20px 0;
				padding: 20px;
				border: 4px dashed #b4b9be;
				width: 80%;
				text-align: center;
				height: 100px;
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.bblpro-import-form-wrap.deactive {
				display: none;
			}
            .bblpro-import-action-link{
                margin-left: 4px;
                padding: 4px 8px;
                position: relative;
                top: -3px;
                text-decoration: none;
                border: 1px solid #0071a1;
                border-radius: 2px;
                text-shadow: none;
                font-weight: 600;
                font-size: 13px;
                line-height: normal;
                color: #0071a1;
                background: #f3f5f6;
                cursor: pointer;
            }
		</style>

		<script type="application/javascript">
			jQuery(document).ready(function ($) {
				//move import button.
				$('#bblpro-import-form-button').insertBefore('.page-title-action');

				var isImporterOpen = false;
				// on click.
				$('button#bblpro-import-form-button').click(function (e) {
					e.preventDefault();
					$('div.bblpro-import-form-wrap').toggleClass('deactive');

					return false;
				});
			});
		</script>
		<?php
	}

	/**
	 * Import form
	 */
	public function import_form() {

		if ( ! bblpro_user_can_manage_forms( get_current_user_id() ) ) {
			return;
		}

		if ( ! isset( $_POST['bblpro-import-form-btn'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'bblpro-import-form' ) ) {
			return;
		}

		if ( 'application/json' !== $_FILES['bblpro-import-form-file']['type'] ) {
			return;
		}

		$post_details = json_decode( file_get_contents( $_FILES['bblpro-import-form-file']['tmp_name'] ), true );

		if ( ! $post_details ) {
			return;
		}

		$post_title  = isset( $post_details['post_title'] ) ? $post_details['post_title'] : '';
		$post_status = isset( $post_details['post_status'] ) ? $post_details['post_status'] : 'draft';
		$post_type   = isset( $post_details['post_type'] ) ? $post_details['post_type'] : bblpro_get_form_post_type();

		$post_id = wp_insert_post(
			array(
				'post_status' => $post_status,
				'post_title'  => $post_title,
				'post_type'   => $post_type,
			)
		);

		if ( ! $post_id ) {
			return;
		}

		$meta_values = array();

		if ( isset( $post_details['meta'] ) ) {
			$meta_values = $post_details['meta'];
		}

		foreach ( $meta_values as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		$this->form_imported = true;
	}

	/**
	 * Render notice
	 */
	public function render_notice() {

		if ( ! $this->form_imported ) {
			return;
		}

		?>
	   <div class="notice notice-success is-dismissible">
			<p><?php _e( 'Form Imported successfully.', 'buddyblog-pro' ); ?></p>
		</div>
		<?php

		unset( $this->form_imported );
	}
}