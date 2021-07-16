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

// yes, we are forcing main query, will reset later.
query_posts(
	bblpro_get_posts_query_args(
		array(
			'post_type' => bblpro_get_current_post_type(),
			'status'    => 'draft',
		)
	)
);

$posted = false;
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$posted = true;
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<?php
				the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
				?>
			</header><!-- .entry-header -->

			<figure class="post-thumbnail">
				<a class="post-thumbnail-inner" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
					<?php the_post_thumbnail( 'post-thumbnail' ); ?>
				</a>
			</figure>


			<div class="entry-content">
				<?php the_excerpt(); ?>
			</div><!-- .entry-content -->

			<footer class="entry-footer">
				<div class="entry-meta-box">
					<span><?php printf( _x( 'by %s', 'Post written by...', 'buddyblog-pro' ), bp_core_get_userlink( $post->post_author ) ); ?></span>
					<?php _e( 'on', 'buddyblog-pro' ); ?>
					<span class="date"><?php printf( __( '%1$s <span>in %2$s</span>', 'buddyblog-pro' ), get_the_date(), get_the_category_list( ', ' ) ); ?></span>
					<?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddyblog-pro' ), ', ', '</span>' ); ?> |
					<span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'buddyblog-pro' ), __( '1 Comment &#187;', 'buddyblog-pro' ), __( '% Comments &#187;', 'buddyblog-pro' ) ); ?></span>

				</div>


				<div class="post-actions">
					<?php echo bblpro_get_post_edit_link( get_the_ID() ); ?>
					<?php echo bblpro_get_post_delete_link( get_the_ID() ); ?>
				</div>

			</footer><!-- .entry-footer -->
		</article><!-- #post-<?php the_ID(); ?> -->

	<?php endwhile; ?>
	<div class="pagination bbl-pagination">
		<?php bblpro_paginate(); ?>
	</div>
<?php else : ?>
	<p><?php _e( 'There are no posts by this user at the moment. Please check back later!', 'buddyblog-pro' ); ?></p>
<?php endif; ?>

<?php
wp_reset_query();
wp_reset_postdata();
?>

<?php if ( ! $posted && bp_is_my_profile() && bblpro_user_can_create_post( get_current_user_id(), bblpro_get_current_post_type() ) ) : ?>
	<p> <?php _e( "You haven't posted anything yet.", 'buddyblog-pro' ); ?> <a
			href="<?php echo bblpro_get_post_type_create_tab_url( get_current_user_id(), bblpro_get_current_post_type() ); ?>"> <?php _e( 'Add New', 'buddyblog-pro' ); ?></a>
	</p>

<?php elseif ( ! $posted && bp_is_user() ) : ?>
	<?php echo sprintf( "<p>%s has't posted anything yet.</p>", bp_get_displayed_user_fullname() ); ?>
<?php endif; ?>
