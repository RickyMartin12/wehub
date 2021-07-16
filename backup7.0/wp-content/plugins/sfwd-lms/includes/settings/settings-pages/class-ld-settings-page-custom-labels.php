<?php
/**
 * LearnDash Settings Page Custom Labels.
 *
 * @since 2.4.0
 * @package LearnDash\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( ! class_exists( 'LearnDash_Settings_Page_Custom_Labels' ) ) ) {
	/**
	 * Class LearnDash Settings Page Custom Labels.
	 *
	 * @since 2.4.0
	 */
	class LearnDash_Settings_Page_Custom_Labels extends LearnDash_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {
			$this->parent_menu_page_url  = 'admin.php?page=learndash_lms_settings';
			$this->menu_page_capability  = LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'learndash_lms_settings_custom_labels';
			$this->settings_page_title   = esc_html__( 'Custom Labels', 'learndash' );
			$this->settings_tab_title    = $this->settings_page_title;
			$this->settings_tab_priority = 20;
			$this->show_quick_links_meta = false;

			parent::__construct();
		}
	}
}
add_action(
	'learndash_settings_pages_init',
	function() {
		LearnDash_Settings_Page_Custom_Labels::add_page_instance();
	}
);
