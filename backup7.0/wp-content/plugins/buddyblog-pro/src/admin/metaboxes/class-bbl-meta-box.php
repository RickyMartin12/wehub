<?php
/**
 * BuddyBlog Form meta class base
 *
 * @package    BuddyBlog_Pro
 * @subpackage Admin/Metaboxes
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BuddyBlog_Pro\Admin\Metaboxes;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Base class for meta boxes.
 */
abstract class BBL_Meta_Box {

	/**
	 * Saves meta.
	 *
	 * @param \WP_Post $post post object.
	 */
	abstract public function save( $post );

	/**
	 * Renders Meta box.
	 *
	 * @param \WP_Post|null $post post object.
	 */
	abstract public function render( $post = null );

	/**
	 * Returns the input field value if present or default.
	 *
	 * @param string     $option option name.
	 * @param mixed|null $default default value.
	 *
	 * @return mixed|null
	 */
	protected function input( $option, $default = null ) {
		$option = 'bbl-input-' . $option;

		return isset( $_POST[ $option ] ) ? wp_unslash( $_POST[ $option ] ) : $default;
	}

	/**
	 * Renders checkboxes.
	 *
	 * @param array $args args.
	 *
	 * @return string
	 */
	protected function checkbox( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'name'     => '',
				'id'       => '',
				'options'  => array(),
				'selected' => '',
				'echo'     => true,
			)
		);

		$name = $args['name'];
		$id   = $args['id'] ? $args['id'] : $args['name'];
		$html = "<div id='{$id}-wrapper'><ul>";

		$selected = ! is_array( $args['selected'] ) ? (array) $args['selected'] : $args['selected'];

		foreach ( $args['options'] as $key => $label ) {
			$html .= "<li><label><input type='checkbox' name='{$name}[]' value='" . esc_attr( $key ) . "' " . checked( true, in_array( $key, $selected ), false ) . " />{$label}</label></li>";
		}

		$html .= '</ul></div>';
		if ( $args['echo'] ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Renders a selectbox.
	 *
	 * @param array $args args.
	 *
	 * @return string
	 */
	protected function selectbox( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'name'     => '',
				'id'       => '',
				'options'  => array(),
				'selected' => '',
				'echo'     => true,
			)
		);

		$name = $args['name'];
		$id   = $args['id'] ? $args['id'] : $args['name'];
		$html = "<select name='{$name}' id='{$id}'>";

		foreach ( $args['options'] as $key => $label ) {
			$html .= "<option value='" . esc_attr( $key ) . "' " . selected( $key, $args['selected'], false ) . ">{$label}</option>";
		}

		$html .= '</select>';

		if ( $args['echo'] ) {
			echo $html;
		}

		return $html;
	}
}
