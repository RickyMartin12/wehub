<?php
/**
 * Views Handler
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
 * Posts vie handler.
 *
 * @property-read string $post_type post type.
 * @property-read string $action action.
 * @property-read int    $post_id post id in context.
 */
class View_Helper {

	/**
	 * Singleton instance.
	 *
	 * @var View_Helper
	 */
	private static $instance;

	/**
	 * Current post type.
	 *
	 * @var string
	 */
	private $post_type = null;

	/**
	 * Current action.
	 *
	 * @var string
	 */
	private $action = null;

	/**
	 * Post id in context(being edited/deleted).
	 *
	 * @var int
	 */
	private $post_id = 0;

	/**
	 * Get singleton instance.
	 *
	 * @return View_Helper
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
	}

	/**
	 * Process for current post type and action.
	 *
	 * @param string $post_type post type.
	 * @param string $action action.
	 * @param int    $action post id.
	 */
	public function process( $post_type, $action, $post_id = 0 ) {

		$this->post_type = $post_type;
		$this->action    = $action;
		$this->post_id   = $post_id;

		$this->handle();
		$this->render();
	}

	/**
	 * Handle
	 */
	private function handle() {
		do_action( 'bblpro_actions' );
	}

	/**
	 * Render view.
	 */
	private function render() {

		switch ( $this->action ) {

			case 'create':
				$this->render_create_screen();
				break;
			case 'draft':
				$this->render_drafts_screen();
				break;
			case 'edit':
				$this->render_edit_screen();
				break;
			case 'view':
				$this->render_single_screen();
				break;
			case 'published':
				$this->render_published_screen();
				break;
			case 'pending':
				$this->render_pending_screen();
				break;
			case 'list':
				$this->render_home_screen();
				break;
			default:
				$this->render_default();
				break;
		}
	}

	/**
	 * Render default screen
	 */
	private function render_default() {
		$this->prepare_attach_custom_tab_content();
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Post type home screen.
	 */
	private function render_home_screen() {
		add_action( 'bp_template_content', array( $this, 'content_home' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load home content.
	 */
	public function content_home() {
		bblpro_get_template_part( $this->post_type, 'posts' );
	}


	/**
	 * Post type create screen.
	 */
	public function render_create_screen() {
		add_action( 'bp_template_content', array( $this, 'content_create_screen' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load create content.
	 */
	public function content_create_screen() {
		bblpro_get_template_part( $this->post_type, 'create' );
	}

	/**
	 * Post type edit screen.
	 */
	public function render_edit_screen() {
		add_action( 'bp_template_content', array( $this, 'content_edit_screen' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load edit content
	 */
	public function content_edit_screen() {
		bblpro_get_template_part( $this->post_type, 'edit' );
	}

	/**
	 * Post type published screen.
	 */
	private function render_published_screen() {
		add_action( 'bp_template_content', array( $this, 'content_published' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load published content.
	 */
	public function content_published() {
		bblpro_get_template_part( $this->post_type, 'published' );
	}

	/**
	 * Post type pending posts.
	 */
	private function render_pending_screen() {
		add_action( 'bp_template_content', array( $this, 'content_pending' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Load Pending posts list.
	 */
	public function content_pending() {
		bblpro_get_template_part( $this->post_type, 'pending' );
	}

	/**
	 * Post type draft screen.
	 */
	private function render_drafts_screen() {
		add_action( 'bp_template_content', array( $this, 'content_drafts_screen' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Draft screen content.
	 */
	public function content_drafts_screen() {
		bblpro_get_template_part( $this->post_type, 'drafts' );
	}

	/**
	 * Post type single screen.
	 */
	private function render_single_screen() {
		add_action( 'bp_template_content', array( $this, 'content_single_screen' ) );
		bp_core_load_template( array( 'members/single/plugins' ) );
	}

	/**
	 * Single screen content
	 */
	public function content_single_screen() {
		bblpro_get_template_part( $this->post_type, 'single' );
	}

	/**
	 * Prepares and attaches custom tab content to the bp_template_content hook.
	 */
	private function prepare_attach_custom_tab_content() {

		// Grab custom content loaded by plugins.
		ob_start();
		do_action( 'bblpro_render_screen_' . $this->action );

		$content = ob_get_clean();
		// load.
		add_action(
			'bp_template_content',
			function () use ( $content ) {
				echo $content;
			}
		);
	}

	/**
	 * Magic method for accessing dynamic property.
	 *
	 * @param string $name property name.
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {
		return isset( $this->{$name} ) ? $this->{$name} : null;
	}

	/**
	 * Magic method for checking if property exists.
	 *
	 * @param string $name property name.
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return isset( $this->{$name} );
	}
}
