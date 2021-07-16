<?php
/**
 * Create Post template page
 *
 * @package    BuddyBlog_Pro
 * @subpackage Templates/default/default
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

require_once 'class-yz-bbl-posts-list.php';
$posts_tab = new YZ_BBL_Posts_List();

// Get Posts Core
$posts_tab->posts_core( bblpro_get_posts_query_args(
	array(
		'post_type' => bblpro_get_current_post_type(),
	)
) );
