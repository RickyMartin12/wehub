<?php

/**
 * Implement a component to show profile is protected message on user's profile.
 */
class BP_Profile_Visibility_Protected_Component extends BP_Component {
	/**
	 * BP_Profile_Visibility_Protected_Component constructor.
	 */
	public function __construct() {
		parent::start(
			'profile_visibility',
			__( 'Protected', 'bp-profile-visibility-manager' )
		);
	}

	/**
	 * Setup globals.
	 *
	 * @param array $args args.
	 */
	public function setup_globals( $args = array() ) {
		$args = array(
			'slug' => 'visibility-protected',
		);

		parent::setup_globals( $args );
	}

	/**
	 * Setup nav.
	 *
	 * @param array $main_nav manin nav.
	 * @param array $sub_nav sub nav.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		// if not user section, or viewing own profile, do not show.
		if ( ! bp_is_user() || bp_is_my_profile() ) {
			return;
		}

		$main_nav = array(
			'name'                    => __( 'Protected', 'bp-profile-visibility-manager' ),
			'slug'                    => 'visibility-protected',
			'show_for_displayed_user' => $this->is_hidden_profile(),
			'screen_function'         => array( $this, 'screen_protected' ),
			'position'                => 1009, // the last one.
			'default_subnav_slug'     => 'visibility',
		);

		$sub_nav[] = array(
			'name'            => __( 'Protected', 'bp-profile-visibility-manager' ),
			'slug'            => 'visibility',
			'parent_slug'     => 'visibility-protected',
			'parent_url'      => bp_displayed_user_domain() . 'visibility-protected/',
			'screen_function' => array( $this, 'screen_protected' ),
		);

		parent::setup_nav( $main_nav, $sub_nav );
	}


	/**
	 * Check if the profile is hidden.
	 *
	 * @return bool
	 */
	private function is_hidden_profile() {
		return ! bp_profile_visibility_is_visible_profile( bp_displayed_user_id(), bp_loggedin_user_id() );
	}


	/**
	 * Show protected screen.
	 */
	public function screen_protected() {
		add_action( 'bp_template_content', array( $this, 'screen_content' ) );
		bp_core_load_template( apply_filters( 'bp_members_screen_display_privacy_template', 'members/single/plugins' ) );
	}

	/**
	 * Protected screen content.
	 */
	public function screen_content() {
		bp_profile_visibility_load_template( 'profile-privacy-notice.php' );
	}

}

/**
 * Setup protected component.
 */
function bp_profile_visibility_register_protection_component() {
	buddypress()->profile_visibility = new BP_Profile_Visibility_Protected_Component();
}

add_action( 'bp_setup_components', 'bp_profile_visibility_register_protection_component', 6 );
