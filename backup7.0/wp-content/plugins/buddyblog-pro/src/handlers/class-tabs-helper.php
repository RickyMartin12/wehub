<?php
/**
 * Tabs Handler
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
 * Tabs manager.
 */
class Tabs_Helper {

	/**
	 * Singleton instance.
	 *
	 * @var Tabs_Helper
	 */
	private static $instance = null;

	/**
	 * Tab position.
	 *
	 * @var int
	 */
	private $tab_position = 100;

	private function __construct() {
	}

	/**
	 * Singleton factory method.
	 *
	 * @return Tabs_Helper
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Class self boot
	 *
	 * @return Tabs_Helper
	 */
	public static function boot() {
		return self::get_instance();
	}

	/**
	 * Register hooks.
	 */
	private function setup() {

		// using init instead of bp_setup_nav tto make sure post types registerd by others are available.
		add_action( 'init', array( $this, 'setup_nav' ), 200 );

		$admin_bar_action_priority = 0;

		if ( ! $admin_bar_action_priority ) {
			$admin_bar_action_priority = 99;
		}
		add_action( 'bp_setup_admin_bar', array( $this, 'setup_admin_bar' ), $admin_bar_action_priority );
	}

	/**
	 * Add tabs.
	 */
	public function setup_nav() {

		if ( ! function_exists( 'buddypress' ) ) {
			return;
		}

		// Determine user to use.
		if ( bp_displayed_user_domain() ) {
			$user_id     = bp_displayed_user_id();
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
			$user_id     = bp_loggedin_user_id();
		} else {
			return;
		}

		$access = bp_core_can_edit_settings();
		$args   = array(
			'access'      => $access,
			'user_domain' => $user_domain,
		);

		$post_types = bblpro_get_enabled_post_types();

		foreach ( $post_types as $post_type ) {
			$this->add_tabs( $user_id, $post_type, $args );
		}

		do_action( 'bblpro_post_types_tabs_added', $user_id, $args );
	}

	/**
	 * Add tabs for the give post type.
	 *
	 * @param int    $user_id user is.
	 * @param string $post_type post type.
	 * @param array  $args args.
	 */
	private function add_tabs( $user_id, $post_type, $args ) {

		$type_object = get_post_type_object( $post_type );

		if ( ! $type_object ) {
			return; // not a valid/registered post type.
		}

		$parent_slug = '';

		$visitor_id = get_current_user_id();

		// 1. should we add a top level tab for the post type
		if ( bblpro_is_root_tab_enabled( $post_type ) && bblpro_is_tab_available( $user_id, $post_type ) && bblpro_is_tab_visible( $user_id, $post_type, $visitor_id ) ) {
			$this->add_post_type_tab( $user_id, $post_type, $args );
		}

		// 1.1. should we add a sub nav for "List"
		if ( bblpro_is_action_enabled( $user_id, $post_type, 'list' ) && bblpro_is_action_visible( $user_id, $post_type, 'list', $visitor_id ) ) {
			$this->add_action_tab(
				$user_id,
				$post_type,
				'list',
				array(
					'label'       => bblpro_get_action_label( $post_type, 'list' ),
					'access'      => true,
					'user_domain' => $args['user_domain'],
					'position'    => 10,
				)
			);
		}

		// 1.2. should we add a sub nav for "Create"
		if ( bblpro_is_action_enabled( $user_id, $post_type, 'create' ) && bblpro_is_action_visible( $user_id, $post_type, 'create', $visitor_id ) ) {
			$this->add_action_tab(
				$user_id,
				$post_type,
				'create',
				array(
					'label'       => bblpro_get_action_label( $post_type, 'create', __( 'Add New', 'buddyblog-pro' ) ),
					'access'      => true,
					'user_domain' => $args['user_domain'],
					'position'    => 20,
				)
			);
		}
		// 1.3. should we add a sub nav for "Create"
		if ( bblpro_is_action_enabled( $user_id, $post_type, 'published' ) && bblpro_is_action_visible( $user_id, $post_type, 'published', $visitor_id ) ) {
			$this->add_action_tab(
				$user_id,
				$post_type,
				'published',
				array(
					'label'       => bblpro_get_action_label( $post_type, 'published', __( 'Published', 'buddyblog-pro' ) ),
					'access'      => true,
					'user_domain' => $args['user_domain'],
					'position'    => 30,
				)
			);
		}

		// 1.4. should we add a sub nav for "Create"
		if ( bblpro_is_action_enabled( $user_id, $post_type, 'pending' ) && bblpro_is_action_visible( $user_id, $post_type, 'pending', $visitor_id ) ) {
			$this->add_action_tab(
				$user_id,
				$post_type,
				'pending',
				array(
					'label'       => bblpro_get_action_label( $post_type, 'pending', __( 'Pending', 'buddyblog-pro' ) ),
					'access'      => true,
					'user_domain' => $args['user_domain'],
					'position'    => 40,
				)
			);
		}

		// 1.3. should we add a sub nav for "Drafts"
		if ( bblpro_is_action_enabled( $user_id, $post_type, 'draft' ) && bblpro_is_action_visible( $user_id, $post_type, 'draft', $visitor_id ) ) {
			$this->add_action_tab(
				$user_id,
				$post_type,
				'draft',
				array(
					'label'       => bblpro_get_action_label( $post_type, 'draft', __( 'Drafts', 'buddyblog-pro' ) ),
					'access'      => true,
					'user_domain' => $args['user_domain'],
					'position'    => 50,
				)
			);
		}

		$logged_id = get_current_user_id();
		// Action handlers.
		// Is editing enabled?
		if ( bblpro_is_post_editing_enabled( $logged_id, $post_type ) ) {
			add_action(
				'bp_actions',
				function () use ( $post_type ) {
					$edit_slug = bblpro_get_option( "{$post_type}_edit_tab_slug", 'edit' );

					if ( ! $edit_slug ) {
						$edit_slug = 'edit';
					}

					if ( bp_is_current_component( bblpro_get_tab_slug( $post_type ) ) && bp_is_current_action( $edit_slug ) && bblpro_user_can_edit_post( get_current_user_id(), bp_action_variable( 0 ) ) ) {
						View_Helper::instance()->process( $post_type, 'edit', bp_action_variable( 0)  );
					}
				}
			);
		}

		// Is deleting enabled?
		if ( bblpro_is_action_enabled( $user_id, $post_type, 'delete' ) && ( bp_is_my_profile() || bblpro_is_user_moderator( $logged_id, $post_type ) ) ) {
			add_action(
				'bp_actions',
				function () use ( $post_type ) {
					$delete_slug = bblpro_get_option( "{$post_type}_delete_tab_slug", 'delete' );

					if ( ! $delete_slug ) {
						$delete_slug = 'delete';
					}

					if ( bp_is_current_component( bblpro_get_tab_slug( $post_type ) ) && bp_is_current_action( $delete_slug ) ) {
						View_Helper::instance()->process( $post_type, 'delete' );
					}
				}
			);
		}

		// Single post on profile?
		if ( bblpro_is_action_enabled( $user_id, $post_type, 'view' ) ) {
			add_action(
				'bp_actions',
				function () use ( $post_type ) {
					$view_slug = bblpro_get_option( "{$post_type}_view_tab_slug", 'view' );
					if ( ! $view_slug ) {
						$view_slug = 'view';
					}
					if ( bp_is_current_component( bblpro_get_tab_slug( $post_type ) ) && bp_is_current_action( $view_slug ) ) {
						View_Helper::instance()->process( $post_type, 'view' );
					}
				}
			);
		}

		do_action( 'bblpro_post_type_tabs_added', $post_type, $user_id, $args );
	}

	/**
	 * Setup admin bar menu items.
	 *
	 * @todo Order sub menu based on tabs order. Also, check if the list tab is enabled.
	 */
	public function setup_admin_bar() {

		if ( ! is_admin_bar_showing() || ! is_user_logged_in() ) {
			return;
		}

		// Bail if this is an ajax request.
		if ( defined( 'DOING_AJAX' ) ) {
			return;
		}

		// Do not proceed if BP_USE_WP_ADMIN_BAR constant is not set or is false.
		if ( ! bp_use_wp_admin_bar() ) {
			return;
		}

		global $wp_admin_bar;
		$bp_my_account_id = buddypress()->my_account_menu_id;
		$user_id          = bp_loggedin_user_id();

		$post_types = bblpro_get_enabled_post_types();

		foreach ( $post_types as $post_type ) {
			// is admin bar enabled?
			if ( ! bblpro_is_admin_bar_enabled( $post_type ) ) {
				continue;
			}

			if ( ! bblpro_is_tab_available( $user_id, $post_type ) ) {
				continue;
			}

			$post_type_object = get_post_type_object( $post_type );
			if ( ! $post_type_object ) {
				continue;
			}

			$adminbar_id = sanitize_title_with_dashes( 'bblpro-' . $post_type );
			$wp_admin_bar->add_menu(
				array(
					'parent' => $bp_my_account_id,
					'id'     => $adminbar_id,
					'title'  => bblpro_get_tab_label( $post_type ),
					'href'   => bblpro_get_post_type_list_tab_url( $user_id, $post_type ),
				)
			);

			// List.
			$wp_admin_bar->add_menu(
				array(
					'parent' => $adminbar_id,
					'id'     => sanitize_title_with_dashes( $adminbar_id . '-list' ),
					'title'  => bblpro_get_action_label( $post_type, 'list' ),
					'href'   => bblpro_get_post_type_list_tab_url( $user_id, $post_type ),
				)
			);

			// Create.
			if ( bblpro_is_action_enabled( $user_id, $post_type, 'create' ) && bblpro_is_action_visible( $user_id, $post_type, 'create', $user_id ) ) {
				$wp_admin_bar->add_menu(
					array(
						'parent' => $adminbar_id,
						'id'     => sanitize_title_with_dashes( $adminbar_id . '-create' ),
						'title'  => bblpro_get_action_label( $post_type, 'create', __( 'Create', 'buddyblog-pro' ) ),
						'href'   => bblpro_get_post_type_create_tab_url( $user_id, $post_type ),
					)
				);
			}
			// Published.
			if ( bblpro_is_action_enabled( $user_id, $post_type, 'published' ) && bblpro_is_action_visible( $user_id, $post_type, 'published', $user_id ) ) {
				$wp_admin_bar->add_menu(
					array(
						'parent' => $adminbar_id,
						'id'     => sanitize_title_with_dashes( $adminbar_id . '-published' ),
						'title'  => bblpro_get_action_label( $post_type, 'published', __( 'Published', 'buddyblog-pro' ) ),
						'href'   => bblpro_get_post_type_published_tab_url( $user_id, $post_type ),
					)
				);
			}
			// Pending.
			if ( bblpro_is_action_enabled( $user_id, $post_type, 'pending' ) && bblpro_is_action_visible( $user_id, $post_type, 'pending', $user_id ) ) {
				$wp_admin_bar->add_menu(
					array(
						'parent' => $adminbar_id,
						'id'     => sanitize_title_with_dashes( $adminbar_id . '-pending' ),
						'title'  => bblpro_get_action_label( $post_type, 'pending', __( 'Pending', 'buddyblog-pro' ) ),
						'href'   => bblpro_get_post_type_pending_tab_url( $user_id, $post_type ),
					)
				);
			}

			// Drafts.
			if ( bblpro_is_action_enabled( $user_id, $post_type, 'draft' ) && bblpro_is_action_visible( $user_id, $post_type, 'draft', $user_id ) ) {
				$wp_admin_bar->add_menu(
					array(
						'parent' => $adminbar_id,
						'id'     => sanitize_title_with_dashes( $adminbar_id . '-draft' ),
						'title'  => bblpro_get_action_label( $post_type, 'draft', __( 'Drafts', 'buddyblog-pro' ) ),
						'href'   => bblpro_get_post_type_draft_tab_url( $user_id, $post_type ),
					)
				);
			}
		}
	}

	/**
	 * Add top level post type tab if enabled.
	 *
	 * @param int    $user_id context user id.
	 * @param string $post_type post type.
	 * @param array  $args args.
	 */
	private function add_post_type_tab( $user_id, $post_type, $args ) {

		$position = bblpro_get_option( $post_type . '_tab_position', 100 );

		if ( ! $position ) {
			$this->tab_position = $this->tab_position + 15;
		} else {
			$this->tab_position = $position;
		}

		$tab_label = trim( bblpro_get_tab_label( $post_type ) );
		// Only grab count if we're on a user page.
		if ( bp_is_user() ) {
			$post_status = bp_user_has_access() ? 'any' : 'publish';

			$count    = bblpro_get_user_posts_count( bp_displayed_user_id(), $post_type, $post_status );
			$class    = ( 0 === $count ) ? 'no-count' : 'count';
			$nav_name = $tab_label . sprintf( ' <span class="%s">%s</span>', esc_attr( $class ), bp_core_number_format( $count ) );
		} else {
			$nav_name = $tab_label;
		}

		bp_core_new_nav_item(
			array(
				'name'                    => $nav_name,
				'slug'                    => bblpro_get_tab_slug( $post_type ),
				'position'                => $this->tab_position,
				'screen_function'         => $this->get_view_callback( $post_type ),
				'default_subnav_slug'     => bblpro_get_default_sub_tab( $post_type ),
				'show_for_displayed_user' => bblpro_is_tab_visible( $user_id, $post_type, get_current_user_id() ),
			)
		);
	}

	/**
	 * Add action tab.
	 *
	 * @param int    $user_id user id.
	 * @param string $post_type post type.
	 * @param string $action action.
	 * @param array  $args args.
	 */
	public function add_action_tab( $user_id, $post_type, $action, $args ) {

		$label = bblpro_get_option( "{$post_type}_{$action}_tab_label", '' );

		if ( ! $label ) {
			$label = $args['label'];
		}

		$slug = bblpro_get_option( "{$post_type}_{$action}_tab_slug", $action );

		if ( ! $slug ) {
			$slug = $action;
		}

		$position = bblpro_get_option( "{$post_type}_{$action}_tab_position", 100 );

		if ( ! $position ) {
			$position = $args['position'];
		}

		$default_sub_nav    = bblpro_get_default_sub_tab( $post_type );
		$show_for_displayed = bblpro_is_tab_visible( $user_id, $post_type, get_current_user_id() );

		if ( bblpro_is_toplevel_tab( $post_type, $action ) ) {
			bp_core_new_nav_item(
				array(
					'name'                    => $label,
					'slug'                    => $slug,
					'position'                => $position,
					'screen_function'         => $this->get_view_callback( $post_type, $action ),
					'default_subnav_slug'     => $default_sub_nav,
					'show_for_displayed_user' => $show_for_displayed,
				)
			);

			return;
		}

		$parent_slug = bblpro_get_option( "{$post_type}_{$action}_tab_parent_slug", '' );

		if ( ! $parent_slug ) {
			$parent_slug = bblpro_get_tab_slug( $post_type );
		}

		$args = array(
			'name'            => $label,
			'slug'            => $slug,
			'parent_url'      => trailingslashit( $args['user_domain'] . $parent_slug ),
			'parent_slug'     => $parent_slug,
			'screen_function' => $this->get_view_callback( $post_type, $action ),
			'position'        => $position,
			'user_has_access' => $args['access'],
		);

		if ( 'create' == $action ) {
			$args['link'] = bblpro_get_post_type_create_tab_url( $user_id, $post_type );
		}

		bp_core_new_subnav_item( $args );
	}

	/**
	 * Get view callback.
	 *
	 * @param string $post_type post type.
	 * @param string $action action.
	 *
	 * @return callable
	 */
	private function get_view_callback( $post_type, $action = '' ) {

		static $callbacks = array();

		if ( ! isset( $callbacks[ $post_type ] ) ) {
			$callbacks[ $post_type ] = array();
		}

		if ( isset( $callbacks[ $post_type ][ $action ] ) ) {
			return $callbacks[ $post_type ][ $action ];
		}

		$callbacks[ $post_type ][ $action ] = function () use ( $post_type, $action ) {
			View_Helper::instance()->process( $post_type, $action );
		};

		return $callbacks[ $post_type ][ $action ];
	}
}
