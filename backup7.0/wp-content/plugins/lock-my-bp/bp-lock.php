<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.wbcomdesigns.com
 * @since             1.0.0
 * @package           Bp_Lock
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - Private Community for BuddyPress
 * Plugin URI:        http://www.wbcomdesigns.com
 * Description:       BuddyPress Private Community allows the site owner to lock the different BuddyPress components on the site for non-logged-in users. It also gives options to lockdown pages.
 * Version:           1.6.0
 * Author:            Wbcom Designs
 * Author URI:        http://www.wbcomdesigns.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-lock
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BPLOCK_PLUGIN_VERSION', '1.6.0' );

if ( ! defined( 'BPLOCK_PLUGIN_PATH' ) ) {
	define( 'BPLOCK_PLUGIN_PATH', plugin_dir_path(__FILE__) );
}

if ( ! defined( 'BPLOCK_TEXT_DOMAIN' ) ) {
	define( 'BPLOCK_TEXT_DOMAIN', 'bp-lock' );
}

if ( ! defined( 'BPLOCK_PLUGIN_URL' ) ) {
	define( 'BPLOCK_PLUGIN_URL', plugin_dir_url(__FILE__) );
}

if ( ! defined( 'BPLOCK_IS_BP_ACTIVE' ) ) {
	define( 'BPLOCK_IS_BP_ACTIVE', in_array( 'buddypress/bp-loader.php', get_option( 'active_plugins' ) ) );
}

if ( !defined( 'BP_ENABLE_MULTIBLOG' ) ) {
	define( 'BP_ENABLE_MULTIBLOG', false );
}
if ( !defined( 'BP_ROOT_BLOG' ) ) {
	define( 'BP_ROOT_BLOG', 1 );
}

if ( ! defined( 'BPLOCK_IS_MULTISITE' ) ) {
	define( 'BPLOCK_IS_MULTISITE', is_multisite() );
}

if ( BPLOCK_IS_MULTISITE ) {
	// Makes sure the plugin is defined before trying to use it
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	if ( ! defined( 'BPLOCK_IS_BP_NETWORK_ACTIVE' ) ) {
		define( 'BPLOCK_IS_BP_NETWORK_ACTIVE', is_plugin_active_for_network('buddypress/bp-loader.php') );
	}
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bp-lock-activator.php
 */
function activate_bp_lock() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-lock-activator.php';
	Bp_Lock_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bp-lock-deactivator.php
 */
function deactivate_bp_lock() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-lock-deactivator.php';
	Bp_Lock_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bp_lock' );
register_deactivation_hook( __FILE__, 'deactivate_bp_lock' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bp_lock() {
	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-bp-lock.php';
	$plugin = new Bp_Lock();
	$plugin->run();

}


/**
 * Actions performed on hook: plugins loaded.
 */
add_action( 'plugins_loaded', 'bplock_plugin_init' );
function bplock_plugin_init() {
	run_bp_lock();
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bplock_plugin_links' );
}

function bplock_plugin_links( $links ) {
	$bplock_links = array(
		'<a href="' . admin_url( 'admin.php?page=bp-lock' ) . '">' . esc_html__( 'Settings', 'bp-lock' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank" title="' . esc_html__( 'Go for any custom development.', 'bp-lock' ) . '">' . esc_html__( 'Support', 'bp-lock' ) . '</a>'
	);
	return array_merge( $links, $bplock_links );
}

/**
 *  Check if buddypress activate.
 */
function bplock_requires_buddypress()
{

    if ( !class_exists( 'Buddypress' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        //deactivate_plugins('buddypress-polls/buddypress-polls.php');
        add_action( 'admin_notices', 'bplock_required_plugin_admin_notice' );
        unset($_GET['activate']);
    }
}

add_action( 'admin_init', 'bplock_requires_buddypress' );
/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  1.3.0
 */
function bplock_required_plugin_admin_notice()
{

    $bpquotes_plugin          = esc_html__('BuddyPress Lock', 'bp-lock');
    $bp_plugin                = esc_html__('BuddyPress', 'bp-lock');
    echo '<div class="error"><p>';
    echo sprintf(esc_html__('%1$s is ineffective now as it requires %2$s to be installed and active.', 'bp-lock'), '<strong>' . esc_html($bpquotes_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>');
    echo '</p></div>';
    if (isset($_GET['activate']) ) {
        unset($_GET['activate']);
    }
}


/**
 * redirect to plugin settings page after activated
 */

add_action( 'activated_plugin', 'bplock_activation_redirect_settings' );
function bplock_activation_redirect_settings( $plugin ){

	if( $plugin == plugin_basename( __FILE__ ) ) {
		wp_redirect( admin_url( 'admin.php?page=bp-lock' ) ) ;
		exit;
	}
} 
