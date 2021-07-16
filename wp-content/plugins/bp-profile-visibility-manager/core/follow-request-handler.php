<?php
/**
 * Short Description
 *
 * @package    BuddyPress profile Visibility
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Follow button and action visibility.
 *
 * Class BPPV_Follow_Request_Handler
 */
class BPPV_Follow_Request_Handler {

	/**
	 * Boot.
	 *
	 * @return BPPV_Follow_Request_Handler
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
		add_filter( 'bp_follow_get_add_follow_button', array( $this, 'filter_follow_button' ) );
		add_action( 'bp_actions', array( $this, 'gate_follow' ), 8 );

		add_action( 'wp_ajax_bp_follow', array( $this, 'gate_follow_ajax' ), 8 );
	}

	/**
	 * Filter button args.
	 *
	 * @param array $args args.
	 *
	 * @return array
	 */
	public function filter_follow_button( $args ) {

		if ( empty( $args ) || ! is_array( $args ) ) {
			return $args;
		}

		if ( 'follow' !== $args['component'] || 'not-following' !== $args['id'] ) {
			return $args;
		}


		$user_link_id = $args['link_id'];
		$user_id      = absint( str_replace( 'follow-', '', $user_link_id ) );

		if ( bp_profile_visibility_get_settings( $user_id, 'bp_allow_follow_request' ) === 'no' ) {
			return array();
		}

		// get the friendship setting for this user.
		return $args;

	}

	/**
	 * Gate follow action.
	 */
	public function gate_follow() {

		if ( ! function_exists( 'bp_follow_start_following' ) ) {
			return;
		}

		$bp = buddypress();

		if ( ! bp_is_current_component( $bp->follow->followers->slug ) || ! bp_is_current_action( 'start' ) ) {
			return;
		}

		if ( bp_displayed_user_id() === bp_loggedin_user_id() ) {
			return;
		}

		check_admin_referer( 'start_following' );

		if ( $this->is_allowed( bp_displayed_user_id() ) ) {
			return;
		}

		// this action is not allowed.
		bp_core_add_message( __( 'Unable to follow.', 'bp-profile-visibility-manager' ), 'error' );

		bp_core_redirect( wp_get_referer() );

	}

	/**
	 * Gate follow action
	 */
	public function gate_follow_ajax() {
		// Gate ajax follow action.
		check_admin_referer( 'start_following' );

		$user_id = absint( $_POST['uid'] );
		if ( ! $user_id ) {
			return;
		}

		if ( $this->is_allowed( $user_id ) ) {
			return;// allowed.
		}

		$link_class = ! empty( $_POST['link_class'] ) ? str_replace( 'follow ', '', $_POST['link_class'] ) : false;

		// Now allowed.
		// output fallback invalid button.
		$button = bp_get_button( array(
			'id'         => 'invalid',
			'link_href'  => 'javascript:;',
			'component'  => 'follow',
			'wrapper'    => false,
			'link_class' => $link_class,
			'link_text'  => __( 'Error following user', 'bp-profile-visibility-manager' ),
		) );

		wp_send_json_success( array(
			'button' => $button,
		) );
	}

	/**
	 * Check if following is allowed.
	 *
	 * @param int $user_id user id.
	 *
	 * @return bool
	 */
	private function is_allowed( $user_id ) {
		$allowed = bp_profile_visibility_get_settings( $user_id, 'bp_allow_follow_request' );

		if ( 'no' === $allowed ) {
			return false;
		}

		return true;
	}
}

BPPV_Follow_Request_Handler::boot();
