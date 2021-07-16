<?php
/**
 * Filters
 *
 * @package    BuddyBlog_Pro
 * @subpackage Handlers
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Handlers;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Class Hooks_Helper
 */
class Default_Filters {

	/**
	 * Boot itself
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	private function setup() {

		add_action( 'init', array( $this, 'register_post_type_activity_support' ), 100 );
		// comments.
		add_action( 'bblpro_before_blog_post', array( $this, 'disable_bp_comment_filter' ) );
		add_action( 'bblpro_after_blog_post', array( $this, 'enable_bp_comment_filter' ) );
		// fix comment form redirect.
		add_action( 'comment_form', array( $this, 'fix_comment_form_redirect' ) );
		add_filter( 'plupload_default_params', array( $this, 'filter_uploader_settings' ) );
		add_filter( 'ajax_query_attachments_args', array( $this, 'filter_ajax_attachment_args' ) );
	}

	/**
	 * Register activity support for post types.
	 */
	public function register_post_type_activity_support() {

		// Check if the Activity component is active before using it.
		if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) ) {
			return;
		}

		$post_types = (array) bblpro_get_option( 'enabled_post_types', array() );

		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object ) {
				continue;
			}

			// check if the post type supports activity integration.
			if ( ! bblpro_get_option( $post_type . '_enable_activity', false ) ) {
				continue;
			}

			// Don't forget to add the 'buddypress-activity' support!
			add_post_type_support( $post_type, 'buddypress-activity' );
			$label = $post_type_object->labels->singular_name;

			bp_activity_set_post_type_tracking_args(
				$post_type,
				array(
					'action_id'                         => 'new_' . $post_type,
					/* translators: %s post type label*/
					'bp_activity_admin_filter'          => sprintf( __( 'Published a new %s', 'buddyblog-pro' ), $label ),
					'bp_activity_front_filter'          => $label,
					/* translators: 1: User display name, 2: post type label */
					'bp_activity_new_post'              => __( '%1$s posted a new <a href="%2$s">' . $label . '</a>', 'buddyblog-pro' ),
					'bp_activity_new_post_ms'           => __( '%1$s posted a new <a href="%2$s">' . $label . '</a>, on the site %3$s', 'buddyblog-pro' ),
					'contexts'                          => array( 'activity', 'member' ),
					'activity_comment'                  => true,
					'comment_action_id'                 => "new_{$post_type}_comment",
					'bp_activity_comments_admin_filter' => sprintf( __( 'Commented a %s', 'buddyblog-pro' ), $label ),
					'bp_activity_comments_front_filter' => sprintf( __( '%s Comments', 'buddyblog-pro' ), $label ),
					'bp_activity_new_comment'           => __( '%1$s commented on the <a href="%2$s">' . $label . '</a>', 'buddyblog-pro' ),
					'bp_activity_new_comment_ms'        => __( '%1$s commented on the <a href="%2$s">' . $label . '</a>, on the site %3$s', 'buddyblog-pro' ),
					'position'                          => 100,
				)
			);
		}
	}


	/**
	 * Fix to disable/re-enable buddypress comment open/close filter.
	 */
	public function disable_bp_comment_filter() {

		if ( has_filter( 'comments_open', 'bp_comments_open' ) ) {
			remove_filter( 'comments_open', 'bp_comments_open', 10 );
		}
	}

	/**
	 * Re enable buddypress comments filter.
	 */
	public function enable_bp_comment_filter() {

		if ( function_exists( 'bp_comments_open' ) ) {
			add_filter( 'comments_open', 'bp_comments_open', 10, 2 );
		}
	}

	/**
	 * Fix comment form url for redirect.
	 *
	 * @param int $post_id post id.
	 *
	 * @return string
	 */
	public function fix_comment_form_redirect( $post_id ) {

		if ( ! bblpro_get_queried_post_id() ) {
			return;
		}

		$permalink = get_permalink( $post_id );

		?>
		<input type="hidden" name="redirect_to" value="<?php echo esc_url( $permalink ); ?>"/>

		<?php
	}

	/**
	 * Filter multipart params.
	 *
	 * @param array $settings params.
	 *
	 * @return array
	 */
	public function filter_uploader_settings( $settings ) {

		if ( ! bblpro_get_current_post_type() ) {
			return $settings;
		}

		if ( bblpro_is_edit_page() || bblpro_is_create_page() ) {
			$settings['bbl_context_type'] = bblpro_get_current_post_type();
		}

		return $settings;

	}

	/**
	 * Filter attachment for current user
	 *
	 * @param array $args args.
	 *
	 * @return array
	 */
	public function filter_ajax_attachment_args( $args ) {

		if ( ! empty( $_REQUEST['bbl_context_type'] ) && is_user_logged_in() ) {
			$args['author'] = get_current_user_id();
		}

		return $args;
	}

}
