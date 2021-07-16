<?php
/**
 * Miscellaneous
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Fires 'bblpro_form_admin_enqueue_scripts' action on BuddyBlog form edit/create pages.
 *
 * Allows modules to reliably load scripts/styles on the form add/edit screen
 *
 * @param string $hook_suffix hook suffix.
 */
function bblogpro_admin_scripts( $hook_suffix ) {

	if ( 'post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix ) {
		return;
	}

	if ( ! bblpro_is_form_admin() ) {
		return;
	}

	do_action( 'bblpro_form_admin_enqueue_scripts' );
}
add_action( 'admin_enqueue_scripts', 'bblogpro_admin_scripts' );

/**
 * Adds View docs & Forms on plugin row on plugins screen
 *
 * @param array $actions links to be shown in the plugin list context.
 *
 * @return array
 */
function bblogpro_plugin_action_links( $actions ) {

	if ( post_type_exists( bblpro_get_form_post_type() ) ) {
		$actions['view-bblogpro-forms'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', admin_url( 'edit.php?post_type=' . bblpro_get_form_post_type() ), __( 'View Forms', 'buddyblog-pro' ), __( 'View Forms', 'buddyblog-pro' ) );
	}

	$actions['view-bblogpro-docs'] = sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', 'https://buddydev.com/docs/buddyblog-pro/', _x( 'Documentation', 'plugin row link label', 'buddyblog-pro' ) );

	return $actions;
}

add_filter( 'plugin_action_links_' . plugin_basename( buddyblog_pro()->get_file() ), 'bblogpro_plugin_action_links' );
