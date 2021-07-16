<?php
/**
 * Single Post template page used only if the post is shown on profile
 *
 * You can copy this to your wp-content/themes/[your-active-theme]/buddyblog/community-builder/default/ and override if needed.
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
// disable BuddyPress causing trouble with the_content.
remove_filter( 'the_content', 'bp_replace_the_content' );
?>
<?php if ( have_posts() ) : ?>

	<?php
	while ( have_posts() ) :
		the_post();
        // Important: Do not remove it.
		// It is used to unhook BuddyPress Theme compatibility comment closing function.
		do_action( 'bblpro_before_blog_post' );
		add_filter( 'cb_show_post_thumbnail', '__return_true' );
		cb_get_template_part( 'template-parts/entry', get_post_type(), 'single' );

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;

	endwhile; // End of the loop.

	// Important: Do not remove it.
    // It is used to hook back BuddyPress Theme compatibility comment closing function.
	do_action( 'bblpro_after_blog_post' );

	?>

	<?php cb_post_navigation(); ?>

<?php else : ?>

	<?php cb_get_template_part( 'template-parts/entry', '404', '404' ); ?>

<?php endif; ?>