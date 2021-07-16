<?php
/**
 * Post Meta Display Helper
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
 * Post Meta display Helper.
 */
class Post_Meta_Display_Helper {

	/**
	 * Boots the class.
	 *
	 * @return Post_Meta_Display_Helper
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
		add_filter( 'the_excerpt', array( $this, 'filter_excerpt' ) );
		add_filter( 'the_content', array( $this, 'filter_content' ) );
	}

	/**
	 * Filters Excerpt content.
	 *
	 * @param string $content excerpt content.
	 *
	 * @return string
	 */
	public function filter_excerpt( $content ) {

		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return $content;
		}

		$form_id = bblpro_post_get_form_id( $post_id );

		$before_excerpt = get_post_meta( $form_id, '_buddyblog_post_meta_before_excerpt_entry', true );
		$after_excerpt  = get_post_meta( $form_id, '_buddyblog_post_meta_after_excerpt_entry', true );

		if ( $before_excerpt ) {
			$content = "<div class='bbl-meta-data-list bbl-meta-data-list-excerpt bbl-meta-data-list-before-excerpt'>" . do_shortcode( nl2br( $before_excerpt ) ) . '</div>' . $content;
		}

		if ( $after_excerpt ) {
			$content = $content . "<div class='bbl-meta-data-list bbl-meta-data-list-excerpt bbl-meta-data-list-after-excerpt'>" . do_shortcode( nl2br( $after_excerpt ) ) . '</div>';

		}

		return $content;
	}

	/**
	 * Filters post content.
	 *
	 * @param string $content post content.
	 *
	 * @return string
	 */
	public function filter_content( $content ) {

		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return $content;
		}

		$form_id = bblpro_post_get_form_id( $post_id );

		$before_single = get_post_meta( $form_id, '_buddyblog_post_meta_before_single_entry', true );
		$after_single  = get_post_meta( $form_id, '_buddyblog_post_meta_after_single_entry', true );

		if ( $before_single ) {
			$content = "<div class='bbl-meta-data-list bbl-meta-data-list-excerpt bbl-meta-data-list-before-content'>" . do_shortcode( nl2br( $before_single ) ) . '</div>' . $content;
		}

		if ( $after_single ) {
			$content = $content . "<div class='bbl-meta-data-list bbl-meta-data-list-excerpt bbl-meta-data-list-after-content'>" . do_shortcode( nl2br( $after_single ) ) . '</div>';

		}

		return $content;
	}
}
