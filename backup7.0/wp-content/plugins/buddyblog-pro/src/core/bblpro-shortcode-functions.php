<?php
/**
 * Shortcode helper functions
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core
 * @copyright  Copyright (c) 2021, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Returns the page id associated with the post type create screen.
 *
 * @param $post_type
 *
 * @return mixed
 */
function bblpro_get_create_page_id( $post_type ) {
	return bblpro_get_option( $post_type . '_create_page_id', 0 );
}

/**
 * Check if a dedicated create page enabled for this post type.
 *
 * @param string $post_type post type.
 *
 * @return bool
 */
function bblpro_has_create_page_enabled( $post_type ) {
	return (bool) bblpro_get_create_page_id( $post_type );
}