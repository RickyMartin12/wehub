<?php
/**
 * Post Meta Shortcode Helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core
 * @copyright  Copyright (c) 2021, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Core;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Post Meta Shortcode Helper.
 */
class Post_Meta_Shortcode_Helper {

	/**
	 * Boots the class.
	 *
	 * @return Post_Meta_Shortcode_Helper
	 */
	public static function boot() {

		static $self = null;

		if ( is_null( $self ) ) {
			$self = new self();
			$self->setup();
		}

		return $self;
	}

	/**
	 * Setup shortcode.
	 */
	public function setup() {
		add_shortcode( 'bbl-meta', array( $this, 'meta_shortcode' ) );
	}


	/**
	 * Handles shortcode.
	 *
	 * @param array  $atts attributes.
	 * @param string $content content.
	 *
	 * @return string
	 */
	public function meta_shortcode( $atts = array(), $content = '' ) {

		$atts = shortcode_atts(
			array(
				'key'        => '',
				'sep'        => ',', // separator.
				'show_empty' => 1,
				'post_id'    => get_the_ID(),
			),
			$atts,
			'bbl-meta'
		);

		if ( empty( $atts['key'] ) || empty( $atts['post_id'] ) ) {
			return ''; // invalid, must have key specified.
		}

		return bblpro_get_post_meta( $atts['post_id'], $atts['key'], $atts['sep'] );
	}
}
