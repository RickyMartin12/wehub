<?php
/**
 * Shortcodes PostType Helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Shortcodes
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */
namespace BuddyBlog_Pro\Shortcodes;

// Do not allow direct access over web.
use BuddyBlog_Pro\Admin\Metaboxes\BBL_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Create_Action_Settings_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Delete_Action_Settings_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Display_Shortcode_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Draft_Post_List_Settings_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Edit_Action_Settings_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Pending_Post_List_Settings_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Post_List_Settings_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Published_Post_List_Settings_Meta_Box;
use BuddyBlog_Pro\Shortcodes\Admin\Metaboxes\Upload_Action_Settings_Meta_Box;

defined( 'ABSPATH' ) || exit;

/**
 * Class Shortcode_Post_Type_Helper
 */
class Shortcode_Post_Type_Helper {

	/**
     * Shortcode post type.
     *
	 * @var string
	 */
	private  $post_type;

	/**
     * Meta boxes.
     *
	 * @var BBL_Meta_Box[]
	 */
    private $metaboxes = array();

    public function __construct() {

        $this->post_type = 'bblpro_shortcode';

	    $this->metaboxes['create_settings']              = new Create_Action_Settings_Meta_Box();
	    $this->metaboxes['edit_settings']                = new Edit_Action_Settings_Meta_Box();
	    $this->metaboxes['delete_settings']              = new Delete_Action_Settings_Meta_Box();
	    $this->metaboxes['upload_settings']              = new Upload_Action_Settings_Meta_Box();
	    $this->metaboxes['post_list_settings']           = new Post_List_Settings_Meta_Box();
	    //$this->metaboxes['published_post_list_settings'] = new Published_Post_List_Settings_Meta_Box();
	    $this->metaboxes['pending_post_list_settings']   = new Pending_Post_List_Settings_Meta_Box();
	    $this->metaboxes['draft_post_list_settings']     = new Draft_Post_List_Settings_Meta_Box();
	    $this->metaboxes['display_shortcode']     = new Display_Shortcode_Meta_Box();
    }

	/**
	 * Boot class
	 */
	public static function boot() {
		$self = new self();

		$self->setup();
	}

	/**
	 * Setup actions here.
	 */
	private function setup() {
		add_action( 'bp_init', array( $this, 'register_post_type' ), 11 );
		add_action( 'save_post_' . $this->post_type, array( $this, 'save' ) );
	}

	/**
	 * Register post type.
	 */
	public function register_post_type() {

		$is_admin = is_super_admin();

		register_post_type(
			$this->post_type,
			array(
				'label'                => __( 'BuddyBlog Shortcode', 'buddyblog-pro' ),
				'labels'               => array(
					'name'          => __( 'Shortcodes', 'buddyblog-pro' ),
					'singular_name' => __( 'Shortcode', 'buddyblog-pro' ),
					'menu_name'     => __( 'Shortcodes', 'buddyblog-pro' ),
					'all_items'     => __( 'Shortcodes', 'buddyblog-pro' ),
					'add_new_item'  => __( 'New Shortcode', 'buddyblog-pro' ),
					'new_item'      => __( 'New Shortcode', 'buddyblog-pro' ),
					'edit_item'     => __( 'Edit Shortcode', 'buddyblog-pro' ),
				),
				'public'               => false,
				'show_ui'              => $is_admin,
				'show_in_menu'         => 'edit.php?post_type=buddyblogpro_form',
				'supports'             => array( 'title' ),
				'register_meta_box_cb' => array( $this, 'add_meta_boxes' ),
			)
		);
	}

	/**
	 * Add metabox for other information
	 *
	 * @param \WP_Post $post Post object.
	 */
	public function add_meta_boxes( $post ) {

		$post_type                          = $this->post_type;

		add_meta_box(
			'bbl_post_create',
			__( 'Post Creation', 'buddyblog-pro' ),
			array( $this->metaboxes['create_settings'], 'render' ),
			$post_type,
			'advanced',
			'default',
			$post
		);

		add_meta_box(
			'bbl_post_edit',
			__( 'Editing', 'buddyblog-pro' ),
			array( $this->metaboxes['edit_settings'], 'render' ),
			$post_type,
			'advanced',
			'default',
			$post
		);

		add_meta_box(
			'bbl_post_delete',
			__( 'Deletion', 'buddyblog-pro' ),
			array( $this->metaboxes['delete_settings'], 'render' ),
			$post_type,
			'advanced',
			'default',
			$post
		);

		add_meta_box(
			'bbl_upload_media',
			__( 'Uploads', 'buddyblog-pro' ),
			array( $this->metaboxes['upload_settings'], 'render' ),
			$post_type,
			'advanced',
			'default',
			$post
		);

		/*
		add_meta_box(
			'bbl_posts_list',
			__( 'Posts List', 'buddyblog-pro' ),
				array( $this->metaboxes['post_list_settings'], 'render' ),
			$post_type,
			'advanced',
			'default',
			$post
		);

		add_meta_box(
			'bbl_posts_published',
			__( 'Posts Published', 'buddyblog-pro' ),
			array( $this->metaboxes['post_list_settings'], 'render' ),
			$post_type,
			'advanced',
			'default',
			$post
		);
        */
		add_meta_box(
			'bbl_posts_pending',
			__( 'Posts Pending', 'buddyblog-pro' ),
			array( $this->metaboxes['pending_post_list_settings'], 'render' ),
			$post_type,
			'advanced',
			'default',
			$post
		);

		add_meta_box(
			'bbl_posts_draft',
			__( 'Posts Draft', 'buddyblog-pro' ),
			array( $this->metaboxes['draft_post_list_settings'], 'render' ),
			$post_type,
			'advanced',
			'default',
			$post
		);
		add_meta_box(
			'bbl_shortcode_display_code',
			__( 'Shortcode', 'buddyblog-pro' ),
			array( $this->metaboxes['display_shortcode'], 'render' ),
			$post_type,
			'side',
			'default',
			$post
		);
	}

	/**
	 * Save details
	 *
	 * @param int $post_id Post id.
	 */
	public function save( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$post = get_post( $post_id );

		// Not our shortcode.
		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['_buddyblog-pro-shortcode-admin-nonce'] ) ) {
			return;
		}

		// verify nonce.
		if ( ! wp_verify_nonce( wp_unslash( $_POST['_buddyblog-pro-shortcode-admin-nonce'] ), 'buddyblog-pro-shortcode-admin-nonce' ) ) {
			return;
		}

		// Delegate saving.
		foreach ( $this->metaboxes as $metabox ) {
			$metabox->save( $post );
		}
	}

}
