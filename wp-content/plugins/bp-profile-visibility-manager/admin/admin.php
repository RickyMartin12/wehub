<?php
// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

// we use options buddy.
require_once dirname( __FILE__ ) . '/options-buddy/ob-loader.php';

/**
 * Admin settings.
 */
class BP_Profile_Visibility_Admin {

	/**
	 * Page object.
	 *
	 * @var OptionsBuddy_Settings_Page
	 */
	private $page;

	/**
	 * BP_Profile_Visibility_Admin constructor.
	 */
	public function __construct() {

		// create a options page.
		// make sure to read the code below.
		$this->page = new OptionsBuddy_Settings_Page( 'bp_profile_visibility_settings' );
		$this->page->set_bp_mode();// make it to use bp_get_option/bp_update_option.


		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_footer', array( $this, 'admin_css' ) );
	}

	/**
	 * Initialize.
	 */
	public function admin_init() {

		$page = $this->page;
		// add_section
		// you can pass section_id, section_title, section_description, the section id must be unique for this page, section descriptiopn is optional.
		$basic_section   = $page->add_section( 'basic_section', __( 'General Settings', 'bp-profile-visibility-manager' ), __( 'General settings for administrators.', 'bp-profile-visibility-manager' ) );
		$default_section = $page->add_section( 'default_section', __( 'Default Settings', 'bp-profile-visibility-manager' ), __( 'Default settings for new user accounts.', 'bp-profile-visibility-manager' ) );

		$basic_section->add_fields(
			array(
				array(
					'name'    => 'admin_only',
					'label'   => __( 'Only site admin can update user settings? ', 'bp-profile-visibility-manager' ),
					'desc'    => __( 'If you enable, users will not be able to change the preference.', 'bp-profile-visibility-manager' ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
						'no'  => __( 'No', 'bp-profile-visibility-manager' ),
					),

				),
				array(
					'name'    => 'enable_member_directory_visibility', // customization might have been a better name.
					'label'   => __( 'Enable Member directory visibility preference? ', 'bp-profile-visibility-manager' ),
					'desc'    => __( 'If you enable, users will be able to set preference if they want to visible in the listing or not?.', 'bp-profile-visibility-manager' ),
					'type'    => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
						'no'  => __( 'No', 'bp-profile-visibility-manager' ),
					),
				),
                array(
					'name'    => 'enable_member_search_visibility', // customization might have been a better name.
					'label'   => __( 'Enable Member search visibility preference? ', 'bp-profile-visibility-manager' ),
					'desc'    => __( 'If you enable, users will be able to set preference if they want to visible in the user search or not?.', 'bp-profile-visibility-manager' ),
					'type'    => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
						'no'  => __( 'No', 'bp-profile-visibility-manager' ),
					),
				),
				array(
					'name'    => 'enable_friend_request_visibility', // customization might have been a better name.
					'label'   => __( 'Enable Friendship Request preference? ', 'bp-profile-visibility-manager' ),
					'desc'    => __( 'If you enable, users will be able to set preference if they want to accept friendship requests or not.', 'bp-profile-visibility-manager' ),
					'type'    => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
						'no'  => __( 'No', 'bp-profile-visibility-manager' ),
					),
				),
				array(
					'name'    => 'enable_friends_list_visibility',
					'label'   => __( 'Enable Friends List visibility? ', 'bp-profile-visibility-manager' ),
					'desc'    => __( 'If you enable, users will be able to set preference for friends list visibility.', 'bp-profile-visibility-manager' ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
						'no'  => __( 'No', 'bp-profile-visibility-manager' ),
					),

				),
				array(
					'name'    => 'enable_profile_privacy_visibility',
					'label'   => __( 'Enable Profile privacy preference? ', 'bp-profile-visibility-manager' ),
					'desc'    => __( 'If you enable, users will be able to set preference for who can see their profile.', 'bp-profile-visibility-manager' ),
					'type'    => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
						'no'  => __( 'No', 'bp-profile-visibility-manager' ),
					),

				),

                array(
					'name'    => 'enable_last_activity_visibility',
					'label'   => __( 'Enable Last active time visibility? ', 'bp-profile-visibility-manager' ),
					'desc'    => __( 'If you enable, users will be able to set preference if they want their last active time to be visible or not.', 'bp-profile-visibility-manager' ),
					'type'    => 'radio',
					'default' => 'yes',
					'options' => array(
						'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
						'no'  => __( 'No', 'bp-profile-visibility-manager' ),
					),

				),
			)
		);




		if ( function_exists( 'bp_follow_start_following' ) ) {
			$basic_section->add_field(
				array(
					'name'    => 'enable_follow_request_visibility',
					'label'   => __( 'Enable Follow Button visibility? ', 'bp-profile-visibility-manager' ),
					'desc'    => __( 'If you enable, users will be able to set preference if they want to enable follow button or not.', 'bp-profile-visibility-manager' ),
					'type'    => 'radio',
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
						'no'  => __( 'No', 'bp-profile-visibility-manager' ),
					),

				)
			);
		}

		$basic_section->add_field(
			array(
				'name'    => 'enable_group_tab_visibility',
				'label'   => __( 'Enable group tab visibility?', 'bp-profile-visibility-manager' ),
				'desc'    => __( 'If you enable, users can change the visibility of the group tab on their profile.', 'bp-profile-visibility-manager' ),
				'type'    => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
					'no'  => __( 'No', 'bp-profile-visibility-manager' ),
				),
			)
		);

		$basic_section->add_field( array(
			'name'    => 'protected_profile_redirection_policy',
			'label'   => __( 'Protected Profile Policy', 'bp-profile-visibility-manager' ),
			'desc'    => __( 'Do you want to show the protected message on profile or redirect to the last visited page.', 'bp-profile-visibility-manager' ),
			'type'    => 'radio',
			'default' => 'referer',
			'options' => array(
				'referer' => __( 'Redirect to the last visited non protected page.', 'bp-profile-visibility-manager' ),
				'no'      => __( 'Show protected message on profile.', 'bp-profile-visibility-manager' ),
			),
		) );
		$basic_section->add_field(
			array(
				'name'    => 'do_not_hide_users_from_admin',
				'label'   => __( 'Do not hide users from site admin?', 'bp-profile-visibility-manager' ),
				'desc'    => __( 'If you enable, When a site admin is logged in to the site, they will see all members irrespective of users settings.', 'bp-profile-visibility-manager' ),
				'type'    => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
					'no'  => __( 'No', 'bp-profile-visibility-manager' ),
				),
			)
		);
		$basic_section->add_field(
			array(
				'name'    => 'enable_members_directory_privacy',
				'label'   => __( 'Restrict access to members directory?', 'bp-profile-visibility-manager' ),
				'desc'    => __( 'If you enable, You can select who can access/see members directory.', 'bp-profile-visibility-manager' ),
				'type'    => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
					'no'  => __( 'No', 'bp-profile-visibility-manager' ),
				),
			)
		);

		$basic_section->add_field(
			array(
				'name'    => 'members_directory_privacy',
				'label'   => __( 'Who can see members directory?', 'bp-profile-visibility-manager' ),
				'desc'    => __( 'If you have enabled members directory protection in previous option, you can choose the visibility.', 'bp-profile-visibility-manager' ),
				'type'    => 'radio',
				'default' => 'all',
				'options' => array(
					'all'      => __( 'Everyone', 'bp-profile-visibility-manager' ),
					'loggedin' => __( 'Only site members(logged in)', 'bp-profile-visibility-manager' ),
					'admin'    => __( 'Only site admins', 'bp-profile-visibility-manager' ),
				),
			)
		);
		// add fields.
		$default_section->add_fields( array( // remember, we registered basic section earlier.
			array(
				'name'    => 'bp_profile_visibility',
				'label'   => __( 'Default profile Visibility?', 'bp-profile-visibility-manager' ),
				// you already know it from previous example.
				'desc'    => __( 'Set default profile visibility for new users.', 'bp-profile-visibility-manager' ),
				// this is used as the description of the field.
				'type'    => 'select',
				'default' => 'public',
				'options' => bp_profile_visibility_get_visibilities(),
			),
			array(
				'name'    => 'bp_friends_list_visibility',
				'label'   => __( 'Default friends list  Visibility?', 'bp-profile-visibility-manager' ),
				// you already know it from previous example.
				'desc'    => __( 'Set default profile visibility for friends list.', 'bp-profile-visibility-manager' ),
				// this is used as the description of the field.
				'type'    => 'select',
				'default' => 'public',
				'options' => bp_profile_visibility_get_visibilities(),
			),

			array(
				'name'    => 'bp_profile_group_tab_visibility',
				'label'   => __( 'Default groups tab visibility on profile?', 'bp-profile-visibility-manager' ),
				// you already know it from previous example.
				'desc'    => __( 'Set default group tab visibility.', 'bp-profile-visibility-manager' ),
				// this is used as the description of the field.
				'type'    => 'select',
				'default' => 'public',
				'options' => bp_profile_visibility_get_visibilities(),
			),
			array(
				'name'    => 'bp_hide_last_active',
				'label'   => __( 'Hide Last Active?', 'bp-profile-visibility-manager' ),
				'desc'    => __( 'If you enable, It will not show when the user was last active.', 'bp-profile-visibility-manager' ),
				'type'    => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
					'no'  => __( 'No', 'bp-profile-visibility-manager' ),
				),
			),
			array(
				'name'    => 'bp_exclude_in_search',
				'label'   => __( 'Exclude Users from Members search?', 'bp-profile-visibility-manager' ),
				'desc'    => __( 'If you enable, It will hide users from search by default.', 'bp-profile-visibility-manager' ),
				'type'    => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
					'no'  => __( 'No', 'bp-profile-visibility-manager' ),
				),

			),
			array(
				'name'    => 'bp_exclude_in_dir',
				'label'   => __( 'Exclude Users from Members directory?', 'bp-profile-visibility-manager' ),
				'desc'    => __( 'If you enable, It will hide users from members directory when they register.', 'bp-profile-visibility-manager' ),
				'type'    => 'radio',
				'default' => 'no',
				'options' => array(
					'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
					'no'  => __( 'No', 'bp-profile-visibility-manager' ),
				),
			),
			array(
				'name'    => 'bp_allow_friendship_request',
				'label'   => __( 'Enable Friendship request by default?', 'bp-profile-visibility-manager' ),
				'desc'    => __( 'If you enable, users will receive new friends request.', 'bp-profile-visibility-manager' ),
				'type'    => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
					'no'  => __( 'No', 'bp-profile-visibility-manager' ),
				),
			),
		) );

		if ( function_exists( 'bp_follow_start_following' ) ) {

			$default_section->add_field( array(
				'name'    => 'bp_allow_follow_request',
				'label'   => __( 'Show follow button', 'bp-profile-visibility-manager' ),
				// you already know it from previous example.
				'desc'    => __( 'Disabling will hide the button for users', 'bp-profile-visibility-manager' ),
				// this is used as the description of the field.
				'type'    => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Yes', 'bp-profile-visibility-manager' ),
					'no'  => __( 'No', 'bp-profile-visibility-manager' ),
				),

			) );
		}

		do_action( 'bp_profile_visibility_settings', $page );
		$page->init();
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		add_options_page( __( 'BP Profile Visibility', 'bp-profile-visibility-manager' ), __( 'BP Profile Visibility Settings', 'bp-profile-visibility-manager' ), 'manage_options', 'bp-profile-visibility-manager', array(
			$this->page,
			'render',
		) );
	}

	/**
	 * Returns all the settings fields
	 */
	public function admin_css() {

		if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'bp-editable-activity' ) {
			return;
		}

		?>

        <style type="text/css">
            .wrap .form-table {
                margin: 10px;
            }
        </style>

		<?php
	}
}

new BP_Profile_Visibility_Admin();
