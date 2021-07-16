<?php
/**
 * Pending Posts list template page
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

// yes, we are forcing main query, will reset later.
query_posts(
	bblpro_get_posts_query_args(
		array(
			'post_type' => bblpro_get_current_post_type(),
			'status'    => 'pending',
		)
	)
);
// disable BuddyPress causing trouble with the_content.
remove_filter( 'the_content', 'bp_replace_the_content' );
?>
<?php do_action( 'cb_before_blog_contents' ); ?>

<?php if ( have_posts() ) : ?>

    <div id='posts-list' class="<?php cb_post_list_class(); ?>">

		<?php
		while ( have_posts() ) :
			the_post();

			cb_get_template_part( 'template-parts/entry-' . cb_get_posts_display_type(), get_post_type(), 'loop' );

		endwhile;
		?>
    </div>

	<?php cb_posts_pagination(); ?>

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
<?php endif; ?>

<?php do_action( 'cb_after_blog_contents' ); ?>


<?php
wp_reset_query();
wp_reset_postdata();
