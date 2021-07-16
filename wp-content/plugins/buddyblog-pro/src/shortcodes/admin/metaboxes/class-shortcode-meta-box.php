<?php
/**
 * Shortcodes Metabox base
 *
 * @package    BuddyBlog_Pro
 * @subpackage Shortcodes\Admin\Metaboxes
 * @copyright  Copyright (c) 2021, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */
namespace BuddyBlog_Pro\Shortcodes\Admin\Metaboxes;

use BuddyBlog_Pro\Admin\Metaboxes\BBL_Meta_Box;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

abstract class Shortcode_Meta_Box extends BBL_Meta_Box {

	/**
	 * Returns the input field value if present or default.
	 *
	 * @param string     $option option name.
	 * @param mixed|null $default default value.
	 *
	 * @return mixed|null
	 */
	protected function input( $option, $default = null ) {
		//$option = 'bbl-input-' . $option;
		return isset( $_POST[ $option ] ) ? wp_unslash( $_POST[ $option ] ) : $default;
	}

	/**
	 * Returns the meta value or default is meta value is not set.
	 *
	 * @param int $post_id p[ost id.
	 * @param string $key meta key.
	 * @param mixed $default efault value.
	 * @param bool $is_single is single meta.
	 *
	 * @return mixed|string
	 */
	protected function get_meta( $post_id, $key, $default = '', $is_single = true ) {

		$val = get_post_meta( $post_id, $key, $is_single );

		if ( '' === $val ) {
			return $default;
		}

		return $val;
	}

	/**
	 * Get roles
	 *
	 * @return array
	 */
	protected function get_roles() {
		$roles = wp_roles()->roles;

		$user_roles        = array();
		$user_roles['logged_in'] = __( 'All Members', 'buddyblog-pro' );

		foreach ( $roles as $role => $detail ) {
			$user_roles[ $role ] = $detail['name'];
		}

		return $user_roles;
	}


	/**
	 * Get forms
	 *
	 * @return array
	 */
	protected function get_forms() {

		$forms = get_posts(
			array(
				'numberposts' => - 1,
				'post_status' => 'publish',
				'post_type'   => bblpro_get_form_post_type(),
			)
		);

		if ( empty( $forms ) ) {
			return array();
		}

		return $forms;
	}
}