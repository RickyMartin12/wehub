<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Lock
 * @subpackage Bp_Lock/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bp_Lock
 * @subpackage Bp_Lock/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Bp_Lock_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function bplock_enqueue_styles() {
		if ( stripos( $_SERVER['REQUEST_URI'], $this->plugin_name ) ) {
			wp_enqueue_style( $this->plugin_name . '-font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css' );
			wp_enqueue_style( $this->plugin_name . '-selectize-css', plugin_dir_url( __FILE__ ) . 'css/selectize.css' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-lock-admin.css' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function bplock_enqueue_scripts() {
		if ( stripos( $_SERVER['REQUEST_URI'], $this->plugin_name ) ) {
			wp_enqueue_script( $this->plugin_name . '-selectize', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-lock-admin.js', array( 'jquery' ) );
			wp_localize_script(
				$this->plugin_name,
				'bplock_admin_js_object',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				)
			);
		}

	}

	/**
	 * Actions performed to save general settings of the plugin
	 */
	public function bplock_save_general_settings() {
		$bplock_general_settings = array();

		$lock_bp_components = 'no';
		if ( isset( $_POST['bplock-bp-components'] ) ) {
			$lock_bp_components = 'yes';
		}

		$lock_cpt = 'no';
		if ( isset( $_POST['bplock-custom-post-types'] ) ) {
			$lock_cpt = 'yes';
		}

		$lock_wp_pages = 'no';
		if ( isset( $_POST['bplock-pages'] ) ) {
			$lock_wp_pages = 'yes';
		}

		$lr_form = 'plugin_form';
		if ( isset( $_POST['lr-form'] ) ) {
			$lr_form = $_POST['lr-form'];
		}

		$custom_form_content = '';
		if ( isset( $_POST['custom_form_content'] ) ) {
			$custom_form_content = $_POST['custom_form_content'];
		}

		$bplock_general_settings                       = get_option( 'bplock_general_settings' );
		$bplock_general_settings['lock_bp_components'] = $lock_bp_components;
		$bplock_general_settings['lock_cpt']           = $lock_cpt;
		$bplock_general_settings['lock_wp_pages']      = $lock_wp_pages;
		$bplock_general_settings['locked_content']     = stripslashes( wp_filter_post_kses( $_POST['bplock-locked-content'] ) );

		$bplock_general_settings['lr_form']             = $lr_form;
		$bplock_general_settings['custom_form_content'] = $custom_form_content;

		update_option( 'bplock_general_settings', $bplock_general_settings );
		$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
		$success_msg .= '<p>' . esc_html__( 'Settings Saved.', 'bp-lock' ) . '</p>';
		$success_msg .= '</div>';
		echo wp_kses_post( $success_msg );
	}

	/**
	 * Actions performed to save cpt settings of the plugin.
	 */
	public function bplock_save_cpt_settings() {
		$locked_cpts = array();
		if ( isset( $_POST['bplock-cpt-list'] ) ) {
			$locked_cpts = wp_unslash( $_POST['bplock-cpt-list'] );
		}
		$bplock_general_settings                = get_option( 'bplock_general_settings' );
		$bplock_general_settings['locked_cpts'] = $locked_cpts;
		update_option( 'bplock_general_settings', $bplock_general_settings );
		$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
		$success_msg .= '<p>' . esc_html__( 'Settings Saved.', 'bp-lock' ) . '</p>';
		$success_msg .= '</div>';
		echo wp_kses_post( $success_msg );
	}

	/**
	 * Actions performed to save wp pages settings of the plugin
	 */
	public function bplock_save_pages_settings() {
		$locked_pages = array();
		if ( isset( $_POST['bplock-pages-list'] ) ) {
			$locked_pages = wp_unslash( $_POST['bplock-pages-list'] );
		}
		$bplock_general_settings                 = get_option( 'bplock_general_settings' );
		$bplock_general_settings['locked_pages'] = $locked_pages;
		update_option( 'bplock_general_settings', $bplock_general_settings );
		$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
		$success_msg .= '<p>' . esc_html__( 'Settings Saved.', 'bp-lock' ) . '</p>';
		$success_msg .= '</div>';
		echo wp_kses_post( $success_msg );
	}

	/**
	 * Actions performed to save BuddyPress components settings of the plugin
	 */
	public function bplock_save_bp_components_settings() {
		$locked_bp_components = array();
		if ( isset( $_POST['bplock-bp-components-list'] ) ) {
			$locked_bp_components = wp_unslash( $_POST['bplock-bp-components-list'] );
		}
		$bplock_general_settings                         = get_option( 'bplock_general_settings' );
		$bplock_general_settings['locked_bp_components'] = $locked_bp_components;
		update_option( 'bplock_general_settings', $bplock_general_settings );
		$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
		$success_msg .= '<p>' . esc_html__( 'Settings Saved.', 'bp-lock' ) . '</p>';
		$success_msg .= '</div>';
		echo wp_kses_post( $success_msg );
	}

	/**
	 * Actions performed to add an admin options page
	 */
	public function bplock_add_options_page() {
		// add_options_page( esc_html__( 'BuddyPress Lock Settings', 'bp-lock' ), esc_html__( 'BuddyPress Lock', 'bp-lock' ), 'manage_options', $this->plugin_name, array( $this, 'bplock_options_page_content' ) );

		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'bp-lock' ), esc_html__( 'WB Plugins', 'bp-lock' ), 'manage_options', 'wbcomplugins', array( $this, 'bplock_options_page_content' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'bp-lock' ), esc_html__( 'General', 'bp-lock' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Lock Settings', 'bp-lock' ), esc_html__( 'BP Private Community', 'bp-lock' ), 'manage_options', 'bp-lock', array( $this, 'bplock_options_page_content' ) );
	}

	/**
	 * Actions performed to create a options page content
	 */
	public function bplock_options_page_content() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome';;
		?>
		<div class="wrap">
                    <hr class="wp-header-end">
                    <div class="wbcom-wrap">
			<div class="blpro-header">
				<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
					<?php esc_html_e( 'BuddyPress Private Community Settings', 'bp-lock' ); ?>
				</h1>
			</div>
			<div class="wbcom-admin-settings-page">
			<?php
			/**
			 * Save General Settings
			 */
			// if( isset( $_POST['bplock-general-settings-submit'] ) && wp_verify_nonce( $_POST['bplock-general-settings-nonce'], 'bplock-general' ) ) {.
			// $this->bplock_save_general_settings();.
			// }.

			/**
			 * Save Custom Post Types Settings
			 */
			// if( isset( $_POST['bplock-cpt-settings-submit'] ) && wp_verify_nonce( $_POST['bplock-cpt-settings-nonce'], 'bplock-cpt' ) ) {
			// $this->bplock_save_cpt_settings();
			// }

			/**
			 * Save Pages Settings
			 */
			// if( isset( $_POST['bplock-pages-settings-submit'] ) && wp_verify_nonce( $_POST['bplock-pages-settings-nonce'], 'bplock-pages' ) ) {
			// $this->bplock_save_pages_settings();
			// }

			/**
			 * Save BuddyPress Components Settings
			 */
			// if( isset( $_POST['bplock-bp-components-settings-submit'] ) && wp_verify_nonce( $_POST['bplock-bp-components-settings-nonce'], 'bplock-bp-components' ) ) {
			// $this->bplock_save_bp_components_settings();
			// }
			// echo '</div>';
			$this->bplock_plugin_settings_tabs();
			do_settings_sections( $tab );
			?>
			</div>
                    </div>
		</div>
		<?php
	}

	/**
	 * Actions performed to create tabs on the options page
	 */
	public function bplock_plugin_settings_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] :'welcome';;
		echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<li><a class="nav-tab ' . esc_attr( $active ) . '" href="?page=' . esc_html( $this->plugin_name ) . '&tab=' . esc_html( $tab_key ) . '">' . esc_html( $tab_caption ) . '</a></li>';
		}
		echo '</div></ul></div>';
	}

	/**
	 * General Tab
	 */
	public function bplock_general_settings() {
		$this->plugin_settings_tabs[ 'welcome' ] = esc_html__( 'Welcome', 'bp-lock' );
		add_settings_section( 'bplock_welcome_settings', ' ', array( &$this, 'bplock_welcome_page_content' ), 'welcome' );

		$this->plugin_settings_tabs[ $this->plugin_name ] = esc_html__( 'General', 'bp-lock' );
		// register_setting( $this->plugin_name, $this->plugin_name );
		// add_settings_section( $this->plugin_name.'-general-section', ' ', array( &$this, 'bplock_general_settings_content' ), $this->plugin_name );
		add_settings_section( 'bplock_general_settings', ' ', array( &$this, 'bplock_general_settings_content' ), $this->plugin_name );
	}

	public function bplock_welcome_page_content() {
		if ( file_exists( BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-welcome-page.php' ) ) {
			require_once BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-welcome-page.php';
		}
	}
	/**
	 * General Tab Content
	 */
	public function bplock_general_settings_content() {
		if ( file_exists( BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-general-settings.php' ) ) {
			require_once BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-general-settings.php';
		}
	}

	/**
	 * BuddyPress Components Tab
	 */
	public function bplock_bp_components_settings() {
		// global $bplock;
		$general_settings = get_option( 'bplock_general_settings', true );
		if ( isset( $general_settings['bplock-bp-components'] ) ) {
			$this->plugin_settings_tabs['bp-components'] = esc_html__( 'BP Components', 'bp-lock' );
			register_setting( 'bp-components', 'bp-components' );
			add_settings_section( 'bp-components', ' ', array( &$this, 'bplock_bp_components_settings_content' ), 'bp-components' );
		}
	}

	/**
	 * BuddyPress Components Tab Content
	 */
	public function bplock_bp_components_settings_content() {
		if ( file_exists( BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-bp-components-settings.php' ) ) {
			require_once BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-bp-components-settings.php';
		}
	}

	/**
	 * CPT Tab
	 */
	// public function bplock_cpt_settings() {
	// $general_settings = get_option( 'bplock_general_settings', true );
	// if ( isset( $general_settings['bplock-custom-post-types'] ) ) {
	// $this->plugin_settings_tabs['custom-post-types'] = esc_html__( 'Custom Post Types', 'bp-lock' );
	// register_setting( 'custom-post-types', 'custom-post-types' );
	// add_settings_section( 'custom-post-types', ' ', array( &$this, 'bplock_cpt_settings_content' ), 'custom-post-types' );
	// }
	// }

	/**
	 * CPT Tab Content
	 */
	// public function bplock_cpt_settings_content() {
	// if ( file_exists( BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-cpt-settings.php' ) ) {
	// require_once BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-cpt-settings.php';
	// }
	// }

	/**
	 * WP Pages Tab
	 */
	public function bplock_wp_pages_settings() {
		// global $bplock;
		$general_settings = get_option( 'bplock_general_settings', true );

		if ( isset( $general_settings['bplock-pages'] ) ) {
			$this->plugin_settings_tabs['pages'] = esc_html__( 'Pages', 'bp-lock' );
			register_setting( 'bplock-pages', 'bplock-pages' );
			add_settings_section( 'bplock-pages', ' ', array( &$this, 'bplock_wp_pages_settings_content' ), 'pages' );
		}
	}

	/**
	 * WP Pages Tab Content
	 */
	public function bplock_wp_pages_settings_content() {
		if ( file_exists( BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-pages-settings.php' ) ) {
			require_once BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-pages-settings.php';
		}
	}

	/**
	 * Support Tab
	 */
	public function bplock_support() {
		$this->plugin_settings_tabs['bp-lock-support'] = esc_html__( 'Support', 'bp-lock' );
		register_setting( 'bp-lock-support', 'bp-lock-support' );
		add_settings_section( 'bp-lock-support-section', ' ', array( &$this, 'bplock_support_content' ), 'bp-lock-support' );
	}

	/**
	 * Support Tab Content
	 */
	public function bplock_support_content() {
		if ( file_exists( BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-support.php' ) ) {
			require_once BPLOCK_PLUGIN_PATH . 'admin/includes/bplock-support.php';
		}
	}

	public function bplock_register_settings_function() {
		register_setting( 'bplock_general_settings', 'bplock_general_settings' );
	}
}
