<?php
/**
 * Members Directory protector
 *
 * @package    BP Profile Visibility Manager
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Protector.
 */
class BPPV_Members_Directory_Protector {
	/**
	 * Boot.
	 *
	 * @return BPPV_Members_Directory_Protector
	 */
	public static function boot() {
		$self = new self();
		$self->setup();

		return $self;
	}

	/**
	 * Setup hooks.
	 */
	public function setup() {
		add_action( 'template_redirect', array( $this, 'check_access' ) );
	}

	/**
	 * Check access and redirect if needed.
	 */
	public function check_access() {

		if ( ! bp_is_members_directory() ) {
			return;
		}

		$enabled = bp_profile_visibility_get_option( 'enable_members_directory_privacy', 'no' );
		if ( empty( $enabled ) || 'yes' !== $enabled ) {
			return;// not enabled.
		}

		$privacy = bp_profile_visibility_get_option( 'members_directory_privacy', 'all' );
		if ( empty( $privacy ) || 'all' == $privacy ) {
			return;
		}
		// all other privacy needs user to login.
		if ( ! is_user_logged_in() ) {
			bp_core_redirect( wp_login_url( bp_get_members_directory_permalink() ) );
		}

		if ( 'loggedin' == $privacy ) {
			return;
		} elseif ( 'admin' == $privacy && bp_profile_visibility_is_admin_user() ) {
			return;
		}

		// all other cases, redirect.
		$redirect_to = apply_filters( 'bp_profile_visibility_members_directory_redirect', home_url( '/' ) );
		bp_core_redirect( $redirect_to );
	}

}
BPPV_Members_Directory_Protector::boot();