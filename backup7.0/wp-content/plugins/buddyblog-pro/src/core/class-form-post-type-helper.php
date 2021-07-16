<?php
/**
 * Form Post Type Helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Core;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Helper class to register the internal form post type
 */
class Form_Post_Type_Helper {

	/**
	 * Singleton instance
	 *
	 * @var Form_Post_Type_Helper
	 */
	private static $instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {
		// Register internal post type.
		add_action( 'bp_init', array( $this, 'register_post_type' ) );
	}

	/**
	 * Get singleton instance
	 *
	 * @return Form_Post_Type_Helper
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		return self::get_instance();
	}

	/**
	 * Register internal post type
	 */
	public function register_post_type() {

		$is_admin = is_super_admin();

		register_post_type(
			bblpro_get_form_post_type(),
			array(
				'label'     => __( 'BuddyBlog Form', 'buddyblog-pro' ),
				'labels'    => array(
					'name'               => __( 'Forms', 'buddyblog-pro' ),
					'singular_name'      => __( 'Form', 'buddyblog-pro' ),
					'menu_name'          => __( 'BuddyBlog', 'buddyblog-pro' ),
					'all_items'          => __( 'All Forms', 'buddyblog-pro' ),
					'add_new_item'       => __( 'New Post Form', 'buddyblog-pro' ),
					'new_item'           => __( 'New Post Form', 'buddyblog-pro' ),
					'edit_item'          => __( 'Edit Post Form', 'buddyblog-pro' ),
					'search_items'       => __( 'Search Post Forms', 'buddyblog-pro' ),
					'not_found_in_trash' => __( 'No post forms found in trash', 'buddyblog-pro' ),
					'not_found'          => __( 'No post form found', 'buddyblog-pro' ),
				),
				'public'    => false,
				'show_ui'   => $is_admin,
				'menu_icon' => 'dashicons-groups',
				'supports'  => array( 'title' ),
			)
		);
	}
}
