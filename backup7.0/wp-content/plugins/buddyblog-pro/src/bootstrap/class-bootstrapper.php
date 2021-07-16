<?php
/**
 * Bootstrapper. Initializes the plugin.
 *
 * @package    BuddyBlog_Pro
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Bootstrap;

use BuddyBlog_Pro\Admin\Form_Admin_Post_Fields_Ajax_Handler;
use BuddyBlog_Pro\Admin\Form_Admin_Custom_Fields_Ajax_Handler;
use BuddyBlog_Pro\Admin\Form_Admin_Helper;
use BuddyBlog_Pro\Admin\Form_List_Helper;
use BuddyBlog_Pro\Admin\Post_Approval_Helper;
use BuddyBlog_Pro\Admin\Settings_Admin;
use BuddyBlog_Pro\Compat\Compat_Helper;
use BuddyBlog_Pro\Core\Form_Post_Type_Helper;
use BuddyBlog_Pro\Core\Post_Meta_Shortcode_Helper;
use BuddyBlog_Pro\Core\Post_Meta_Display_Helper;
use BuddyBlog_Pro\Handlers\Ajax_Draft_Handler;
use BuddyBlog_Pro\Handlers\Ajax_Post_Submission_Handler;
use BuddyBlog_Pro\Handlers\Ajax_Post_Thumbnail_Handler;
use BuddyBlog_Pro\Handlers\Capability_Filter;
use BuddyBlog_Pro\Handlers\Default_Filters;
use BuddyBlog_Pro\Handlers\Notifications\Post_Approval_Notifier;
use BuddyBlog_Pro\Handlers\Notifications\Post_Rejection_Notifier;
use BuddyBlog_Pro\Handlers\Post_Submission_Handler;
use BuddyBlog_Pro\Handlers\Notifications\Post_Submission_Admin_Notifier;
use BuddyBlog_Pro\Handlers\Notifications\Post_Submission_Author_Notifier;
use BuddyBlog_Pro\Handlers\Post_Deletion_Handler;
use BuddyBlog_Pro\Handlers\Tabs_Helper;
use BuddyBlog_Pro\Handlers\URL_Filter;
use BuddyBlog_Pro\Shortcodes\Shortcode_Loader;


// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Bootstrapper.
 */
class Bootstrapper {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Bind hooks
	 */
	private function setup() {
		add_action( 'bp_loaded', array( $this, 'load' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'load_admin' ), 9994 ); // pt settings 1.0.6.
		add_action( 'init', array( $this, 'load_translations' ) );
	}

	/**
	 * Load core functions/template tags.
	 * These are non auto loadable constructs.
	 */
	public function load() {

		$path = buddyblog_pro()->path;

		$files = array(
			'src/core/bblpro-functions.php',
			'src/core/bblpro-form-function.php',
			'src/core/bblpro-post-functions.php',
			'src/core/bblpro-shortcode-functions.php',
			'src/core/bblpro-template-functions.php',
			'src/core/bblpro-permissions-functions.php',
			'src/core/bblpro-field-type-functions.php',
			'src/core/bblpro-taxonomy-functions.php',
			'src/core/bblpro-tab-functions.php',
		);

		if ( is_admin() ) {
			$files[] = 'src/admin/admin-misc.php';
			//$files[] = 'src/admin/class-form-admin.php';
		}

		foreach ( $files as $file ) {
			require_once $path . $file;
		}

		Form_Post_Type_Helper::boot();
		Default_Filters::boot();
		Ajax_Post_Thumbnail_Handler::boot();

		Post_Submission_Author_Notifier::boot();
		Post_Submission_Admin_Notifier::boot();

		Post_Approval_Notifier::boot();
		Post_Rejection_Notifier::boot();

		Post_Approval_Helper::boot();
		Post_Submission_Handler::boot();
		Ajax_Post_Submission_Handler::boot();
		Ajax_Draft_Handler::boot();
		Post_Deletion_Handler::boot();
		URL_Filter::boot();
		Capability_Filter::boot();
		Tabs_Helper::boot();
		Compat_Helper::boot();
		Post_Meta_Shortcode_Helper::boot();
		Post_Meta_Display_Helper::boot();
		Assets_Loader::boot();
		Shortcode_Loader::boot();

		do_action( 'bblpro_loaded' );
	}

	/**
	 * Load pt-settings framework
	 */
	public function load_admin() {

		if ( ! is_admin() || ! function_exists( 'buddypress' ) ) {
			return;
		}

		$files = array();
		if ( ! defined( 'DOING_AJAX' ) ) {
			$files[] = 'src/admin/pt-settings/pt-settings-loader.php';
		}
		$files[] = 'src/admin/bblpro-admin-functions.php';

		$path = buddyblog_pro()->path;
		foreach ( $files as $file ) {
			require_once $path . $file;
		}

		Settings_Admin::boot();
		Form_List_Helper::boot();
		Form_Admin_Helper::boot();
		Form_Admin_Post_Fields_Ajax_Handler::boot();
		Form_Admin_Custom_Fields_Ajax_Handler::boot();
	}

	/**
	 * Load translations.
	 */
	public function load_translations() {
		load_plugin_textdomain( 'buddyblog-pro', false, basename( buddyblog_pro()->path ) . '/languages' );
	}
}
