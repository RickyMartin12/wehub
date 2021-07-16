<?php
/**
 * Pending Posts list template page
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

// yes, we are forcing main query, will reset later.
query_posts(
	bblpro_get_posts_query_args(
		array(
			'post_type' => bblpro_get_current_post_type(),
			'status'    => 'pending',
		)
	)
);
$blog_type = sq_option( 'blog_type', 'masonry' );
$blog_type = apply_filters( 'kleo_blog_type', $blog_type );
// disable BuddyPress causing trouble with the_content.
remove_filter( 'the_content', 'bp_replace_the_content' );
?>
<?php
if ( have_posts() ) :
	if ( sq_option( 'blog_switch_layout', 0 ) == 1 ) : /* Blog Layout Switcher */ ?>

		<?php kleo_view_switch( sq_option( 'blog_enabled_layouts' ), $blog_type ); ?>

	<?php endif; ?>

	<?php do_action( 'kleo_before_blog_outer_content' ); ?>

	<?php
	if ( 'masonry' === $blog_type ) :
		?>
		<div class="row responsive-cols kleo-masonry per-row-<?php echo sq_option( 'blog_columns', 3 ); ?>">
	<?php endif; ?>

	<?php
	// Start the Loop.
	while ( have_posts() ) :
		the_post();

		/*
		 * Include the post format-specific template for the content. If you want to
		 * use this in a child theme, then include a file called called content-___.php
		 * (where ___ is the post format) and that will be used instead.
		 */
		?>
		<?php
		if ( 'masonry' === $blog_type ) :
			get_template_part( 'page-parts/post-content-' . $blog_type );
		else :
			get_template_part( 'content', get_post_format() );
		endif;

	endwhile;

	if ( 'masonry' === $blog_type ) :
		?>
		</div>
	<?php endif; ?>

	<?php

	// Post navigation.
	kleo_pagination();
	?>
<?php else : ?>
	<?php if ( bp_is_my_profile() && bblpro_user_can_create_post( get_current_user_id(), bblpro_get_current_post_type() ) ) : ?>
		<div class="bbl-notice bbl-create-post-notice">
			<p> <?php _e( "You don't have any pending post.", 'buddyblog-pro' ); ?> <a href="<?php echo esc_url( bblpro_get_post_type_create_tab_url( get_current_user_id(), bblpro_get_current_post_type() ) ); ?>"> <?php _e( 'Add New', 'buddyblog-pro' ); ?></a></p>
		</div>
	<?php else : ?>
		<div class="bbl-notice bbl-not-posted-notice">
			<p> <?php echo sprintf( __( "%s hasn't submitted anything yet.", 'buddyblog-pro' ), bp_get_displayed_user_fullname() ); ?></p>
		</div>

	<?php endif; ?>
<?php
endif;

wp_reset_query();
wp_reset_postdata();
