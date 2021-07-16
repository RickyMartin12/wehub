<?php
/**
 * Shortcodes Loader
 *
 * @package    BuddyBlog_Pro
 * @subpackage Shortcodes
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */
namespace BuddyBlog_Pro\Shortcodes;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Class Shortcode_Loader
 */
class Shortcode_Loader {

	/**
	 * Boot class
	 */
	public static function boot() {
		//Shortcode_Post_Type_Helper::boot();
		BBL_Create_Form_Shortcode::boot();
	}
}
