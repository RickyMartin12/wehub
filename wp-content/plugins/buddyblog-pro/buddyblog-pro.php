<?php
/**
 * Plugin Name: BuddyBlog Pro
 * Version: 1.1.4
 * Plugin URI: https://buddydev.com/plugins/buddyblog-pro/
 * Description: The most flexible front end publishing solution for BuddyPress and BuddyBoss communities.
 * Author: BuddyDev
 * Author URI: https://buddydev.com/
 * Requires PHP: 5.3
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  buddyblog-pro
 * Domain Path:  /languages
 *
 * @package buddyblog-pro
 **/

use BuddyBlog_Pro\Bootstrap\Autoloader;
use BuddyBlog_Pro\Bootstrap\Bootstrapper;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Class BuddyBlog Pro
 *
 * @property-read string    $path Absolute path to the plugin directory.
 * @property-read string    $url  Absolute url to the plugin directory.
 * @property-read string    $basename Plugin base name.
 * @property-read string    $version  Plugin version.
 * @property-read  WP_Error $errors  string Plugin version.
 * @property-read  WP_Error $notices  string Plugin version.
 */
class BuddyBlog_Pro {

	/**
	 * Plugin Version.
	 *
	 * @var string
	 */
	private $version = '1.1.4';

	/**
	 * Class instance
	 *
	 * @var BuddyBlog_Pro
	 */
	private static $instance = null;

	/**
	 * Plugin absolute directory path
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Plugin absolute directory url
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Plugin Basename.
	 *
	 * @var string
	 */
	private $basename;

	/**
	 * Protected properties. These properties are inaccessible via magic method.
	 *
	 * @var array
	 */
	private $guarded = array( 'instance' );

	/**
	 * Form handling error.
	 *
	 * @var WP_Error
	 */
	private $errors = null;

	/**
	 * Global notices.
	 *
	 * @var WP_Error
	 */
	private $notices = null;

	/**
	 * BP_User_Privacy constructor.
	 */
	private function __construct() {
		$this->bootstrap();
	}

	/**
	 * Get Singleton Instance
	 *
	 * @return BuddyBlog_Pro
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap the core.
	 */
	private function bootstrap() {

		$this->path     = plugin_dir_path( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->basename = plugin_basename( __FILE__ );
		$this->errors   = new WP_Error();
		$this->notices  = new WP_Error();

		// Load autoloader.
		require_once $this->path . 'src/bootstrap/class-autoloader.php';

		$autoloader = new Autoloader( 'BuddyBlog_Pro\\', __DIR__ . '/src/' );

		spl_autoload_register( $autoloader );

		register_activation_hook( __FILE__, array( $this, 'on_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'on_deactivation' ) );

		Bootstrapper::boot();
	}

	/**
	 * On activation create table
	 */
	public function on_activation() {
		if ( ! get_option( 'buddyblog-pro' ) ) {
			require_once $this->path . 'src/core/bblpro-functions.php';
			update_option( 'buddyblog-pro', bblpro_get_default_settings() );
		}
	}

	/**
	 * On deactivation. Do cleanup if needed.
	 */
	public function on_deactivation() {
		// do cleanup.
	}

	/**
	 * Get the plugin base file.
	 *
	 * @return string
	 */
	public function get_file() {
		return __FILE__;
	}

	/**
	 * Magic method for accessing property as readonly(It's a lie, references can be updated).
	 *
	 * @param string $name property name.
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {

		if ( ! in_array( $name, $this->guarded, true ) && property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		return null;
	}
}

/**
 * Helper to access singleton instance
 *
 * @return BuddyBlog_Pro
 */
function buddyblog_pro() {
	return BuddyBlog_Pro::get_instance();
}

buddyblog_pro();
