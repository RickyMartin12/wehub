<?php

/*
	Advance Portfolio Grid
	By WPBean
	
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


// Register Custom Post Type for portfolio
	
if ( ! function_exists('wpb_fp_post_type') ) {
function wpb_fp_post_type() {

	$labels = array(
		'name'                => esc_html_x( 'Portfolios', 'Post Type General Name', 'wpb_fp' ),
		'singular_name'       => esc_html_x( 'Portfolio', 'Post Type Singular Name', 'wpb_fp' ),
		'menu_name'           => esc_html__( 'Portfolio', 'wpb_fp' ),
		'parent_item_colon'   => esc_html__( 'Parent Portfolio:', 'wpb_fp' ),
		'all_items'           => esc_html__( 'All Portfolios', 'wpb_fp' ),
		'view_item'           => esc_html__( 'View Portfolio', 'wpb_fp' ),
		'add_new_item'        => esc_html__( 'Add New Portfolio', 'wpb_fp' ),
		'add_new'             => esc_html__( 'Add New', 'wpb_fp' ),
		'edit_item'           => esc_html__( 'Edit Portfolio', 'wpb_fp' ),
		'update_item'         => esc_html__( 'Update Portfolio', 'wpb_fp' ),
		'search_items'        => esc_html__( 'Search Portfolio', 'wpb_fp' ),
		'not_found'           => esc_html__( 'Not found', 'wpb_fp' ),
		'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'wpb_fp' ),
	);
	$rewrite = array(
		'slug'                => apply_filters( 'wpb_fp_post_type_slug', 'portfolio-items' ),
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => esc_html__( 'Portfolio', 'wpb_fp' ),
		'description'         => esc_html__( 'Portfolio Grid plugin post type', 'wpb_fp' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'show_in_rest'	      => true,
		'taxonomies'          => array( 'wpb_fp_portfolio_cat' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 75,
		'menu_icon'           => 'dashicons-portfolio',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'wpb_fp_portfolio', $args );

}

// Hook into the 'init' action
add_action( 'init', 'wpb_fp_post_type', 0 );

}



// Register Theme Features (feature image for portfolio)

if ( ! function_exists('wpb_fp_theme_support') ) {

function wpb_fp_theme_support()  {

	// Add theme support for Featured Images
	add_theme_support( 'post-thumbnails', array( 'wpb_fp_portfolio' ) );
}

// Hook into the 'after_setup_theme' action
add_action( 'after_setup_theme', 'wpb_fp_theme_support' );

}


// Register Custom Taxonomy for Portfolio

if ( ! function_exists( 'wpb_fp_taxonomy' ) ) {
function wpb_fp_taxonomy() {

	$labels = array(
		'name'                       => esc_html_x( 'Portfolio Categories', 'Taxonomy General Name', 'wpb_fp' ),
		'singular_name'              => esc_html_x( 'Portfolio Category', 'Taxonomy Singular Name', 'wpb_fp' ),
		'menu_name'                  => esc_html__( 'Portfolio Category', 'wpb_fp' ),
		'all_items'                  => esc_html__( 'All Categories', 'wpb_fp' ),
		'parent_item'                => esc_html__( 'Parent Category', 'wpb_fp' ),
		'parent_item_colon'          => esc_html__( 'Parent Category:', 'wpb_fp' ),
		'new_item_name'              => esc_html__( 'New Category Name', 'wpb_fp' ),
		'add_new_item'               => esc_html__( 'Add New Category', 'wpb_fp' ),
		'edit_item'                  => esc_html__( 'Edit Category', 'wpb_fp' ),
		'update_item'                => esc_html__( 'Update Category', 'wpb_fp' ),
		'separate_items_with_commas' => esc_html__( 'Separate categories with commas', 'wpb_fp' ),
		'search_items'               => esc_html__( 'Search categories', 'wpb_fp' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove Categories', 'wpb_fp' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used categories', 'wpb_fp' ),
		'not_found'                  => esc_html__( 'Not Found', 'wpb_fp' ),
	);
	$rewrite = array(
		'slug'                       => 'portfolio-category',
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'rewrite'                    => $rewrite,
		'show_in_rest'				 => true,
	);
	register_taxonomy( 'wpb_fp_portfolio_cat', array( 'wpb_fp_portfolio' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'wpb_fp_taxonomy', 0 );

}


/**
 *  Filtering Posts by Taxonomies in the Dashboard
 */

function wpb_fp_filter_cars_by_taxonomies( $post_type, $which ) {

	// Apply this only on a specific post type
	if ( 'wpb_fp_portfolio' !== $post_type )
		return;

	// A list of taxonomy slugs to filter by
	$taxonomies = array( 'wpb_fp_portfolio_cat' );

	foreach ( $taxonomies as $taxonomy_slug ) {

		// Retrieve taxonomy data
		$taxonomy_obj = get_taxonomy( $taxonomy_slug );
		$taxonomy_name = $taxonomy_obj->labels->name;

		// Retrieve taxonomy terms
		$terms = get_terms( $taxonomy_slug );

		// Display filter HTML
		echo "<select name='{$taxonomy_slug}' id='{$taxonomy_slug}' class='postform'>";
		echo '<option value="">' . sprintf( esc_html__( 'Show All %s', 'wpb_fp' ), $taxonomy_name ) . '</option>';
		foreach ( $terms as $term ) {
			printf(
				'<option value="%1$s" %2$s>%3$s (%4$s)</option>',
				$term->slug,
				( ( isset( $_GET[$taxonomy_slug] ) && ( $_GET[$taxonomy_slug] == $term->slug ) ) ? ' selected="selected"' : '' ),
				$term->name,
				$term->count
			);
		}
		echo '</select>';
	}

}
add_action( 'restrict_manage_posts', 'wpb_fp_filter_cars_by_taxonomies' , 10, 2);