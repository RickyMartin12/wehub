<?php

/**
 * The global variable functionality of the plugin.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bp_Lock
 * @subpackage Bp_Lock/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Bp_Lock_Globals {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $plugin_name
	 */
	public $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version
	 */
	public $version;

	/**
	 * The variable holds whether to lock BuddyPress components or not.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $lock_bp_components
	 */
	public $lock_bp_components;

	/**
	 * The variable holds whether to lock wp pages or not.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $lock_wp_pages
	 */
	public $lock_wp_pages;

	/**
	 * The variable holds whether to lock cpt or not.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $lock_cpt
	 */
	public $lock_cpt;

	/**
	 * The variable holds the locked page content.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $locked_content
	 */
	public $locked_content;

	/**
	 * The variable holds the array of locked custom post types.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $locked_cpts
	 */
	public $locked_cpts;

	/**
	 * The variable holds the array of locked WordPress pages.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $locked_pages
	 */
	public $locked_pages;

	/**
	 * The variable holds the array of locked BuddyPress components.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $locked_bp_components
	 */
	public $locked_bp_components;

	/**
	 * The variable holds the path of the locked content template.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $locked_content_template
	 */
	public $locked_content_template;

	/**
	 * Define the global class constructor of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->setup_plugin_global();
		$this->copy_plugin_globals_to_option();

	}

	/**
	 * Define the global vaiable values.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function setup_plugin_global() {
		global $bplock;
		$bplock_general_settings = get_option( 'bplock_general_settings' );

		$this->lock_bp_components = 'no';
		if ( ! empty( $bplock_general_settings['lock_bp_components'] ) && BPLOCK_IS_BP_ACTIVE ) {
			$this->lock_bp_components = $bplock_general_settings['lock_bp_components'];
		}
		$this->lock_wp_pages = 'no';
		if ( ! empty( $bplock_general_settings['lock_wp_pages'] ) ) {
			$this->lock_wp_pages = $bplock_general_settings['lock_wp_pages'];
		}

		$this->lock_cpt = 'no';
		if ( ! empty( $bplock_general_settings['lock_cpt'] ) ) {
			$this->lock_cpt = $bplock_general_settings['lock_cpt'];
		}

		$this->locked_content = '';
		if ( ! empty( $bplock_general_settings['locked_content'] ) ) {
			$this->locked_content = $bplock_general_settings['locked_content'];
		}

		$this->locked_cpts = array();
		if ( ! empty( $bplock_general_settings['locked_cpts'] ) ) {
			$this->locked_cpts = $bplock_general_settings['locked_cpts'];
		}

		$this->locked_pages = array();
		if ( ! empty( $bplock_general_settings['locked_pages'] ) ) {
			$this->locked_pages = $bplock_general_settings['locked_pages'];
		}

		$this->locked_bp_components = array();
		if ( ! empty( $bplock_general_settings['locked_bp_components'] ) ) {
			$this->locked_bp_components = $bplock_general_settings['locked_bp_components'];
		}

		$this->lr_form = array();
		if ( ! empty( $bplock_general_settings['lr_form'] ) ) {
			$this->lr_form = $bplock_general_settings['lr_form'];
		}

		$this->custom_form_content = array();
		if ( ! empty( $bplock_general_settings['custom_form_content'] ) ) {
			$this->custom_form_content = $bplock_general_settings['custom_form_content'];
		}

		$locked_content_file_path      = BPLOCK_PLUGIN_PATH . 'public/templates/bplock-locked-content-template.php';
		$this->locked_content_template = apply_filters( 'bplock_locked_content_template', $locked_content_file_path );
	}

	public function copy_plugin_globals_to_option() {

		$general_settings = get_option( 'bplock_general_settings' );

		$bplock_gen_globals_copied = get_option( 'bplock_gen_globals_copied' );
		if ( ! $bplock_gen_globals_copied && $bplock_gen_globals_copied != 'copied' ) {
			$general_settings['bplock-bp-components']     = $this->lock_bp_components;
			$general_settings['bplock-pages']             = $this->lock_wp_pages;
			$general_settings['bplock-custom-post-types'] = $this->lock_cpt;
			$general_settings['locked_content']           = $this->locked_content;
			$general_settings['lr-form']                  = 'plugin_form';
			update_option( 'bplock_general_settings', $general_settings );

			$locked_bp_components = get_option( 'bp-components', true );
			$locked_bp_components = $this->locked_bp_components;
			update_option( 'bp-components', $locked_bp_components );

			$locked_cpts = get_option( 'custom-post-types', true );
			$locked_cpts = $this->locked_cpts;
			update_option( 'custom-post-types', $locked_cpts );

			$locked_pages = get_option( 'bplock-pages', true );
			$locked_pages = $this->locked_pages;
			update_option( 'custom-post-types', $locked_pages );

			update_option( 'bplock_gen_globals_copied', 'copied' );
		}

		$plugin_default = get_option( 'bplock-default-set' );
		if ( ! $plugin_default && $plugin_default != 'bplock-default' ) {
			// if( !isset( $general_settings['bplock-bp-components'] ) ){.
				$general_settings['bplock-bp-components'] = 'on';
				update_option( 'bplock_general_settings', $general_settings );
				update_option( 'bplock-default-set', 'bplock-default' );

				$locked_bp_components = array(
					'0' => 'members',
					'1' => 'groups',
					'2' => 'activity',
				);
				update_option( 'bp-components', $locked_bp_components );
				// }
		}
	}
}
