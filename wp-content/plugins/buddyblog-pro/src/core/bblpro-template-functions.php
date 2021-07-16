<?php
/**
 * Template functions
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Get the base directory where our templates are stored.
 * It is relative to the theme too.
 * For example, if we specify 'buddyblog/template-pack' the templates will be searched in
 *  1. themes/twentyseventeen(or the current active child theme or theme)/buddyblog/template-pack
 *  2. If not found in child theme and not found in parent theme,
 *     It will fallback to plugins/buddyblog/templates/buddyblog/template-pack
 *
 * @return string
 */
function bblpro_get_template_base_dir() {
	return apply_filters( 'bblpro_template_base_dir', 'buddyblog' );
}

/**
 * Get sorted template stacks.
 *
 * Lower number stacks have higher priority while locating template.
 *
 * @return array
 */
function bblpro_get_template_stacks() {

	$template_stacks = apply_filters( 'bblpro_template_stack', array( 'default' => 10000 ) );

	uasort(
		$template_stacks,
		function ( $a, $b ) {
			return $a - $b;
		}
	);

	return $template_stacks;
}

/**
 * Get template part (for templates like the user-home). Loads the template.
 *
 * @param string $post_type Post type.
 * @param string $slug template part name.
 * @param string $name template part part name(optional, default:'').
 * @param string $fallback_path Fallback template directory base path.
 */
function bblpro_get_template_part( $post_type, $slug, $name = '', $fallback_path = '' ) {

	$templates = array();

	if ( $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	$template = bblpro_locate_template( $post_type, $templates, false, $fallback_path );

	$template = apply_filters( 'bblpro_get_template_part', $template, $slug, $name, $fallback_path, $post_type );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *        your-child-theme  /    buddyblog    / default(current template pack) / post_type|default /   $template_name
 *        your-theme        /    buddyblog    / default(current template pack) / post_type|default /   $template_name
 *        wp-content/plugins/buddyblog-pro/templates/        /    buddyblog    / default(current template pack) / post_type|default /   $template_name
 *
 *        $default_path     / default(current template pack) / post_type|default /   $template_name
 *
 * @see bblpro_get_template_part() if you are looking to load template parts.
 *      This function is aimed at plugin developer.
 *
 * @param string $post_type post type.
 * @param array  $template_files array of php templates.
 * @param bool   $load whether to load or return the path.
 * @param string $default_path (default: ''), path to use as base. Allows plugins to override it.
 *
 * @return string
 */
function bblpro_locate_template( $post_type, $template_files, $load = false, $default_path = '' ) {

	$base_dir = bblpro_get_template_base_dir();

	// Fallback to BuddyBlog pro included plugin template path.
	if ( ! $default_path ) {
		$default_path = buddyblog_pro()->path . 'templates/' . $base_dir;
	}

	$default_path = untrailingslashit( $default_path );
	// the array looks like an array of relative paths ee.g user/xyx.php. etc.
	// remove any empty entry.
	$template_files = array_filter( (array) $template_files );

	$located = '';

	$template_stacks = bblpro_get_template_stacks();

	foreach ( $template_stacks as $template_stack => $load_order ) {

		if ( ! $template_stack ) {
			continue;
		}

		foreach ( $template_files as $template_file ) {

			if ( ! $template_file ) {
				continue;
			}

			// locate theme/buddyblog/template-pack-id/post_type/files.
			$theme_template_file = $base_dir . '/' . $template_stack . '/' . $post_type . '/' . $template_file;

			$located = locate_template( array( $theme_template_file ), false );
			// fallback to plugin/templates/buddyblog/template-pack-id/post_type/files.
			if ( ! $located && is_readable( $default_path . '/' . $template_stack . '/' . $post_type . '/' . $template_file ) ) {
				$located = $default_path . '/' . $template_stack . '/' . $post_type . '/' . $template_file;
			}

			// if not found, fall back to the 'default' template.
			if ( ! $located ) {
				$theme_template_file = $base_dir . '/' . $template_stack . '/default/' . $template_file;

				$located = locate_template( array( $theme_template_file ), false );
				if ( ! $located && is_readable( $default_path . '/' . $template_stack . '/default/' . $template_file ) ) {
					$located = $default_path . '/' . $template_stack . '/default/' . $template_file;
				}
			}

			if ( $located ) {
				break 2;
			}
		}
	}

	if ( $load && $located ) {
		load_template( $located, false );
	}

	// Return what we found.
	return apply_filters( 'bblpro_located_template', $located, $template_files, $default_path, $post_type );
}

/**
 * Locate asset from theme template dirs/plugin.
 *
 * It loads assets relative to buddyblog/{current_template_pack}/
 *
 * @param string $file file name.
 * @param string $default_path default fallback patch to check for asset.
 * @param string $default_url default fallback url if asset is not found in themes.
 *
 * @return string
 */
function bblpro_locate_asset( $file, $default_path = '', $default_url = '' ) {

	$base_dir = bblpro_get_template_base_dir();

	// Fallback to BuddyBlog Pro included plugin template path.
	if ( ! $default_url ) {
		$default_url  = buddyblog_pro()->url . 'templates/' . $base_dir;
		$default_path = buddyblog_pro()->path . 'templates/' . $base_dir;
	}

	$file = ltrim( $file, '/' );

	if ( empty( $file ) ) {
		return '';
	}

	$default_url = rtrim( $default_url, '/' );

	$template_stacks = bblpro_get_template_stacks();

	$url = '';
	foreach ( $template_stacks as $template_stack => $template_order ) {
		// default/path.
		$asset_rel_path = $base_dir . '/' . $template_stack . '/' . $file;

		if ( file_exists( get_stylesheet_directory() . '/' . $asset_rel_path ) ) {
			$url = get_stylesheet_directory_uri() . '/' . $asset_rel_path;
		} elseif ( file_exists( get_template_directory() . '/' . $asset_rel_path ) ) {
			$url = get_template_directory_uri() . '/' . $asset_rel_path;
		} elseif ( $default_path && file_exists( $default_path . '/' . $template_stack . '/' . $file ) ) {
			$url = $default_url . '/' . $template_stack . '/' . $file;
		}

		if ( $url ) {
			break;
		}
	}

	return $url;
}