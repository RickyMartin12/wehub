<?php
/**
 * Single Post template page used only if the post is shown on profile
 *
 * You can copy this to your wp-content/themes/[your-active-theme]/buddyblog/kleo/default/ and override if needed.
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
/* Related posts logic */
$related = sq_option( 'related_posts', 1 );
if ( ! is_singular( 'post' ) ) {
	$related = sq_option( 'related_custom_posts', 0 );
}
//post setting
if ( get_cfield( 'related_posts' ) != '' ) {
	$related = get_cfield( 'related_posts' );
}

// disable BuddyPress causing trouble with the_content.
remove_filter( 'the_content', 'bp_replace_the_content' );
?>
<div class="bbl-single-post-wrapper">
	<?php
	global $post;
	if ( have_posts() ) :

		while ( have_posts() ) :
			the_post();

			// Important: Do not remove it. It is used to unhook BuddyPress Theme compatibility comment closing function.
			do_action( 'bblpro_before_blog_post' );
			?>
			<?php get_template_part( 'content', get_post_format() ); ?>

			<?php get_template_part( 'page-parts/posts-social-share' ); ?>

            <!-- Begin Comments -->
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template( '', true );
			}
			?>
            <!-- End Comments -->

		<?php endwhile; ?>

		<?php
		// used to hook back BuddyPress Theme compatibility comment closing function.
		do_action( 'bblpro_after_blog_post' );
		?>
		<?php
		wp_reset_postdata();
		wp_reset_query();
		?>
	<?php else : ?>
        <p> <?php _e( 'No Posts found!', 'buddyblog-pro' ); ?></p>
	<?php endif; ?>
</div>
