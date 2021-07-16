<?php

/*
	Advance Portfolio Grid
	By WPBean
	
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


/**
 * Enqueue Scripts
 */

function wpb_fp_styles() {

	// Slider
	$enable_slider = wpb_fp_get_option( 'wpb_fp_enable_slider', 'wpb_fp_slider', 'on' );

	if( $enable_slider && $enable_slider == 'on' ){
		wp_enqueue_script('wpb-fp-owl-carousel', plugins_url('../assets/js/owl.carousel.min.js', __FILE__), array('jquery'), '2.3.4', false);
		wp_enqueue_style('wpb-fp-owl-carousel', plugins_url('../assets/css/owl.carousel.min.css', __FILE__), '', '2.3.4');
	}


	wp_enqueue_script('wpb-fp-magnific-popup', plugins_url('../assets/js/jquery.magnific-popup.min.js', __FILE__), array('jquery'), '1.1', false);
	wp_enqueue_script('wpb-fp-main', plugins_url('../assets/js/main.js', __FILE__), array('jquery'), '1.0', false);

	wp_enqueue_style('wpb-fp-bootstrap-grid', plugins_url('../assets/css/wpb-custom-bootstrap.css', __FILE__), '', '3.2');
	wp_enqueue_style('wpb-fp-magnific-popup', plugins_url('../assets/css/magnific-popup.css', __FILE__), '', '1.0');
	wp_enqueue_style('wpb-fp-main', plugins_url('../assets/css/main.css', __FILE__), '', '1.0');


	$custom_style 			= wpb_fp_get_option( 'wpb_fp_custom_css_', 'wpb_fp_style', '' );
	$primary_color 			= wpb_fp_get_option( 'wpb_fp_primary_color_', 'wpb_fp_style', '#DF6537' );
	$primary_color_hover 	= wpb_fp_get_option( 'wpb_fp_primary_color_hover', 'wpb_fp_style', '#009cba' );
	$title_font_size 		= wpb_fp_get_option( 'wpb_fp_title_font_size_', 'wpb_fp_style', 20 );
	$qv_max_width 			= wpb_fp_get_option( 'wpb_fp_qv_max_width', 'wpb_fp_style', 980 );

	$custom_style .= ".wpb-fp-filter li:hover, 
	.wpb_portfolio .wpb_fp_icons .wpb_fp_preview i,
	.wpb_fp_quick_view_content .wpb_fp_btn:hover {
		color: {$primary_color};
	}";

	$custom_style .= ".tooltipster-punk, 
	.wpb_fp_filter_default li:hover,
	.wpb_fp_quick_view_content .wpb_fp_btn:hover,
	.wpb_fp_quick_view_content .wpb_fp_btn {
		border-color: {$primary_color};
	}";

	$custom_style .= ".wpb_portfolio .wpb_fp_icons .wpb_fp_link i,
	.wpb_fp_btn,
	.wpb_fp_filter_capsule li.active,
	#wpb_fp_filter_select,
	#wpb_fp_filter_select #wpb-fp-sort-portfolio,
	#wpb_fp_filter_select li,
	.wpb_fp_slider.owl-carousel .owl-nav button {
		background: {$primary_color};
	}";

	$custom_style .= ".wpb_fp_slider.owl-carousel .owl-nav button:hover, .wpb_fp_slider.owl-carousel .owl-nav button:focus,
	.wpb_fp_slider.owl-theme .owl-dots .owl-dot span, .wpb_fp_slider.owl-theme .owl-dots .owl-dot.active span, .wpb_fp_slider.owl-theme .owl-dots .owl-dot:hover span {
		background: {$primary_color_hover};
	}";

	$custom_style .= ".wpb_fp_grid figure h2 {
		font-size: {$title_font_size}px;
	}";

	$custom_style .= ".wpb_fp_quick_view.white-popup {
		max-width: {$qv_max_width}px;
	}";

	wp_add_inline_style( 'wpb-fp-main', $custom_style );
}
add_action( 'wp_enqueue_scripts', 'wpb_fp_styles' );


/**
 * Enqueue CSS For Admin
 */

function wpb_fp_admin_adding_style() {
	$screen = get_current_screen();
	$wpb_post_type_select = wpb_fp_get_option( 'wpb_post_type_select_', 'wpb_fp_advanced', 'wpb_fp_portfolio' );

	if( $screen->id == 'wpb_fp_portfolio_page_portfolio-settings' || $screen->id == $wpb_post_type_select ){
		wp_enqueue_style('wpb_wrps_admin_style', plugins_url('../assets/css/admin-style.css', __FILE__),'','1.0', false);
	}
}
add_action( 'admin_enqueue_scripts', 'wpb_fp_admin_adding_style',11 );