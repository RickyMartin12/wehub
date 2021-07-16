<?php

/*
    Advance Portfolio Grid
    By WPBean
    
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 



if ( ! function_exists('wpb_fp_post_type_select') ) {

	// Getting all custom post type avaiable for portfolio plugin

	function wpb_fp_post_type_select(){

		$args = array(
		   	'public'   => true,
   			'_builtin' => false
		);

		$rerutn_object = get_post_types( $args );
		//$rerutn_object['post'] = 'Post';

		return $rerutn_object;
	}

}

if ( ! function_exists('wpb_fp_taxonomy_select') ) {

	// Getting all custom taxonomy avaiable for portfolio plugin

	function wpb_fp_taxonomy_select(){
		$taxonomy = array();
		$wpb_fp_post_type = wpb_fp_get_option( 'wpb_post_type_select_', 'wpb_fp_advanced', 'wpb_fp_portfolio' );
		$taxonomy_objects = get_object_taxonomies( $wpb_fp_post_type, 'objects' );
		foreach ($taxonomy_objects as $taxonomy_object) {
			$taxonomy[$taxonomy_object->name] = $taxonomy_object->label;
		}

		return $taxonomy;
	}

}


if ( ! function_exists('wpb_fp_exclude_categories') ) {

	// Exclude selected categiry form portfolio.

	function wpb_fp_exclude_categories(){
		$terms = $category_link = array();
		$wpb_fp_post_type = wpb_fp_get_option( 'wpb_post_type_select_', 'wpb_fp_advanced', 'wpb_fp_portfolio' );
		$taxonomy_objects = get_object_taxonomies( $wpb_fp_post_type, 'objects' );

		if( isset($taxonomy_objects) && !empty($taxonomy_objects) ){
		 	$wpb_fp_taxonomy = wpb_fp_get_option( 'wpb_taxonomy_select_', 'wpb_fp_advanced', 'wpb_fp_portfolio_cat' );
		    $terms = get_terms($wpb_fp_taxonomy);
		    foreach ( $terms as $term ) {
		        $category_link[$term->term_id] =  esc_html( $term->name ) . ' ('. esc_html( $term->term_id ) .')';
		    }                                         
	      
	    }
	    return $category_link;
	}

}



/**
 * WPBean Socail Info for Plugin settings page
 */

add_action( 'wpb_fp_after_settings','wpb_wpbean_socail_info' );

if( !function_exists('wpb_wpbean_socail_info') ){
	function wpb_wpbean_socail_info(){
		?>
		<div class="wpb_wpbean_socials">
			<h3><?php esc_html_e( 'For getting updates of our plugins, features update, WordPress new trend, New web technology, etc. Follows Us.', 'wpb_fp' ) ?></h3>
			<a href="https://twitter.com/wpbean" title="<?php esc_html_e( 'Twitter', 'wpb_fp' ) ?>" class="wpb_twitter" target="_blank"><?php esc_html_e( 'Twitter', 'wpb_fp' ) ?></a>
			<a href="https://www.facebook.com/wpbean" title="<?php esc_html_e( 'FaceBook', 'wpb_fp' ) ?>" class="wpb_facebook" target="_blank"><?php esc_html_e( 'Facebook', 'wpb_fp' ) ?></a>
			<a href="https://www.youtube.com/user/wpbean/videos" title="<?php esc_html_e( 'Youtube', 'wpb_fp' ) ?>" class="wpb_youtube" target="_blank"><?php esc_html_e( 'Youtube', 'wpb_fp' ) ?></a>
		</div>
		<?php
	}
}



/**
 * PRO version Info
 */

add_action( 'wpb_fp_settings_content', 'wpb_fp_pro_version_info' );
if( !function_exists( 'wpb_fp_pro_version_info' ) ){
	function wpb_fp_pro_version_info(){
		?>
		<h3><?php esc_html_e( 'PRO Version Features', 'wpb_fp' ) ?></h3>
		<ul>
			<li><?php esc_html_e( 'Advance filtering option with awesome effects.', 'wpb_fp' ) ?></li>
			<li><?php esc_html_e( 'Video support, both on grid and quick view popup.', 'wpb_fp' ) ?></li>
			<li><?php esc_html_e( 'Image gallery for each portfolio, gallery image slider in quick view popup.', 'wpb_fp' ) ?></li>
			<li><?php esc_html_e( 'Advance settings for developers. Easy to use with any theme. Custom css support.', 'wpb_fp' ) ?></li>
			<li><?php esc_html_e( 'Easy to install and video documentation.', 'wpb_fp' ) ?></li>
			<li><?php esc_html_e( 'You can use your own custom post type and taxonomy.', 'wpb_fp' ) ?></li>
			<li><?php esc_html_e( 'Category exclude feature.', 'wpb_fp' ) ?></li>
		</ul>
		<a class="wpb_get_pro_btn" href="http://bit.ly/1RQQsV6" target="_blank"><?php esc_html_e( 'Get The Pro Version', 'wpb_fp' ) ?></a>
		<?php
	}
}

