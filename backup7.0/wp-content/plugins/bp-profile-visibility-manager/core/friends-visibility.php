<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Module to handle friends list visibility
 *
 * Class BP_Profile_Visibility_Friends_Helper
 */
class BP_Profile_Visibility_Friends_Helper {

	/**
	 * Singleton.
	 *
	 * @var BP_Profile_Visibility_Friends_Helper
	 */
	private static $instance = null;

	/**
	 * BP_Profile_Visibility_Friends_Helper constructor.
	 */
	private function __construct() {
		$this->setup();
	}


	/**
	 * Get singleton.
	 *
	 * @return BP_Profile_Visibility_Friends_Helper
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup hiding of friends nav.
	 */
	private function setup() {
		add_action( 'bp_friends_setup_nav', array( $this, 'hide_nav' ) );
	}

	/**
	 * Hide friends nav if needed.
	 */
	public function hide_nav() {
		// do not hide if not on a user profile or is own profile or the user is super admin.
		if ( ! bp_is_user() || bp_is_my_profile() || bp_profile_visibility_is_admin_user() ) {
			return;
		}

		$hide              = true;
		$displayed_user_id = bp_displayed_user_id();
		$logged_user_id    = bp_loggedin_user_id();

		$privacy = bp_profile_visibility_get_settings( $displayed_user_id, 'bp_friends_list_visibility' );

		if ( empty( $privacy ) || $privacy == 'public' ) {
			$hide = false;
		} elseif ( $privacy == 'loggedin' && is_user_logged_in() ) {
			$hide = false;
		} elseif ( 'friends' == $privacy && function_exists( 'friends_check_friendship' ) && friends_check_friendship( $logged_user_id, $displayed_user_id ) ) {
			$hide = false;
		} elseif ( 'groups' == $privacy && bp_profile_visibility_has_common_group( $logged_user_id, $displayed_user_id ) ) {
			$hide = false;
		}

		if ( ! $hide ) {
			return;
		}
		bp_core_remove_nav_item( 'friends' );

	}
}

BP_Profile_Visibility_Friends_Helper::get_instance();
