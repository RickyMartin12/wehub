<?php
/**
 * Single Post template page used only if the post is shown on profile
 *
 * You can copy this to your wp-content/themes/[your-active-theme]/buddyblog/buddyboss/default/ and override if needed.
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


if ( bp_is_my_profile() || is_super_admin() ) {
	$status = 'any';
} else {
	$status = 'publish';
}

$query_args = array(
	'author'      => bp_displayed_user_id(),
	'post_type'   => bblpro_get_current_post_type(),
	'post_status' => $status,
	'p'           => bblpro_get_queried_post_id(),
);


query_posts( $query_args );
global $post;
// disable BuddyPress causing trouble with the_content.
remove_filter( 'the_content', 'bp_replace_the_content' );
if ( have_posts() ) :

	do_action( THEME_HOOK_PREFIX . '_template_parts_content_top' );

	while ( have_posts() ) :
		the_post();
		do_action( 'bblpro_before_blog_post' );
		//do_action( THEME_HOOK_PREFIX . '_single_template_part_content', get_post_type() );
		get_template_part( 'template-parts/content', get_post_type() );

		/**
		 * If comments are open or we have at least one comment, load up the comment template.
		 */
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
		do_action( 'bblpro_after_blog_post' );
	endwhile; // End of the loop.

endif;
wp_reset_postdata();
wp_reset_query();