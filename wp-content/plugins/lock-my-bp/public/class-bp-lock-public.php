<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Lock
 * @subpackage Bp_Lock/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin namev, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bp_Lock
 * @subpackage Bp_Lock/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Bp_Lock_Public {

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function bplock_enqueue_styles() {
		wp_enqueue_style( $this->plugin_name . '-font-awesome', BPLOCK_PLUGIN_URL . 'admin/css/font-awesome.min.css' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-lock-public.css' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function bplock_enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-lock-public.js', array( 'jquery' ) );
		wp_localize_script(
			$this->plugin_name,
			'bplock_public_js_object',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}


	public function bplock_lock_pages( $template ) {
		/**
	 * Modify the template of the locked pages.
	 *
	 * @since    1.0.0
	 */
		global $post, $bplock, $wp;

		$general_settings = get_option( 'bplock_general_settings', true );
		$lr_form          = ( isset( $general_settings['lr-form'] ) ) ? $general_settings['lr-form'] : 'plugin_form';

		if ( ! isset( $general_settings['bplock-pages'] ) ) {
			return $template;
		}
		$locked_pages = get_option( 'bplock-pages', true );

		$current_url = home_url( $wp->request );
		$match       = false;
		if ( is_array( $locked_pages ) ) {
			foreach ( $locked_pages as $pid ) {
				$slug = get_post_field( 'post_name', $pid );
				if ( $slug ) {
					if ( strpos( $current_url, $slug ) !== false ) {
						$match = true;
					}
				}
			}
		}
		if ( ! is_user_logged_in() && is_array( $locked_pages ) && ! empty( $post ) && in_array( $post->ID, $locked_pages ) || ! is_user_logged_in() && is_array( $locked_pages ) && $match ) {

			if ( 'page_redirect' === $lr_form ) {

				$redirepage     = isset( $general_settings['logout_redirect_page'] ) ? $general_settings['logout_redirect_page'] : '';
				$redirepage_url = get_permalink( $redirepage );
				if ( ! empty( $redirepage_url ) ) {
					?>
						<script>
							window.location = '<?php echo esc_url( $redirepage_url ); ?>';
						</script>
					<?php
				}
			} else {
				if ( file_exists( $bplock->locked_content_template ) ) {
					$found_template = $bplock->locked_content_template;
				}
			}
		}
		return $template;
	}

	public function bplock_exclude_search( $query ) {
		global $bplock, $bp, $wp;

		$general_settings = get_option( 'bplock_general_settings', true );

		$locked_bp_components = get_option( 'bp-components', true );
		$locked_pages         = get_option( 'bplock-pages', true );
		$locked_cpts          = get_option( 'custom-post-types', true );

		$match = array();
		if ( ! is_user_logged_in() && isset( $general_settings['bplock-bp-components'] ) && is_array( $locked_bp_components ) ) {
			foreach ( $locked_bp_components as $bpc ) {
				$pid = get_page_by_path( $bpc );
				if ( $pid ) {
					$match[] = $pid->ID;
				}
			}
		}

		if ( ! is_user_logged_in() && isset( $general_settings['bplock-pages'] ) && is_array( $locked_pages ) ) {
			foreach ( $locked_pages as $pid ) {
				if ( $pid ) {
					if ( ! in_array( $pid, $match ) ) {
						$match[] = $pid;
					}
				}
			}
		}
		$match_cpt = array();
		$cpts      = array();
		if ( ! is_user_logged_in() && isset( $general_settings['bplock-custom-post-types'] ) && is_array( $locked_cpts ) ) {
			$args = array(
				'public'              => true,
				'_builtin'            => false,
				'exclude_from_search' => false,
			);
			$cpts = get_post_types( $args, 'name' );
			if ( ! empty( $cpts ) ) {
				$cpts = array_keys( $cpts );
			}
			foreach ( $locked_cpts as $cpt ) {
				if ( $cpt ) {
					$match_cpt[] = $cpt;
				}
			}
		}
		$new_cpts = array();
		if ( ! empty( $match_cpt ) && ! empty( $cpts ) ) {
			foreach ( $cpts as $key => $cpt ) {
				if ( ! in_array( $cpt, $match_cpt ) ) {
					$new_cpts[] = $cpt;
				}
			}
		}
		if ( $query->is_search ) {
			if ( ! empty( $match ) ) {
				$query->set( 'post__not_in', $match );
			}
			if ( ! empty( $new_cpts ) ) {
				$query->set( 'post_type', $new_cpts );
			}
		}

		return $query;
	}

	/**
	 * Modify the archive template of the locked custom post types.
	 *
	 * @since    1.0.0
	 */
	public function bplock_lock_cpts( $template ) {
		global $post, $bplock;

		$general_settings = get_option( 'bplock_general_settings', true );
		$locked_cpts      = get_option( 'custom-post-types', true );
		$lr_form          = ( isset( $general_settings['lr-form'] ) ) ? $general_settings['lr-form'] : 'plugin_form';

		if ( ! is_user_logged_in() && isset( $general_settings['bplock-custom-post-types'] ) && is_array( $locked_cpts ) && ! empty( $post ) && in_array( $post->post_type, $locked_cpts ) ) {

			if ( 'page_redirect' === $lr_form ) {

				$redirepage     = isset( $general_settings['logout_redirect_page'] ) ? $general_settings['logout_redirect_page'] : '';
				$redirepage_url = get_permalink( $redirepage );
				if ( ! empty( $redirepage_url ) ) {
					?>
						<script>
							window.location = '<?php echo esc_url( $redirepage_url ); ?>';
						</script>
					<?php
				}
			} else {
				if ( file_exists( $bplock->locked_content_template ) ) {
					$found_template = $bplock->locked_content_template;
				}
			}
		}
		return $template;
	}

	/**
	 * Modify the single template of the locked custom post types.
	 *
	 * @since    1.0.0
	 */
	public function bplock_lock_cpt_single( $template ) {
		global $post, $bplock;

		$general_settings = get_option( 'bplock_general_settings', true );
		$locked_cpts      = get_option( 'custom-post-types', true );
		$lr_form          = ( isset( $general_settings['lr-form'] ) ) ? $general_settings['lr-form'] : 'plugin_form';

		if ( ! is_user_logged_in() && isset( $general_settings['bplock-custom-post-types'] ) && is_array( $locked_cpts ) && ! empty( $post ) && in_array( $post->post_type, $locked_cpts ) ) {

			if ( 'page_redirect' === $lr_form ) {

				$redirepage     = isset( $general_settings['logout_redirect_page'] ) ? $general_settings['logout_redirect_page'] : '';
				$redirepage_url = get_permalink( $redirepage );
				if ( ! empty( $redirepage_url ) ) {
					?>
						<script>
							window.location = '<?php echo esc_url( $redirepage_url ); ?>';
						</script>
					<?php
				}
			} else {
				if ( file_exists( $bplock->locked_content_template ) ) {
					$found_template = $bplock->locked_content_template;
				}
			}
		}
		return $template;
	}

	/**
	 * Modify the single template of the locked custom post types.
	 *
	 * @since    1.0.0
	 */
	public function bplock_lock_bp_components( $found_template, $templates ) {
		global $bplock, $bp, $wp;

		$general_settings     = get_option( 'bplock_general_settings', true );
		$locked_bp_components = get_option( 'bp-components', true );
		$lr_form              = ( isset( $general_settings['lr-form'] ) ) ? $general_settings['lr-form'] : 'plugin_form';

		$current_url = home_url( $wp->request );
		$match       = false;
		if ( is_array( $locked_bp_components ) && isset( $general_settings['bplock-bp-components'] ) ) {
			foreach ( $locked_bp_components as $url ) {
				if ( strpos( $current_url, $url ) !== false ) {
					$match = true;
				}
			}
		}
		if ( ! is_user_logged_in() && isset( $general_settings['bplock-bp-components'] ) && is_array( $locked_bp_components ) && in_array( $bp->current_component, $locked_bp_components ) || ! is_user_logged_in() && isset( $general_settings['bplock-bp-components'] ) && is_array( $locked_bp_components ) && $match ) {

			if ( 'page_redirect' === $lr_form ) {

				$redirepage     = isset( $general_settings['logout_redirect_page'] ) ? $general_settings['logout_redirect_page'] : '';
				$redirepage_url = get_permalink( $redirepage );
				if ( ! empty( $redirepage_url ) ) {
					?>
						<script>
							window.location = '<?php echo esc_url( $redirepage_url ); ?>';
						</script>
					<?php
				}
			} else {
				if ( file_exists( $bplock->locked_content_template ) ) {
					$found_template = $bplock->locked_content_template;
				}
			}
		}
		return $found_template;
	}

	/**
	 * The shortcode template for user login
	 *
	 * @since    1.0.0
	 */
	public function bplock_login_form_template() {
		global $bplock;
		?>
		<div class="bplock-login-form-container">
			<ul class="bplock-login-shortcode-tabs">
				<li class="tab-link current" id="bplock-login-tab" data-tab="tab-login"><i class="fa fa-sign-in" aria-hidden="true"></i> <?php esc_html_e( 'Login', 'bp-lock' ); ?></li>
				<?php $users_can_register = get_option( 'users_can_register' ); ?>
				<?php if ( $users_can_register ) { ?>
					<li class="tab-link" id="bplock-register-tab" data-tab="tab-register"><i class="fa fa-user-plus" aria-hidden="true"></i> <?php esc_html_e( 'Register', 'bp-lock' ); ?></li>
				<?php } ?>
			</ul>
			<div id="tab-login" class="tab-content current">
				<?php
				// $file = BPLOCK_PLUGIN_PATH . 'public/templates/bplock-login-form.php';
				$file = $this->bplock_get_template( 'bplock-login-form.php' );
				if ( file_exists( $file ) ) {
					include_once $file;
				}
				?>
			</div>
			<div id="tab-register" class="tab-content">
				<?php
				// $file = BPLOCK_PLUGIN_PATH . 'public/templates/bplock-register-form.php';
				$file = $this->bplock_get_template( 'bplock-register-form.php' );
				if ( file_exists( $file ) ) {
					include_once $file;
				}
				?>
			</div>
		</div>
		</div>
		<?php
	}

	/**
	 * AJAX server, to login the user
	 */
	function bplock_login() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bplock_login' ) {
			$username = sanitize_text_field( $_POST['username'] );
			$password = sanitize_text_field( $_POST['password'] );

			$credentials   = array(
				'user_login'    => sanitize_text_field( $_POST['username'] ),
				'user_password' => sanitize_text_field( $_POST['password'] ),
				'remember'      => true,
			);
			$secure_cookie = true;
			$user          = wp_signon( $credentials, $secure_cookie );
			if ( is_wp_error( $user ) ) {
				$login_success = 'no';
				$msg           = $user->get_error_message();
			} else {
				$login_success = 'yes';
				$msg           = esc_html__( 'Login successful! Redirecting...', 'bp-lock' );
			}
			$response = array(
				'message'       => apply_filters( 'bplock_login_success_message', $msg ),
				'login_success' => $login_success,
			);
			wp_send_json_success( $response );
			die;
		}
	}

	/**
	 * AJAX server, to register the user
	 */
	function bplock_register() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'bplock_register' ) {
			$email    = sanitize_text_field( $_POST['email'] );
			$username = sanitize_text_field( $_POST['username'] );
			$password = sanitize_text_field( $_POST['password'] );
			$user     = get_user_by( 'email', $email );

			if ( ! empty( $user ) ) {
				$register_success = 'no';
				$msg              = apply_filters( 'bplock_register_user_already_exists_message', esc_html__( 'User account already exists with the requested email!', 'bp-lock' ) );
			} else {
				$user_id       = wp_create_user( $username, $password, $email );
				$credentials   = array(
					'user_login'    => $username,
					'user_password' => $password,
					'remember'      => true,
				);
				$secure_cookie = true;
				$loggedin_user = wp_signon( $credentials, $secure_cookie );
				if ( is_wp_error( $user ) ) {
					$register_success = 'no';
					$msg              = $loggedin_user->get_error_message();
				} else {
					$register_success = 'yes';
					$msg              = apply_filters( 'bplock_register_success_message', esc_html__( 'User registered! Logging in...', 'bp-lock' ) );
				}
			}
			$response = array(
				'message'          => $msg,
				'register_success' => $register_success,
			);
			wp_send_json_success( $response );
			die;
		}
	}

	/**
	 * Locate template.
	 *
	 * Locate the called template.
	 * Search Order:
	 * 1. /themes/child-theme/bp-lock/$template_name
	 * 2. /themes/theme/bp-lock/$template_name
	 * 3. /plugins/bp-lock/public/templates/$template_name.
	 *
	 * @since 1.0.0
	 *
	 * @param   string $template_name          Template to load.
	 * @param   string $string $template_path  Path to templates.
	 * @param   string $default_path           Default path to template files.
	 * @return  string                          Path to the template file.
	 */
	public function bplock_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		// Set variable to search in woocommerce-plugin-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'bp-lock/';
		endif;

		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = plugin_dir_path( __FILE__ ) . 'templates/'; // Path to the template folder.
		endif;

		// Search template file in theme folder.
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
			)
		);

		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;

		return apply_filters( 'bplock_locate_template', $template, $template_name, $template_path, $default_path );

	}

	/**
	 * Get template.
	 *
	 * Search for the template and include the file.
	 *
	 * @since 1.0.0
	 *
	 * @see bplock_get_template()
	 *
	 * @param string $template_name          Template to load.
	 * @param array  $args                   Args passed for the template file.
	 * @param string $string $template_path  Path to templates.
	 * @param string $default_path           Default path to template files.
	 */
	public function bplock_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {

		if ( is_array( $args ) && isset( $args ) ) :
			extract( $args );
		endif;

		$template_file = $this->bplock_locate_template( $template_name, $tempate_path, $default_path );
// @codingStandardsIgnoreStart
		if ( ! file_exists( $template_file ) ) :

			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
			return;
		endif;
// @codingStandardsIgnoreEnd
		include $template_file;

	}

}
