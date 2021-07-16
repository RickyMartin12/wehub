<?php
/**
 * URL Filter
 *
 * @package    BuddyBlog_Pro
 * @subpackage Handlers
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Handlers;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * URL Filter
 */
class URL_Filter {

	/**
	 * Class self boot
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	private function setup() {

		add_filter( 'post_link', array( $this, 'post_permalink_filter' ), 10, 3 );
		add_filter( 'page_link', array( $this, 'post_permalink_filter' ), 10, 3 );
		add_filter( 'post_type_link', array( $this, 'post_permalink_filter' ), 10, 3 );
		add_filter( 'get_edit_post_link', array( $this, 'post_edit_link_filter' ), 10, 3 );
	}

	/**
	 * Filter permalink.
	 *
	 * @param string       $url url.
	 * @param int|\WP_Post $post post id or object.
	 * @param string       $leave sub.
	 *
	 * @return string
	 */
	public function post_permalink_filter( $url, $post, $leave ) {

		$post = get_post( $post );

		if ( ! $post || ! bblpro_is_post_type_enabled( $post->post_type ) ) {
			return $url;
		}

		if ( ! bblpro_is_action_enabled( $post->post_author, $post->post_type, 'view' ) ) {
			return $url;
		}
		if ( ! bblpro_is_action_available( $post->post_author, $post->post_type, 'view' ) ) {
			return $url;
		}

		if ( $this->is_using_page_builder( $post->ID ) ) {
			return $url;
		}

		if ( $this->is_editing( $post->ID ) ) {
			return $url;
		}

		return bblpro_get_post_view_url( $post->ID );
	}

	/**
	 * Filter edit link.
	 *
	 * @param string $url url.
	 * @param int    $post_id or object.
	 * @param string $context context.
	 *
	 * @return string
	 */
	public function post_edit_link_filter( $url, $post_id, $context ) {
		// Inside wp admin, let us not change the edit experience.
		if ( is_admin() ) {
			return $url;
		}

		$post = get_post( $post_id );

		if ( ! $post || ! bblpro_is_post_type_enabled( $post->post_type ) ) {
			return $url;
		}

		if ( ! bblpro_is_post_editing_enabled( $post->post_author, $post->post_type ) ) {
			return $url;
		}

		return bblpro_get_post_edit_url( $post_id );
	}


	/**
	 * Is the current Post/Page using page builder?
	 *
	 * @param int $post_id post id.
	 *
	 * @return bool
	 */
	private function is_using_page_builder( $post_id = 0 ) {

		$using = false;

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			$using = false;
		} elseif ( function_exists( 'et_pb_is_pagebuilder_used' ) ) {
			$using = et_pb_is_pagebuilder_used( $post_id );
		} elseif ( is_page_template(
			array(
				'templates/page-canvas.php',
				'elementor_header_footer',
				'elementor_canvas',
			)
		)
		) {
			$using = true;
		}

		return apply_filters( 'bblpro_using_page_builder', $using, $post_id );
	}

	/**
	 * Check if post is being edited in elementor.
	 *
	 * @param int $post_id post id.
	 *
	 * @return bool
	 */
	private function is_editing( $post_id ) {

		$is = false;
		if ( is_admin() ) {

			$action = isset( $_REQUEST['action'] ) ? wp_unslash( $_REQUEST['action'] ) : '';
			// elementor.
			if ( 'elementor' === $action || 'elementor_ajax' === $action ) {
				$is = true;
			}
		}

		return $is;
	}

}
