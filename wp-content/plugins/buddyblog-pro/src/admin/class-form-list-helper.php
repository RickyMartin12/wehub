<?php
/**
 * BuddyBlog Pro Form List helper
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Form list helper.
 */
class Form_List_Helper {

	/**
	 * Constructor
	 */
	private function __construct() {
	}

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
		$post_type = bblpro_get_form_post_type();
		// post type columns.
		add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_cols' ) );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'column_data' ), 10, 2 );

		// sortable by atatched post type..
		add_filter( "manage_edit-{$post_type}_sortable_columns", array( $this, 'add_sortable_cols' ) );
		add_action( 'pre_get_posts', array( $this, 'sort_by_attached_post_type' ) );
	}

	/**
	 * Add columns.
	 *
	 * @param array $cols columns.
	 *
	 * @return array
	 */
	public function add_cols( $cols ) {
		$cols['bbl_post_type'] = __( 'Post Type', 'buddyblog-pro' );

		return $cols;
	}

	/**
	 * Add columns.
	 *
	 * @param array $cols columns.
	 *
	 * @return array
	 */
	public function add_sortable_cols( $cols ) {
		$cols['bbl_post_type'] = 'bbl_post_type';
		return $cols;
	}


	/**
	 * Prints post type name.
	 *
	 * @param string $col column name.
	 * @param int    $post_id post id.
	 */
	public function column_data( $col, $post_id ) {

		if ( 'bbl_post_type' === $col ) {
			$ptype_object = get_post_type_object( bblpro_form_get_post_type( $post_id ) );
			if ( $ptype_object ) {
				echo $ptype_object->labels->singular_name;
			}
		}
	}

	/**
	 * Apply order by atatched post type if applicable.
	 *
	 * @param \WP_Query $query query.
	 */
	public function sort_by_attached_post_type( $query ) {

		if ( ! isset( $_GET['orderby'] ) || 'bbl_post_type' !== wp_unslash( $_GET['orderby'] ) ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( ! in_array( bblpro_get_form_post_type(), (array) $query->get( 'post_type' ) ) ) {
			return;
		}

		$query->set( 'orderby', 'meta_value' );
		$query->set( 'meta_key', '_buddyblog_post_type' );
	}
}
