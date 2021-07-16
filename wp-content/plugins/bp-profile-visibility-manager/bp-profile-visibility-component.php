<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Adds settings option to the user settings
 */
class BP_Profile_Visibility_Loader {

	/**
	 * Arbitrary data store.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Singleton instance.
	 *
	 * @var BP_Profile_Visibility_Loader
	 */
	private static $instance;

	/**
	 * Will throw away in next update.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * BP_Profile_Visibility_Loader constructor.
	 */
	private function __construct() {

		if ( function_exists( 'bp_get_option' ) ) {
			// admin settings.
			$this->settings = bp_get_option( 'bp_profile_visibility_settings' );
		}

		// setup navigation menu under settings tab of profile.
		add_action( 'bp_settings_setup_nav', array( $this, 'setup_nav' ), 11 );
		// user nav.
		// add filter for saving option.
		add_action( 'bp_init', array( $this, 'save_settings' ) );
		// add Profile Visibility to the user settings menu in wp adminbar.
		// setup admin bar.
		add_action( 'bp_setup_admin_bar', array( $this, 'setup_admin_bar' ), 30 );
	}

	/**
	 * Get teh singleton instance.
	 *
	 * @return BP_Profile_Visibility_Loader
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Dummy.
	 */
	public function includes() {
	}

	/**
	 * Setup nav menu.
	 */
	public function setup_nav() {

		if ( ! $this->is_front_enabled() ) {
			return;
		}

		if ( is_user_logged_in() && bp_is_active( 'settings' ) ) {

			$domain = bp_loggedin_user_domain();

			$show_menu = bp_profile_visibility_user_can_update_settings();

			if ( $show_menu && bp_is_user() ) {
				$domain = bp_displayed_user_domain();
			}

			$settings_link = $domain . bp_get_settings_slug() . '/';

			$name = defined('BP_PLATFORM_VERSION') ? __( 'Profile Visibility', 'bp-profile-visibility-manager' ) : __( 'Profile Privacy', 'bp-profile-visibility-manager' );
			bp_core_new_subnav_item( array(
				'name'            => $name,
				'slug'            => 'account-admin-visibility-mode',
				'parent_url'      => $settings_link,
				'parent_slug'     => bp_get_settings_slug(),
				'screen_function' => array( $this, 'handle_user_settings' ),
				'position'        => 52,
				'user_has_access' => $show_menu,
			) );
		}
	}

	/**
	 * Save settings on front end.
	 */
	public function save_settings() {

		if ( ! $this->is_front_enabled() ) {
			return;
		}

		$user_id = false;

		// only user and super admins can update it.
		if ( bp_is_settings_component() && is_user_logged_in() ) {

			// check if the settings was updated.
			// if the form was submitted, update here.
			if ( ! empty( $_POST['bppv_save_submit'] ) ) {

				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'bp-profile-visibility-manager' ) ) {
					die( __( 'Security check failed', 'bp-profile-visibility-manager' ) );
				}

				// now get all the settings and update.
				if ( bp_profile_visibility_is_admin_user() ) {


					$allowed_keys = array(
						'bp_exclude_in_dir',
						'bp_exclude_in_search',
						'bp_hide_last_active',
						'bp_profile_visibility',
						'bp_allow_friendship_request',
						'bp_friends_list_visibility',
						'bp_allow_follow_request',
						'bp_profile_group_tab_visibility',
					);

				} else {

					$allowed_keys = array();

					if ( bp_profile_visibility_get_option( 'enable_member_directory_visibility', 'yes' ) != 'no' ) {
						$allowed_keys[] = 'bp_exclude_in_dir';
					}

					if ( bp_profile_visibility_get_option( 'enable_member_search_visibility', 'yes' ) != 'no' ) {
						$allowed_keys[] = 'bp_exclude_in_search';
					}

					if ( bp_profile_visibility_get_option( 'enable_friend_request_visibility', 'yes' ) != 'no' ) {
						$allowed_keys[] = 'bp_allow_friendship_request';
					}

					if ( bp_profile_visibility_get_option( 'enable_follow_request_visibility', 'no' ) == 'yes' ) {
						$allowed_keys[] = 'bp_allow_follow_request';
					}

					if ( bp_profile_visibility_get_option( 'enable_last_activity_visibility', 'yes' ) != 'no' ) {
						$allowed_keys[] = 'bp_hide_last_active';
					}

					if ( bp_profile_visibility_get_option( 'enable_profile_privacy_visibility', 'yes' ) != 'no' ) {
						$allowed_keys[] = 'bp_profile_visibility';
					}
					if ( bp_profile_visibility_get_option( 'enable_friends_list_visibility', 'yes' ) != 'no' ) {
						$allowed_keys[] = 'bp_friends_list_visibility';
					}

					if ( bp_profile_visibility_get_option( 'enable_group_tab_visibility', 'yes' ) != 'no' ) {
						$allowed_keys[] = 'bp_profile_group_tab_visibility';
					}

				}

				if ( bp_profile_visibility_user_can_update_settings() ) {
					$user_id = bp_displayed_user_id();
				}

				if ( ! $user_id ) {
					return;
				}

				foreach ( $allowed_keys as $key ) {

					if ( ! isset( $_POST[ $key ] ) ) {
						continue;
					}

					bp_update_user_meta( $user_id, $key, $_POST[ $key ] );

				}

				if ( bp_profile_visibility_get_settings( $user_id, 'bp_hide_last_active' ) == 'yes' ) {
					// do something.
					// ok, do not delete last activity as the user will be hidden, instead, let us do something else
					// we will filter on last activity at other place.
					// BP_Core_User::delete_last_activity( $user_id );
					// bp_delete_user_meta( $user_id, 'last_activity' ); // delete.
				} else {
					// make visible immediately.
					bp_core_record_activity();
				}

				bp_core_add_message( __( 'Settings Updated!', 'bp-profile-visibility-manager' ) );
			}
		}
	}

	/**
	 * Handle the visibility mode settings screen.
	 */
	public function handle_user_settings() {

		// hook the content.
		add_action( 'bp_template_title', array( $this, 'user_settings_page_title' ) );
		add_action( 'bp_template_content', array( $this, 'user_settings_page_content' ) );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Settings page title
	 */
	public function user_settings_page_title() {
		echo  __( 'Profile Privacy', 'bp-profile-visibility-manager' );
	}

	/**
	 * Settings page screen
	 */
	public function user_settings_page_content() {
		bp_profile_visibility_load_template( 'privacy-settings.php' );
		?>

		<?php
	}

	/**
	 * Setup adminbar menu
	 *
	 * @global WP_Admin_Bar $wp_admin_bar adminbar object.
	 */
	public function setup_admin_bar() {

		if ( ! $this->is_front_enabled() ) {
			return;
		}

		// Bail if this is an ajax request.
		if ( defined( 'DOING_AJAX' ) || ! bp_use_wp_admin_bar() || ! bp_is_active( 'settings' ) ) {
			return;
		}

		global $wp_admin_bar;

		// Menus for logged in user.
		if ( is_user_logged_in() && bp_profile_visibility_show_adminbar_menu() ) {

			// Setup the logged in user variables.
			$user_domain   = bp_loggedin_user_domain();
			$settings_link = trailingslashit( $user_domain . bp_get_settings_slug() );


			// General Account.
			$wp_admin_bar->add_menu( array(
				'parent' => 'my-account-settings',
				'id'     => 'my-account-profile-visibility',
				'title'  => __( 'Profile Privacy', 'bp-profile-visibility-manager' ),
				'href'   => trailingslashit( $settings_link . 'account-admin-visibility-mode' ),
			) );
		}
	}

	/**
	 * Set data.
	 *
	 * @param string $key key.
	 * @param mixed  $val value.
	 */
	public function set_data( $key, $val ) {
		$this->data[ $key ] = $val;
	}

	/**
	 * Get specific data item.
	 *
	 * @param string $key key.
	 *
	 * @return mixed
	 */
	public function get_data( $key ) {

		if ( isset( $this->data[ $key ] ) ) {
			return $this->data[ $key ];
		}

		return '';
	}

	/**
	 * Is the plugin enabled for front end?
	 *
	 * @return bool
	 */
	public function is_front_enabled() {

		if ( isset( $this->settings['admin_only'] ) && $this->settings['admin_only'] == 'yes' && ! bp_profile_visibility_is_admin_user() ) {
			return false;
		}

		return true;
	}

}

/**
 * Shortcut.
 *
 * @return BP_Profile_Visibility_Loader
 */
function bp_profile_visibility_loader() {
	return BP_Profile_Visibility_Loader::get_instance();
}

bp_profile_visibility_loader();


