<?php
/**
 * Published posts list template page
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

// yes, we are forcing main query, will reset later.
query_posts(
	bblpro_get_posts_query_args(
		array(
			'post_type' => bblpro_get_current_post_type(),
			'status'    => 'publish',
		)
	)
);
$blog_type = 'masonry'; // standard, grid, masonry.
$blog_type = apply_filters( 'bb_blog_type', $blog_type );

$class = '';

if ( 'masonry' === $blog_type ) {
	$class = 'bb-masonry';
} elseif ( 'grid' === $blog_type ) {
	$class = 'bb-grid';
} else {
	$class = 'bb-standard';
}

// disable BuddyPress causing trouble with the_content.
remove_filter( 'the_content', 'bp_replace_the_content' );
?>

<?php if ( have_posts() ) : ?>

	<?php do_action( THEME_HOOK_PREFIX . '_template_parts_content_top' ); ?>
    <div class="bbl-posts-list">
        <div class="post-grid <?php echo esc_attr( $class ); ?>">

			<?php if ( 'masonry' === $blog_type ) { ?>
                <div class="bb-masonry-sizer"></div>
			<?php } ?>

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 *
				 * I am not sure why we need to laod content-post_format.php only if blog_type is not standard
				 * lets load that in all cases.
				 * Please change this if required.
				 */
				bblpro_locate_template( bblpro_get_current_post_type(), array( 'entry-content.php' ), true );

				//bblpro_get_template_part( 'template-parts/content', apply_filters( 'bb_blog_content', get_post_format() ) );

			endwhile;
			?>
        </div>
    </div>
    <div class="pagination bbl-pagination">
		<?php bblpro_paginate(); ?>
    </div>
<?php else : ?>
	<?php if ( bp_is_my_profile() && bblpro_user_can_create_post( get_current_user_id(), bblpro_get_current_post_type() ) ) : ?>
        <div class="bbl-notice bbl-create-post-notice">
            <p> <?php _e( "You haven't published anything yet.", 'buddyblog-pro' ); ?> <a href="<?php echo esc_url( bblpro_get_post_type_create_tab_url( get_current_user_id(), bblpro_get_current_post_type() ) ); ?>"> <?php _e( 'Add New', 'buddyblog-pro' ); ?></a></p>
        </div>
	<?php else : ?>
        <div class="bbl-notice bbl-not-posted-notice">
            <p> <?php echo sprintf( __( "%s hasn't posted anything yet.", 'buddyblog-pro' ), bp_get_displayed_user_fullname() ); ?></p>
        </div>

	<?php endif; ?>
<?php endif; ?>
<?php
wp_reset_query();
wp_reset_postdata();
