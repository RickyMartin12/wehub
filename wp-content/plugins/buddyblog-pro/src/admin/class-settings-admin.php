<?php
/**
 * BuddyBlog Pro Settings Helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin;

use Press_Themes\PT_Settings\Page;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * BuddyBlog settings admin
 */
class Settings_Admin {

	/**
	 * Admin Menu slug
	 *
	 * @var string
	 */
	private $menu_slug;

	/**
	 * Used to keep a reference of the Page, It will be used in rendering the view.
	 *
	 * @var Page
	 */
	private $page;

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Register actions.
	 */
	private function setup() {
		$this->menu_slug = 'buddyblog-pro';
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_head', array( $this, 'custom_css' ) );
	}

	/**
	 * Add Menu
	 */
	public function add_menu() {

		add_submenu_page(
			'edit.php?post_type=' . bblpro_get_form_post_type(),
			_x( 'Settings', 'Admin settings page title', 'buddyblog-pro' ),
			_x( 'Settings', 'Admin settings menu label', 'buddyblog-pro' ),
			'manage_options',
			$this->menu_slug,
			array( $this, 'render' )
		);
	}

	/**
	 * Initialize the admin settings panel and fields
	 */
	public function init() {

		if ( ! $this->needs_loading() ) {
			return;
		}

		$page = new Page( 'buddyblog-pro', __( 'BuddyBlog', 'buddyblog-pro' ) );
		$this->register_settings( $page );
		$this->page = $page;
		$this->page->init();
	}

	/**
	 * Show/render the setting page
	 */
	public function render() {
		$this->page->render();
	}

	/**
	 * Register settings.
	 *
	 * @param Page $page page object.
	 */
	public function register_settings( $page ) {
		// find all post types.
		$post_types = bblpro_get_available_post_types();

		// create post type as options array.
		$post_type_options = array();
		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			if ( ! $post_type_object ) {
				continue;
			}

			$post_type_options[ $post_type ] = $post_type_object->labels->singular_name;
		}

		unset( $post_type_options['attachment'] );

		$this->add_general_panel( $page, $post_type_options );

		unset( $post_type );
		// Let us add panel for each enabled post type.
		$enabled_post_types = bblpro_get_enabled_post_types();
		foreach ( $enabled_post_types as $post_type ) {
			$this->add_post_type_panel( $page, $post_type );
		}
	}

	/**
	 * Add general settings panel.
	 *
	 * @param Page  $page page object.
	 * @param array $post_type_options post types.
	 */
	private function add_general_panel( $page, $post_type_options ) {

		$panel   = $page->add_panel( 'general', __( 'General', 'buddyblog-pro' ) );
		$section = $panel->add_section( 'general_enabled_post_types', __( 'Profile Tabs', 'buddyblog-pro' ), __( 'Choose which post types should appear as profile tabs.', 'buddyblog-pro' ) );
		$section->add_field(
			array(
				'id'      => 'enabled_post_types',
				'name'    => 'enabled_post_types',
				'label'   => __( 'Enable Tabs for', 'buddyblog-pro' ),
				'desc'    => __( 'Once you save the selection, you can configure tab for individual post types', 'buddyblog-pro' ),
				'default' => array(),
				'type'    => 'multicheck',
				'options' => $post_type_options,
			)
		);
	}

	/**
	 * Add panel for the post type.
	 *
	 * @param Page   $page Page object.
	 * @param string $post_type post type name.
	 */
	private function add_post_type_panel( $page, $post_type ) {

		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object ) {
			return;
		}

		$roles = wp_roles()->roles;

		$user_roles        = array();
		$user_roles['all'] = __( 'All Members', 'buddyblog-pro' );

		foreach ( $roles as $role => $detail ) {
			$user_roles[ $role ] = $detail['name'];
		}

		$who_can_view_roles = $user_roles;
		unset( $who_can_view_roles['all'] );

		$extra_visible_options = array(
			'all'       => __( 'Anyone', 'buddyblog-pro' ),
			'logged_in' => __( 'Logged In Members', 'buddyblog-pro' ),
			'self'      => __( 'Profile Owner', 'buddyblog-pro' ),
		);

		if ( bp_is_active( 'friends' ) ) {
			$extra_visible_options['friends'] = __( 'Friends', 'buddyblog-pro' );
		}

		if ( function_exists( 'bp_follow_get_followers' ) ) {
			$extra_visible_options['followers'] = __( 'Followers', 'buddyblog-pro' );
			$extra_visible_options['following'] = __( 'Leaders(whom the user is following)', 'buddyblog-pro' );
		}

		$visible_roles    = array_merge( $extra_visible_options, $who_can_view_roles );
		$enable_for_roles = $user_roles;

		$label = $post_type_object->labels->singular_name;

		/* translators: %s post type name */
		$pt_panel = $page->add_panel( 'post_type_' . $post_type, $label, sprintf( __( 'User profile tab settings for %s', 'buddyblog-pro' ), $label ) );
		$section  = $pt_panel->add_section( $post_type . '-tab-settings', __( 'Tab Settings', 'buddyblog-pro' ) );

		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => $post_type . '_root_tab_enable',
					'label'   => __( 'Add a top level nav?', 'buddyblog-pro' ),
					'desc'    => __( "Add a top level menu item in user's profile menu.", 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_tab_label',
					'label'   => __( 'Tab label', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item label. If not provided, post type plural name will be used.', 'buddyblog-pro' ),
					'default' => '',
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_tab_slug',
					'label'   => __( 'Tab slug', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item slug. If not provided, post type will be used.', 'buddyblog-pro' ),
					'default' => '',
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_tab_position',
					'label'   => __( 'Tab order', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item order. Please enter a number.', 'buddyblog-pro' ),
					'default' => 45,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_tab_available_roles',
					'label'   => __( 'Enable tab for', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be available on the profile of these users.', 'buddyblog-pro' ),
					'default' => array( 'all' => 'all' ),
					'options' => $enable_for_roles,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_tab_visible_roles',
					'label'   => __( 'Tab is visible to', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be visible to these users when they visit a profile.', 'buddyblog-pro' ),
					'default' => array( 'all' => 'all' ),
					'options' => $visible_roles,
				),
				array(
					'type'    => 'radio',
					'name'    => $post_type . '_admin_bar_enabled',
					'label'   => __( 'Add admin bar nav?', 'buddyblog-pro' ),
					'desc'    => __( 'Add to My Account dropdown in admin bar.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
			)
		);
		// List.
		$section = $pt_panel->add_section( $post_type . '-list-tab-settings', __( 'Post Lists', 'buddyblog-pro' ) );
		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_list_enabled",
					'label'   => __( 'Show posts list?', 'buddyblog-pro' ),
					'desc'    => __( 'Show entries on profile.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				/*array(
					'type'    => 'text',
					'name'    => $post_type . '_list_tab_parent_slug',
					'label'   => __( 'Parent tab slug', 'buddyblog-pro' ),
					'desc'    => __( "Optional.By default it will appear under the post type's tab.", 'buddyblog-pro' ),
					'default' => '',
				),*/
				array(
					'type'    => 'text',
					'name'    => $post_type . '_list_tab_label',
					'label'   => __( 'Tab label', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item label. If not provided, post type plural name will be used.', 'buddyblog-pro' ),
					'default' => '',
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_list_tab_slug',
					'label'   => __( 'Tab slug', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item slug. If not provided, post type will be used.', 'buddyblog-pro' ),
					'default' => '',
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_list_tab_position',
					'label'   => __( 'Tab order', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item order. Please enter a number.', 'buddyblog-pro' ),
					'default' => 10,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_list_tab_available_roles',
					'label'   => __( 'Enable tab for', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be available on the profile of these users.', 'buddyblog-pro' ),
					'default' => array( 'all' => 'all' ),
					'options' => $enable_for_roles,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_list_tab_visible_roles',
					'label'   => __( 'Tab is visible to', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be visible to these users when they visit a profile.', 'buddyblog-pro' ),
					'default' => array( 'all' => 'all' ),
					'options' => $visible_roles,
				),
			)
		);

		$section = $pt_panel->add_section( $post_type . '-create-tab-settings', __( 'Post Creation', 'buddyblog-pro' ) );

		$posts = get_posts(
			array(
				'post_type'   => bblpro_get_form_post_type(),
				'per_page'    => - 1,
				'post_status' => 'publish',
				'meta_key'    => '_buddyblog_post_type',
				'meta_value'  => $post_type,
			)
		);

		$form = array(
			'' => __( 'Select a form', 'buddyblog-pro' ),
		);

		foreach ( $posts as $post ) {
			$form[ $post->ID ] = $post->post_title;
		}

		$shortcode_code = __( "You can use the shortcode <code>[bbl-create-by-post-type post_type='{$post_type}']</code> in your selected page(not needed if you have not selected a page)", 'buddyblog-pro' );

		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_create_enabled",
					'label'   => __( 'Allow creating post?', 'buddyblog-pro' ),
					'desc'    => __( 'Allow users to create posts from profile.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				/*array(
					'type'    => 'text',
					'name'    => $post_type . '_create_tab_parent_slug',
					'label'   => __( 'Parent tab slug', 'buddyblog-pro' ),
					'desc'    => __( "Optional.By default it will appear under the post type's tab.", 'buddyblog-pro' ),
					'default' => '',
				),*/
				array(
					'type'    => 'text',
					'name'    => $post_type . '_create_tab_label',
					'label'   => __( 'Tab label', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item label. If not provided, post type plural name will be used.', 'buddyblog-pro' ),
					'default' => __( 'Create', 'buddyblog-pro' ),
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_create_tab_slug',
					'label'   => __( 'Tab slug', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item slug. If not provided, post type will be used.', 'buddyblog-pro' ),
					'default' => 'create',
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_create_tab_position',
					'label'   => __( 'Tab order', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item order. Please enter a number.', 'buddyblog-pro' ),
					'default' => 20,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_create_tab_available_roles',
					'label'   => __( 'Enable tab for', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be available on the profile of these users.', 'buddyblog-pro' ),
					'default' => array( 'self' => 'self' ),
					'options' => $enable_for_roles,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_create_tab_visible_roles',
					'label'   => __( 'Tab is visible to', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be visible to these users when they visit a profile.', 'buddyblog-pro' ),
					'default' => array( 'self' => 'self' ),
					'options' => $visible_roles,
				),
				array(
					'type'    => 'select',
					'name'    => $post_type . '_form_id',
					'label'   => __( 'Post Form', 'buddyblog-pro' ),
					'default' => '',
					'options' => $form,
					'desc'    => __( ' Please select a form that will be used for creating/editing content. If there are no forms, Please create a form from BuddyBlog->Forms screen.', 'buddyblog-pro' ),
				),

				array(
					'type'    => 'pages_dropdown',
					'name'    => $post_type . '_create_page_id',
					'label'   => __( 'Create page', 'buddyblog-pro' ),
					'default' => 0,
					'desc'    => __( "If you want to use a dedicated page for your users to create/edit post, Please select the page. If you don't select a page, the form will be shown under user profile tab", 'buddyblog-pro' ),
				),
				array(
					'type'    => 'html',
					'name'    => $post_type . '_shortcode_code',
					'label'   => __( 'Shortcode', 'buddyblog-pro' ),
					'default' => '',
					'desc'    => $shortcode_code,
				),
			)
		);
		// Editing.
		$section = $pt_panel->add_section( $post_type . '-edit-tab-settings', __( 'Editing', 'buddyblog-pro' ) );
		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_edit_enabled",
					'label'   => __( 'Allow users to edit their posts?', 'buddyblog-pro' ),
					'desc'    => __( 'Let users edit their content after publishing.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_edit_tab_available_roles',
					'label'   => __( 'Enable Edit for', 'buddyblog-pro' ),
					'desc'    => __( 'These users will be able to edit.', 'buddyblog-pro' ),
					'default' => array( 'all' => 'all' ),
					'options' => $enable_for_roles,
				),
			)
		);

		$section = $pt_panel->add_section( $post_type . '-uploads-tab-settings', __( 'Uploads', 'buddyblog-pro' ) );
		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_upload_enabled",
					'label'   => __( 'Allow Uploading media?', 'buddyblog-pro' ),
					'desc'    => __( 'Allow users to upload images etc to their posts.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_upload_tab_available_roles',
					'label'   => __( 'Enable Upload for', 'buddyblog-pro' ),
					'desc'    => __( 'These users will be able to upload.', 'buddyblog-pro' ),
					'default' => array( 'all' => 'all' ),
					'options' => $enable_for_roles,
				),
			)
		);
		$section = $pt_panel->add_section( $post_type . '-published-tab-settings', __( 'Published', 'buddyblog-pro' ) );
		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_published_enabled",
					'label'   => __( 'Add Published tab?', 'buddyblog-pro' ),
					'desc'    => __( 'Allow users to have a tab with published posts.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				/* array(
					'type'    => 'text',
					'name'    => $post_type . '_published_tab_parent_slug',
					'label'   => __( 'Parent tab slug', 'buddyblog-pro' ),
					'desc'    => __( "Optional.By default it will appear under the post type's tab.", 'buddyblog-pro' ),
					'default' => '',
				),*/
				array(
					'type'    => 'text',
					'name'    => $post_type . '_published_tab_label',
					'label'   => __( 'Tab label', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item label. If not provided, Published will be used.', 'buddyblog-pro' ),
					'default' => __( 'Published', 'buddyblog-pro' ),
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_published_tab_slug',
					'label'   => __( 'Tab slug', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item slug. If not provided, "published" will be used.', 'buddyblog-pro' ),
					'default' => 'published',
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_published_tab_position',
					'label'   => __( 'Tab order', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item order. Please enter a number.', 'buddyblog-pro' ),
					'default' => 30,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_published_tab_available_roles',
					'label'   => __( 'Enable tab for', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be available on the profile of these users.', 'buddyblog-pro' ),
					'default' => array( 'self' => 'self' ),
					'options' => $enable_for_roles,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_published_tab_visible_roles',
					'label'   => __( 'Tab is visible to', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be visible to these users when they visit a profile.', 'buddyblog-pro' ),
					'default' => array( 'self' => 'self' ),
					'options' => $visible_roles,
				),
			)
		);

		$section = $pt_panel->add_section( $post_type . '-pending-tab-settings', __( 'Pending', 'buddyblog-pro' ) );
		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_pending_enabled",
					'label'   => __( 'Add Pending tab?', 'buddyblog-pro' ),
					'desc'    => __( 'Allow users to have a tab with pending posts.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				/* array(
					'type'    => 'text',
					'name'    => $post_type . '_pending_tab_parent_slug',
					'label'   => __( 'Parent tab slug', 'buddyblog-pro' ),
					'desc'    => __( "Optional.By default it will appear under the post type's tab.", 'buddyblog-pro' ),
					'default' => '',
				),*/
				array(
					'type'    => 'text',
					'name'    => $post_type . '_pending_tab_label',
					'label'   => __( 'Tab label', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item label. If not provided, Pending will be used.', 'buddyblog-pro' ),
					'default' => __( 'Pending', 'buddyblog-pro' ),
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_pending_tab_slug',
					'label'   => __( 'Tab slug', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item slug. If not provided, "pending" will be used.', 'buddyblog-pro' ),
					'default' => 'pending',
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_pending_tab_position',
					'label'   => __( 'Tab order', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item order. Please enter a number.', 'buddyblog-pro' ),
					'default' => 40,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_pending_tab_available_roles',
					'label'   => __( 'Enable tab for', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be available on the profile of these users.', 'buddyblog-pro' ),
					'default' => array( 'self' => 'self' ),
					'options' => $enable_for_roles,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_pending_tab_visible_roles',
					'label'   => __( 'Tab is visible to', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be visible to these users when they visit a profile.', 'buddyblog-pro' ),
					'default' => array( 'self' => 'self' ),
					'options' => $visible_roles,
				),
			)
		);

		$section = $pt_panel->add_section( $post_type . '-drafts-tab-settings', __( 'Drafts', 'buddyblog-pro' ) );
		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_draft_enabled",
					'label'   => __( 'Allow saving drafts?', 'buddyblog-pro' ),
					'desc'    => __( 'Allow users to save drafts of the post.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				/* array(
					'type'    => 'text',
					'name'    => $post_type . '_draft_tab_parent_slug',
					'label'   => __( 'Parent tab slug', 'buddyblog-pro' ),
					'desc'    => __( "Optional.By default it will appear under the post type's tab.", 'buddyblog-pro' ),
					'default' => '',
				),*/
				array(
					'type'    => 'text',
					'name'    => $post_type . '_draft_tab_label',
					'label'   => __( 'Tab label', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item label. If not provided, post type plural name will be used.', 'buddyblog-pro' ),
					'default' => __( 'Drafts', 'buddyblog-pro' ),
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_draft_tab_slug',
					'label'   => __( 'Tab slug', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item slug. If not provided, post type will be used.', 'buddyblog-pro' ),
					'default' => 'drafts',
				),
				array(
					'type'    => 'text',
					'name'    => $post_type . '_draft_tab_position',
					'label'   => __( 'Tab order', 'buddyblog-pro' ),
					'desc'    => __( 'User profile tab menu item order. Please enter a number.', 'buddyblog-pro' ),
					'default' => 50,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_draft_tab_available_roles',
					'label'   => __( 'Enable tab for', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be available on the profile of these users.', 'buddyblog-pro' ),
					'default' => array( 'self' => 'self' ),
					'options' => $enable_for_roles,
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_draft_tab_visible_roles',
					'label'   => __( 'Tab is visible to', 'buddyblog-pro' ),
					'desc'    => __( 'Tab will be visible to these users when they visit a profile.', 'buddyblog-pro' ),
					'default' => array( 'self' => 'self' ),
					'options' => $visible_roles,
				),
			)
		);

		$section = $pt_panel->add_section( $post_type . '-delete-tab-settings', __( 'Deletion', 'buddyblog-pro' ) );
		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_delete_enabled",
					'label'   => __( 'Allow users to delete their posts?', 'buddyblog-pro' ),
					'desc'    => __( 'Let users delete their content after publishing.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_delete_enable_confirmation",
					'label'   => __( 'Ask users to confirm before deleting the post?', 'buddyblog-pro' ),
					'desc'    => __( 'It will show a confirmation box. Prevents accidental post deletion.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_delete_tab_available_roles',
					'label'   => __( 'Enable Deletion for', 'buddyblog-pro' ),
					'desc'    => __( 'These users will be able to delete their posts.', 'buddyblog-pro' ),
					'default' => array( 'all' => 'all' ),
					'options' => $enable_for_roles,
				),
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_on_delete_keep_in_trash",
					'label'   => __( 'On Delete action?', 'buddyblog-pro' ),
					'desc'    => __( 'what should we do on post delete?', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Move to trash', 'buddyblog-pro' ),
						0 => __( 'Delete permanently', 'buddyblog-pro' ),
					),
				),
			)
		);

		// content settings.
		$section = $pt_panel->add_section( $post_type . '-misc-settings', __( 'Misc Settings', 'buddyblog-pro' ) );

		$section->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_show_front_posts_only",
					'label'   => __( 'Should tab post lists show only the posts created by BuddyBlog(front end)?', 'buddyblog-pro' ),
					'desc'    => __( 'Limit the list of posts in all, published, pending tabs to the posts created from BuddyBlog only.', 'buddyblog-pro' ),
					'default' => 0,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				array(
					'type'    => 'radio',
					'name'    => "{$post_type}_action_view_enabled",
					'label'   => __( 'Make single post viewable on profile?', 'buddyblog-pro' ),
					'desc'    => __( 'It will make the posts from user to be visible on http://example.com/members/membername/post_tab_slug/view/id', 'buddyblog-pro' ),
					'default' => 0,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
				array(
					'type'    => 'multicheck',
					'name'    => $post_type . '_view_tab_available_roles',
					'label'   => __( 'Enable Single post viewable on profile for', 'buddyblog-pro' ),
					'desc'    => __( 'It will make the posts from user to be visible on http://example.com/members/membername/post_tab_slug/view/id.', 'buddyblog-pro' ),
					'default' => array( 'all' => 'all' ),
					'options' => $enable_for_roles,
				),
			)
		);

		// content settings.
		$section_activity = $pt_panel->add_section( $post_type . '-activity-settings', __( 'Activity Integration', 'buddyblog-pro' ) );

		$section_activity->add_fields(
			array(
				array(
					'type'    => 'radio',
					'name'    => $post_type . '_enable_activity',
					'label'   => __( 'Enable activity recording', 'buddyblog-pro' ),
					'desc'    => __( 'Enable recording activity when an entry is published.', 'buddyblog-pro' ),
					'default' => 1,
					'options' => array(
						1 => __( 'Yes', 'buddyblog-pro' ),
						0 => __( 'No', 'buddyblog-pro' ),
					),
				),
			)
		);

		do_action( 'bblpro_settings_post_type_panel', $pt_panel, $post_type );
	}

	/**
	 * Is it the setting page?
	 *
	 * @return bool
	 */
	private function needs_loading() {

		global $pagenow;

		// We need to load on options.php otherwise settings won't be reistered.
		if ( 'options.php' === $pagenow ) {
			return true;
		}

		if ( isset( $_GET['page'] ) && $_GET['page'] === $this->menu_slug ) {
			return true;
		}

		return false;
	}

	/**
	 * Hides Page create button for now.
	 */
	public function custom_css() {
		if ( ! $this->needs_loading() ) {
			return;
		}
		?>
		<style type="text/css">
            .pt-settings-create-page-button {
                display: none !important;
            }
		</style>
		<?php
	}
}
