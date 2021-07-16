<?php
/**
 * Post functions
 *
 * @package    BuddyBlog_Pro
 * @subpackage Core
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Get raw post meta value.
 *
 * @param int    $post_id post id.
 * @param string $meta_key registered meta key in our form.
 *
 * @return array|string
 */
function bblpro_get_post_meta_raw( $post_id, $meta_key ) {
	$field             = null;
	$field_type_object = null;
	$form_id           = bblpro_post_get_form_id( $post_id );

	$value = null;

	if ( $form_id ) {

		$field = bblpro_form_get_custom_field( $form_id, $meta_key );

		if ( isset( $field['type'] ) ) {
			$field_type_object = bblpro_get_field_type_object( $field['type'] );
		}

		// is it our registered field?
		if ( $field && $field_type_object ) {
			$value = $field_type_object->get_field_data_raw( $post_id, $meta_key );
		}
	}

	// we know that the value was not generated.
	if ( is_null( $value ) || ! $field || ! $field_type_object ) {
		$value = get_post_meta( $post_id, $meta_key, true );
	}

	return $value;
}

/**
 * Get post meta value.
 *
 * @param int    $post_id post id.
 * @param string $meta_key meta key.
 * @param string $separator separator for multi value field.
 *
 * @return string
 */
function bblpro_get_post_meta( $post_id, $meta_key, $separator = ',' ) {

	$field             = null;
	$field_type_object = null;
	$form_id           = bblpro_post_get_form_id( $post_id );

	$value = null;

	if ( $form_id ) {

		$field = bblpro_form_get_custom_field( $form_id, $meta_key );

		if ( isset( $field['type'] ) ) {
			$field_type_object = bblpro_get_field_type_object( $field['type'] );
		}

		// is it our registered field?
		if ( $field && $field_type_object ) {
			$value = $field_type_object->get_field_data( $post_id, $meta_key );
		}
	}

	// we know that the value was not generated.
	if ( is_null( $value ) || ! $field || ! $field_type_object ) {
		$value = get_post_meta( $post_id, $meta_key, true );
	}

	return is_array( $value ) ? join( $separator, $value ) : $value;
}

/**
 * Update post meta.
 *
 * @param int   $form_id form id.
 * @param int   $post_id post id.
 * @param array $cf_settings custom field settings.
 * @param array $cf_data custom field data.
 */
function bblpro_post_update_custom_fields( $form_id, $post_id, $cf_settings, $cf_data ) {

	if ( empty( $cf_settings ) ) {
		return;
	}

	foreach ( $cf_settings as $key => $field_setting ) {
		// what should we do with tha value if not present.
		$value = isset( $cf_data[ $key ] ) ? $cf_data[ $key ] : null;
		$value = bblpro_get_sanitized_value( $value, $field_setting, $form_id, $post_id );

		// Remove if null.
		if ( is_null( $value ) ) {
			delete_post_meta( $post_id, $key );
			continue;
		}

		bblpro_post_update_custom_field_data( $post_id, $key, $value, $field_setting );
	}
}

/**
 * Save Post meta.
 *
 * @param int    $post_id post id.
 * @param string $meta_key meta key.
 * @param mixed  $value meta value.
 * @param array  $field_setting field settings.
 *
 * @return false
 */
function bblpro_post_update_custom_field_data( $post_id, $meta_key, $value, $field_setting ) {

	$type_object = bblpro_get_field_type_object( $field_setting['type'] );

	if ( ! $type_object ) {
		return false; // Not a valid registered type.
	}

	$type_object->save_value( $post_id, $meta_key, $value, $field_setting );
}

/**
 * Handles upload fields.
 *
 * @param int    $post_id post id.
 * @param string $key key.
 * @param array  $field_setting field settings.
 *
 * @todo implement
 */
function bblpro_post_handle_upload_field( $post_id, $key, $field_setting ) {

}

/**
 * Get post form id.
 *
 * @param int $post_id post id.
 *
 * @return int
 */
function bblpro_post_get_form_id( $post_id ) {
	return absint( get_post_meta( $post_id, '_bbl_form_id', true ) );
}

/**
 * Get post form id.
 *
 * @param int $post_id post id.
 * @param int $form_id form id.
 *
 * @return int
 */
function bblpro_post_update_form_id( $post_id, $form_id ) {
	return update_post_meta( $post_id, '_bbl_form_id', $form_id );
}

/**
 * Get tab url.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return string
 */
function bblpro_get_post_type_tab_url( $user_id, $post_type ) {
	return bp_core_get_user_domain( $user_id ) . bblpro_get_tab_slug( $post_type ) . '/';
}

/**
 * Get posts list url.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return string
 */
function bblpro_get_post_type_list_tab_url( $user_id, $post_type ) {
	return trailingslashit( bblpro_get_post_type_tab_url( $user_id, $post_type ) . bblpro_get_action_slug( $post_type, 'list' ) );
}

/**
 * returns post create tab url.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return string
 */
function bblpro_get_post_type_create_tab_url( $user_id, $post_type ) {

	$page_id = bblpro_get_create_page_id( $post_type );

	if ( $page_id ) {
		$url = get_permalink( $page_id );
	} else {
		$url = trailingslashit( bblpro_get_post_type_tab_url( $user_id, $post_type ) . bblpro_get_action_slug( $post_type, 'create' ) );
	}

	return $url;
}

/**
 * Get published posts list url.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return string
 */
function bblpro_get_post_type_published_tab_url( $user_id, $post_type ) {
	return trailingslashit( bblpro_get_post_type_tab_url( $user_id, $post_type ) . bblpro_get_action_slug( $post_type, 'published' ) );
}

/**
 * Get pending posts list url.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return string
 */
function bblpro_get_post_type_pending_tab_url( $user_id, $post_type ) {
	return trailingslashit( bblpro_get_post_type_tab_url( $user_id, $post_type ) . bblpro_get_action_slug( $post_type, 'pending' ) );
}

/**
 * Get posts Drafts tab url.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return string
 */
function bblpro_get_post_type_draft_tab_url( $user_id, $post_type ) {
	return trailingslashit( bblpro_get_post_type_tab_url( $user_id, $post_type ) . bblpro_get_action_slug( $post_type, 'draft' ) );
}

/**
 * Returns posts list url.
 *
 * @param int $post_id post id.
 *
 * @return string
 */
function bblpro_get_post_view_url( $post_id ) {

	$post = get_post( $post_id );
	$url  = trailingslashit( bblpro_get_post_type_tab_url( $post->post_author, $post->post_type ) . bblpro_get_action_slug( $post->post_type, 'view' ) );

	return trailingslashit( $url . $post_id );
}

/**
 * Returns post deletion link.
 *
 * @param int   $post_id post id.
 * @param array $args args.
 *
 * @return string
 */
function bblpro_get_post_view_link( $post_id, $args = array() ) {

	if ( ! bblpro_user_can_view_post( get_current_user_id(), $post_id ) ) {
		return '';
	}

	$defaults = array(
		'label' => _x( 'View', 'Post view link label', 'buddyblog-pro' ),
		'class' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['label'] ) ) {
		return '';
	}

	return sprintf( '<a href="%1$s" class="%3$s">%2$s</a>', esc_url( bblpro_get_post_view_url( $post_id ) ), $args['label'], 'bbl-action-link bbl-view-link ' . esc_attr( $args['class'] ) );
}

/**
 * Returns post edit url.
 *
 * @param int $post_id post id.
 *
 * @return string
 */
function bblpro_get_post_edit_url( $post_id ) {

	$post = get_post( $post_id );

	$page_id = bblpro_get_create_page_id( get_post_type( $post ) );

	if ( $page_id ) {
		$url = add_query_arg( array( 'bbl_post_id' => $post->ID ), get_permalink( $page_id ) );
	} else {
		$url = trailingslashit( trailingslashit( bblpro_get_post_type_tab_url( $post->post_author, $post->post_type ) . bblpro_get_action_slug( $post->post_type, 'edit' ) ) . $post_id );
	}

	return $url;
}

/**
 * Get edit post link.
 *
 * @param int   $post_id post id.
 * @param array $args args.
 *
 * @return string
 */
function bblpro_get_post_edit_link( $post_id, $args = array() ) {

	if ( ! bblpro_user_can_edit_post( get_current_user_id(), $post_id ) ) {
		return '';
	}

	$defaults = array(
		'label' => _x( 'Edit', 'Post edit link label', 'buddyblog-pro' ),
		'class' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['label'] ) ) {
		return '';
	}

	return sprintf( '<a href="%1$s" class="%3$s">%2$s</a>', esc_url( bblpro_get_post_edit_url( $post_id ) ), $args['label'], 'bbl-action-link bbl-edit-link' . esc_attr( $args['class'] ) );
}

/**
 * Get post delete url.
 *
 * @param int $post_id post id.
 *
 * @return string
 */
function bblpro_get_post_delete_url( $post_id, $screen = '' ) {

	if ( ! $screen && bp_current_action() ) {
		$screen = bp_current_action();
	}

	$post = get_post( $post_id );
	$url  = trailingslashit( bblpro_get_post_type_tab_url( $post->post_author, $post->post_type ) . bblpro_get_action_slug( $post->post_type, 'delete' ) );

	$url = wp_nonce_url( $url . $post_id, 'bblpro_delete_' . $post_id );

	return add_query_arg( array( 'screen' => $screen ), $url );
}

/**
 * Get delete post link.
 *
 * @param int   $post_id post id.
 * @param array $args args.
 *
 * @return string
 */
function bblpro_get_post_delete_link( $post_id, $args = array() ) {

	if ( ! bblpro_user_can_delete_post( get_current_user_id(), $post_id ) ) {
		return '';
	}

	$defaults = array(
		'label' => _x( 'Delete', 'Post delete link label', 'buddyblog-pro' ),
		'class' => '',
	);

	$args = wp_parse_args( $args, $defaults );
	if ( empty( $args['label'] ) ) {
		return '';
	}
	$post = get_post( $post_id );

	$is_confirmation_enabled = bblpro_get_option( "{$post->post_type}_action_delete_enable_confirmation", 1 );

	$text_confirm  = $is_confirmation_enabled ? __( 'Are you sure you want to delete this?', 'buddyblog-pro' ) : '';
	$confirm_class = $is_confirmation_enabled ? 'bbl-confirm-action' : '';

	return sprintf( '<a href="%1$s" class="%3$s" data-bbl-confirm="%4$s">%2$s</a>', esc_url( bblpro_get_post_delete_url( $post_id ) ), $args['label'], 'bbl-action-link bbl-delete-link ' . esc_attr( $args['class'] . ' ' . $confirm_class ), esc_attr( $text_confirm ) );
}

/**
 * Default post information to use when populating the "Write Post" form.
 * A clone of get_default_post_to_edit ( wp-admin/includes/post.php
 *
 * @param string $post_type Optional. A post type string. Default 'post'.
 * @param bool   $create_in_db Optional. Whether to insert the post into database. Default false.
 *
 * @return WP_Post Post object containing all the default post data as attributes
 */
function bblpro_get_default_post_to_edit( $post_type = 'post', $create_in_db = false ) {

	$post = apply_filters( 'bblpro_default_post_to_edit', null, $post_type, $create_in_db );

	if ( $post && $post instanceof \WP_Post ) {
		return $post;
	}

	$post_title = '';

	if ( ! empty( $_REQUEST['bbl_post_title'] ) ) {
		$post_title = esc_html( wp_unslash( $_REQUEST['bbl_post_title'] ) );
	}

	$post_content = '';
	if ( ! empty( $_REQUEST['bbl_post_content'] ) ) {
		$post_content = esc_html( wp_unslash( $_REQUEST['bbl_post_content'] ) );
	}

	$post_excerpt = '';
	if ( ! empty( $_REQUEST['excerpt'] ) ) {
		$post_excerpt = esc_html( wp_unslash( $_REQUEST['excerpt'] ) );
	}

	if ( $create_in_db ) {
		$post_id = wp_insert_post(
			array(
				'post_title'  => _x( 'Auto Draft', 'Auto draft post title', 'buddyblog-prot' ),
				'post_type'   => $post_type,
				'post_status' => 'auto-draft',
				'post_author' => get_current_user_id(),
			)
		);

		$post = get_post( $post_id );

		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) && get_option( 'default_post_format' ) ) {
			set_post_format( $post, get_option( 'default_post_format' ) );
		}
	} else {
		$post                 = new stdClass();
		$post->ID             = 0;
		$post->post_author    = '';
		$post->post_date      = '';
		$post->post_date_gmt  = '';
		$post->post_password  = '';
		$post->post_name      = '';
		$post->post_type      = $post_type;
		$post->post_status    = 'auto-draft';
		$post->to_ping        = '';
		$post->pinged         = '';
		$post->comment_status = get_default_comment_status( $post_type );
		$post->ping_status    = get_default_comment_status( $post_type, 'pingback' );
		$post->page_template  = 'default';
		$post->post_parent    = 0;
		$post->menu_order     = 0;
		$post                 = new WP_Post( $post );
	}

	/**
	 * Filter the default post content initially used in the "Write Post" form.
	 *
	 * @param string $post_content Default post content.
	 * @param WP_Post $post Post object.
	 */
	$post->post_content = apply_filters( 'default_content', $post_content, $post );

	/**
	 * Filter the default post title initially used in the "Write Post" form.
	 *
	 * @param string $post_title Default post title.
	 * @param WP_Post $post Post object.
	 */
	$post->post_title = apply_filters( 'default_title', $post_title, $post );

	/**
	 * Filter the default post excerpt initially used in the "Write Post" form.
	 *
	 * @param string $post_excerpt Default post excerpt.
	 * @param WP_Post $post Post object.
	 */
	$post->post_excerpt = apply_filters( 'default_excerpt', $post_excerpt, $post );

	return $post;
}

/**
 * Get total no. of Posts  posted by a user
 *
 * @param int    $user_id user id.
 * @param string $post_type Post type.
 * @param string $status post status.
 *
 * @return int
 *
 * @todo : may need revisit for caching.
 */
function bblpro_get_user_posts_count( $user_id, $post_type = '', $status = 'publish' ) {

	if ( ! $user_id ) {
		return 0;
	}

	// Needs revisit.
	global $wpdb;

	$where = array();

	$where[] = $wpdb->prepare( 'post_author=%d', $user_id );

	if ( $post_type ) {
		$where[] = $wpdb->prepare( 'post_type=%s', $post_type );
	}

	if ( empty( $status ) || 'any' === $status ) {
		$where[] = $wpdb->prepare( 'post_status != %s', 'auto-draft' );
	} else {
		$where[] = $wpdb->prepare( 'post_status=%s', $status );
	}

	$where_sql = join( ' AND ', $where );

	$query_stmt = "SELECT COUNT('*') FROM {$wpdb->posts} WHERE {$where_sql}";

	$count = $wpdb->get_var( $query_stmt );

	return intval( $count );
}

/**
 * Returns total no. of published post for the user
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return int
 */
function bblpro_get_user_published_posts_count( $user_id, $post_type = '' ) {
	return bblpro_get_user_posts_count( $user_id, $post_type, 'publish' );
}

/**
 * Returns Post submission label.
 *
 * @param string        $post_type post type.
 * @param null| WP_Post $post post object or null.
 *
 * @return mixed|void
 */
function bblpro_get_submit_button_label( $post_type, $post = null ) {

	$label = _x( 'Save', 'Post submission button label', 'buddyblog-pro' );

	$form = bblpro_get_form_for_post_type( $post_type );

	$current_status = 'auto-draft';

	if ( $post ) {
		$current_status = $post->post_status;
	}
	// editing already published post.
	if ( 'publish' === $current_status ) {
		$label = _x( 'Update', 'Post submission button label', 'buddyblog-pro' );

	} elseif ( $form ) {
		$status = bblpro_form_get_post_status( $form->ID );
		if ( 'pending' === $status ) {
			$label = _x( 'Submit for review', 'Post submission button label', 'buddyblog-pro' );
		} elseif ( 'publish' === $status ) {
			$label = _x( 'Publish', 'Post submission button label', 'buddyblog-pro' );
		}
	}

	return apply_filters( 'bblpro_submit_button_label', $label, $form, $post_type );
}

/**
 * Checks if draft button is enabled for the user for the given post type.
 *
 * @param int    $user_id user id.
 * @param string $post_type post type.
 *
 * @return bool
 */
function bblpro_is_draft_button_enabled( $user_id, $post_type ) {

	$is_enabled = bblpro_is_action_enabled( $user_id, $post_type, 'draft' );

	$post = bblpro_get_current_editable_post();

	if ( $is_enabled && $post ) {
		$is_enabled = in_array( $post->post_status, array( 'auto-draft', 'draft' ), true ) ? true : false;
	}

	return $is_enabled;
}

/**
 * Checks if the post has given status.
 *
 * @param WP_Post|null $post post object.
 * @param string       $status post status.
 *
 * @return bool
 */
function bblpro_is_post_status( $post, $status ) {
	return $post && $status === $post->post_status;
}

/**
 * Parses and replaces tokens.
 *
 * @param string   $text text with tokens.
 * @param \WP_Post $post post object.
 *
 * @return string
 */
function bblpro_parse_tokens( $text, $post ) {

	$tokens = array(
		'[site_url]'         => site_url( '/' ),
		'[network_home_url]' => network_home_url( '/' ),
		'[site_name]'        => get_bloginfo( 'name' ),
		'[site_login_url]'   => wp_login_url(),
	);
	// User tokens.
	$user = get_user_by( 'id', $post->post_author );

	if ( $user ) {
		$user_tokens = array(
			'[author_login]'        => $user->user_login,
			'[author_email]'        => $user->user_login,
			'[author_display_name]' => $user->display_name,
			'[author_first_name]'   => $user->first_name,
			'[author_last_name]'    => $user->last_name,
			'[author_url]'          => bp_core_get_user_domain( $user->ID ),
		);

		$tokens = array_merge( $tokens, $user_tokens );
	}

	// Post tokens.
	if ( $post ) {
		$post_type    = $post->post_type;
		$ptype_object = get_post_type_object( $post_type );
		$post_tokens  = array(
			'[post_title]'              => get_the_title( $post ),
			'[post_url]'                => get_permalink( $post ),
			'[post_permalink]'          => get_permalink( $post ),
			'[post_status]'             => ucwords( $post->post_status ),
			'[create_post_url]'         => bblpro_get_post_type_create_tab_url( $user->ID, $post->post_type ),
			'[edit_post_url]'           => bblpro_get_post_edit_url( $post->ID ),
			'[user_post_lists_url]'     => bblpro_get_post_type_tab_url( $post->post_author, $post->post_type ),
			'[post_type]'               => $post_type,
			'[post_type_singular_name]' => $ptype_object ? $ptype_object->labels->singular_name : '',
			'[post_type_name]'          => $ptype_object ? $ptype_object->labels->name : '',

		);

		$tokens = array_merge( $tokens, $post_tokens );
	}

	return str_replace( array_keys( $tokens ), array_values( $tokens ), $text );
}
