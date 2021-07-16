<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Lock
 * @subpackage Bp_Lock/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bp_Lock
 * @subpackage Bp_Lock/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Bp_Lock {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bp_Lock_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BPLOCK_PLUGIN_VERSION' ) ) {
			$this->version = BPLOCK_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'bp-lock';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_globals();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bp_Lock_Loader. Orchestrates the hooks of the plugin.
	 * - Bp_Lock_i18n. Defines internationalization functionality.
	 * - Bp_Lock_Admin. Defines all hooks for the admin area.
	 * - Bp_Lock_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-lock-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-lock-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-lock-admin.php';

		/**
		 * The class responsible for defining the global variable of the plugin
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-lock-globals.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bp-lock-public.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Include bp-lock feedback class */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-lock-feedback.php';

		$this->loader = new Bp_Lock_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bp_Lock_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Bp_Lock_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Bp_Lock_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'bplock_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'bplock_enqueue_scripts' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'bplock_register_settings_function' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bplock_add_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bplock_general_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bplock_bp_components_settings' );
		// $this->loader->add_action( 'admin_init', $plugin_admin, 'bplock_cpt_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bplock_wp_pages_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bplock_support' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Bp_Lock_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'bplock_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'bplock_enqueue_scripts' );
		$this->loader->add_filter( 'template_include', $plugin_public, 'bplock_lock_pages' );
		$this->loader->add_filter( 'template_include', $plugin_public, 'bplock_lock_cpts' );
		$this->loader->add_filter( 'single_template', $plugin_public, 'bplock_lock_cpt_single', 999 );
		$this->loader->add_filter( 'bp_located_template', $plugin_public, 'bplock_lock_bp_components', 999, 2 );
		$this->loader->add_shortcode( 'bplock_login_form', $plugin_public, 'bplock_login_form_template' );
		$this->loader->add_filter( 'wp_ajax_nopriv_bplock_login', $plugin_public, 'bplock_login' );
		$this->loader->add_filter( 'wp_ajax_nopriv_bplock_register', $plugin_public, 'bplock_register' );
		$this->loader->add_filter( 'pre_get_posts', $plugin_public, 'bplock_exclude_search' );
	}

	/**
	 * Registers a global variable of the plugin - $bplock
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function define_globals() {
		global $bplock;
		$bplock = new Bp_Lock_Globals( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Bp_Lock_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
