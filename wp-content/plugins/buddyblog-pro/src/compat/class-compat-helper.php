<?php
/**
 * Helps achieving compatibility with themes
 *
 * @package    BuddyBlog_Pro
 * @subpackage Compat
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Compat;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Helps achieving compatibility with themes
 */
class Compat_Helper {

	/**
	 * Class self boot
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	private function setup() {
		//add_action( 'bblpro_template_base_dir', array( $this, 'filter_template_base_dir' ) );
		add_filter( 'bblpro_template_stack', array( $this, 'update_stack' ) );
		add_action( 'bp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'kleo_post_footer', array( $this, 'kleo_action_links' ) );
	}

	/**
	 * Filter base template directory for BuddyBlog.
	 *
	 * @param array $stacks Array of template stacks.
	 *
	 * @return array
	 */
	public function update_stack( $stacks ) {

		if ( $this->is_community_builder() ) {
			$stacks['community-builder'] = 100;
		} elseif ( $this->is_youzer() ) {
			$stacks['youzer'] = 100;
		} elseif ( $this->is_buddyboss_theme() ) {
			$stacks['buddyboss'] = 100;
		} elseif ( $this->is_kleo() ) {
			$stacks['kleo'] = 100;
		} elseif ( $this->is_aardvark() ) {
			$stacks['aardvark'] = 100;
		}

		return $stacks;
	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue() {
		wp_enqueue_style( 'bbl-theme-css', bblpro_locate_asset( 'assets/css/default.css' ), array(), buddyblog_pro()->version );
	}

	/**
	 * Adds Edit|Delete links to Kleo post loop entries
	 */
	public function kleo_action_links() {
		?>

		<?php echo bblpro_get_post_edit_link( get_the_ID() ); ?>
		<?php echo bblpro_get_post_delete_link( get_the_ID() ); ?>

		<?php
	}

	/**
	 * Is community builder theme.
	 *
	 * @return bool
	 */
	private function is_community_builder() {
		return class_exists( 'Community_Builder' );
	}

	/**
	 * Is BuddyBoss theme active.
	 *
	 * @return bool
	 */
	private function is_buddyboss_theme() {
		return function_exists( 'buddyboss_theme' );
	}

	/**
	 * Is it Aardvark theme.
	 *
	 *
	 * @todo add templates later.
	 *
	 * @return bool
	 */
	private function is_aardvark() {
		return false;// we do not provide exlicit support in first version.

		// return defined( 'AARDVARK_THEME_VERSION' );
	}

	/**
	 * Is kleo theme.
	 *
	 * @return bool
	 */
	private function is_kleo() {
		return class_exists( 'Kleo' ) || function_exists( 'kleo_setup' );
	}

	/**
	 * Check if Youzer is active.
	 *
	 * @return bool
	 */
	private function is_youzer() {
		return class_exists( 'Youzer' );
	}
}
