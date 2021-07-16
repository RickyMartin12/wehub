<?php

/*
	Advance Portfolio Grid
	By WPBean
	
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

/**
 * PHP implode with key and value ( Owl carousel data attr )
 */

if( !function_exists('wpb_fp_data_attr_implode') ){
	function wpb_fp_data_attr_implode( $array ){
		
		foreach ($array as $key => $value) {

			if( isset($value) && $value != '' ){
				$output[] = $key . '=' . '"' . esc_attr( $value ) . '"' ;
			}
		}

        return implode( ' ', $output );
	}
}


/* ==========================================================================
   Shortcode For this plugin
   ========================================================================== */


function wpb_fp_shortcode_funcation( $atts ){

	$cat_excludes = wpb_fp_get_option( 'wpb_fp_cat_exclude_', 'wpb_fp_advanced', array() );
	$cat_include  = wpb_fp_get_option( 'wpb_fp_cat_include_', 'wpb_fp_advanced', array() );

	extract(shortcode_atts(array(
		'orderby'				=> 'date', // portfolio orderby
		'order'					=> 'DESC', // portfolio order
		'exclude'				=> ( isset($cat_excludes) && !empty($cat_excludes) ? implode(',', $cat_excludes ) : '' ), // comma separated portfolio category ids
		'include'				=> ( isset($cat_include) && !empty($cat_include) ? implode(',', $cat_include ) : '' ), // comma separated portfolio category ids
		'column'				=> wpb_fp_get_option( 'wpb_fp_column_', 'wpb_fp_general', 4 ), // 2, 3, 4, 6
		'show_quickview_btn'	=> 'on', //off
		'show_details_btn'		=> 'on', //off
		'type'					=> 'grid', //slider

		'autoplay'				=> wpb_fp_get_option( 'wpb_fp_autoplay', 'wpb_fp_slider', 'on' ),
		'loop'					=> wpb_fp_get_option( 'wpb_fp_loop', 'wpb_fp_slider', '' ),
		'items'					=> wpb_fp_get_option( 'wpb_fp_items', 'wpb_fp_slider', 3 ),
		'tablet'				=> wpb_fp_get_option( 'wpb_fp_items_tablet', 'wpb_fp_slider', 2 ),
		'mobile'				=> wpb_fp_get_option( 'wpb_fp_items_mobile', 'wpb_fp_slider', 1 ),
		'navigation'			=> wpb_fp_get_option( 'wpb_fp_navigation', 'wpb_fp_slider', 'on' ),
		'pagination'			=> wpb_fp_get_option( 'wpb_fp_pagination', 'wpb_fp_slider', 'on' ),
		'margin'				=> wpb_fp_get_option( 'wpb_fp_margin', 'wpb_fp_slider', 15 ),

		'posts'					=> wpb_fp_get_option( 'wpb_fp_number_of_post_', 'wpb_fp_general', -1 ),
		'post_type'				=> wpb_fp_get_option( 'wpb_post_type_select_', 'wpb_fp_advanced', 'wpb_fp_portfolio' ),
		'taxonomy'				=> wpb_fp_get_option( 'wpb_taxonomy_select_', 'wpb_fp_advanced', 'wpb_fp_portfolio_cat' ),
		'hover_effect'			=> wpb_fp_get_option( 'wpb_fp_hover_effect_', 'wpb_fp_style', 'effect-oscar' ),
	), $atts));

	if( isset($_GET['wpb_hover_effect']) ) {
		$hover_effect = $_GET['wpb_hover_effect'];
	}

	$wpb_fp_number_of_title_character 	= wpb_fp_get_option( 'wpb_fp_number_of_title_character', 'wpb_fp_general', 16 );
	$wpb_fp_image_width 				= wpb_fp_get_option( 'wpb_fp_image_width_', 'wpb_fp_advanced', 480 );
	$wpb_fp_image_height 				= wpb_fp_get_option( 'wpb_fp_image_height_', 'wpb_fp_advanced', 480 );
	$wpb_fp_show_overlay 				= wpb_fp_get_option( 'wpb_fp_show_overlay_', 'wpb_fp_advanced', 'show' );
	$wpb_fp_show_links 					= wpb_fp_get_option( 'wpb_fp_show_links_', 'wpb_fp_advanced', 'show' );
	$wpb_fp_full_grid_link 				= wpb_fp_get_option( 'wpb_fp_full_grid_link', 'wpb_fp_advanced', 'no' );
	$wpb_fp_popup_effect 				= wpb_fp_get_option( 'wpb_fp_popup_effect_', 'wpb_fp_style', 'mfp-zoom-in' );


	$args = array(
		'post_type' 		=> $post_type,
		'posts_per_page'	=> $posts,
		'orderby' 			=> $orderby,
		'order' 			=> $order,
	);

	// Exclude selected categories from the portfolio.

	if( $exclude && $exclude != '' ){

		$args['tax_query'][] = array(
			'taxonomy' 	=> $taxonomy,
	        'field'    	=> 'id',
			'terms'    	=> explode(',', $exclude),
	        'operator' 	=> 'NOT IN' 
		);
	}

	// Include selected categories from the portfolio.

	if( $include && $include != '' ){

		$args['tax_query'][] = array(
			'taxonomy' 	=> $taxonomy,
	        'field'    	=> 'id',
			'terms'    	=> explode(',', $include),
	        'operator' 	=> 'IN' 
		);
	}

	if( $type == 'slider' ){
		$portfolio_class 	= 'wpb_fp_slider owl-carousel owl-theme';
		$column_class 		= 'wpb_fp_slider_item';
	}else{
		$portfolio_class 	= 'wpb_fp_row wpb_fp_grid';
		$column_class 		= apply_filters( 'wpb_fp_portfolio_column_class', 'wpb_fp_col-md-'. esc_attr( $column ) .' wpb_fp_col-sm-6 wpb_fp_col-xs-12' );
	}

	$portfolio_class .= ' wpb-portfolio-hover-bg-' . wpb_fp_get_option( 'wpb_fp_hover_bg', 'wpb_fp_advanced', 'yes' );
	$portfolio_class .= ' wpb-portfolio-show-overly-' . $wpb_fp_show_overlay;
	$portfolio_class .= ' wpb-portfolio-full-grid-link-' . $wpb_fp_full_grid_link;


	$enable_slider = wpb_fp_get_option( 'wpb_fp_enable_slider', 'wpb_fp_slider' );

	$slider_attr = array(
    	'data-autoplay' 	=> ( $autoplay == 'on' ? 'true' : 'false' ),
    	'data-loop' 	    => ( $loop == 'on' ? 'true' : 'false' ),
    	'data-items' 		=> $items,
    	'data-tablet' 		=> $tablet,
    	'data-mobile' 		=> $mobile,
    	'data-navigation' 	=> ( $navigation == 'on' ? 'true' : 'false' ),
    	'data-pagination' 	=> ( $pagination == 'on' ? 'true' : 'false' ),
    	'data-margin' 		=> $margin,
    	'data-direction' 	=> ( is_rtl() ? 'true' : 'false' ),
    );

	$loop = new WP_Query( $args );

	if ( $loop->have_posts() ) {
		$output = '<div class="wpb_portfolio_area">';

		if($type == 'slider' && $enable_slider != 'on'){
			$output .= '<div class="wpb_fp_notice wpb_fp_notice_error">'. esc_html__( 'Please, enable the slider from the settings.', 'wpb_fp' ) .'</div>';
		}

		$output .= '<div class="wpb_portfolio '.$portfolio_class.'" '. (  $type == 'slider' ? wpb_fp_data_attr_implode( $slider_attr ) : '' ) .'>';

		while ( $loop->have_posts() ) : $loop->the_post();

			$image_thumb 				= wpb_fp_aq_resize( wp_get_attachment_url( get_post_thumbnail_id() ), $wpb_fp_image_width, $wpb_fp_image_height, true, true, true );
			$thumbnail_mata 			= get_post_meta(get_post_thumbnail_id() ,'_wp_attachment_image_alt', true);
			$wpb_fp_portfolio_ex_link 	= get_post_meta( get_the_id(), 'wpb_fp_portfolio_ex_link', true );
			$portfolio_title 			= get_the_title();
			$portfolio_title 			= ( strlen($portfolio_title) > $wpb_fp_number_of_title_character + 2 ) ? substr($portfolio_title, 0, $wpb_fp_number_of_title_character ) . '...' : $portfolio_title;
			$qv_image_src 				= wp_get_attachment_image_src( get_post_thumbnail_id(), apply_filters( 'wpb_fp_quick_view_image_size', 'medium_large' ) );

			$output .= '<div class="'.$column_class.' wpb_portfolio_post">';
			$output .= '<figure class="'. esc_attr( $hover_effect ) .'">';
			$output .= '<img src="'. esc_url( $image_thumb ) .'" '. ($thumbnail_mata ? 'alt="'. esc_html( $thumbnail_mata ) .'"' : '') .' />';
			if( isset($wpb_fp_show_overlay) && $wpb_fp_show_overlay == 'show' ):
				$output .= '<figcaption>';
				$output .= '<div>';
				$output .= '<h2>'. esc_html( $portfolio_title ) .'</h2>';
				if( isset($wpb_fp_show_links) && $wpb_fp_show_links == 'show' ):
					$output .= '<p class="wpb_fp_icons">';
					if( $show_quickview_btn == 'on' ){
						$output .= '<a class="wpb_fp_preview open-popup-link" href="#" data-mfp-src="#wpb_fp_quick_view_'. esc_attr( get_the_id() ) .'" data-effect="'. esc_attr( $wpb_fp_popup_effect ) .'"><i class="wpbfpicons-eye"></i></a>';
					}
					if( $show_details_btn == 'on' ){
						if( isset($wpb_fp_portfolio_ex_link) && !empty($wpb_fp_portfolio_ex_link) ){
							$output .= '<a class="wpb_fp_link" href="'. esc_url( $wpb_fp_portfolio_ex_link ) .'" target="_blank"><i class="wpbfpicons-link"></i></a>';
						}else{
							$output .= '<a class="wpb_fp_link" href="'. esc_url( get_the_permalink() ) .'"><i class="wpbfpicons-link"></i></a>';
						}
					}
					$output .= '</p>';
				endif;
				$output .= '</div>';
				if( $wpb_fp_full_grid_link == 'yes' ){
					$output .= '<a class="wpb_fp_preview wpb_fp_full_grid_link open-popup-link" href="#" data-mfp-src="#wpb_fp_quick_view_'. esc_attr( get_the_id() ) .'" data-effect="'. esc_attr( $wpb_fp_popup_effect ) .'"></a>';
				}
				$output .= '</figcaption>';
			endif;
			if( $wpb_fp_full_grid_link == 'yes' && $wpb_fp_show_overlay == 'hide' ){
				$output .= '<a class="wpb_fp_preview wpb_fp_full_grid_link wpb-portfolio-no-overly open-popup-link" href="#" data-mfp-src="#wpb_fp_quick_view_'. esc_attr( get_the_id() ) .'" data-effect="'. esc_attr( $wpb_fp_popup_effect ) .'"></a>';
			}
			$output .= '</figure>';
				// Quick view
				$output .= '<div id="wpb_fp_quick_view_'. esc_attr( get_the_id() ) .'" class="white-popup mfp-hide mfp-with-anim wpb_fp_quick_view">';
				$output .= '<div class="wpb_fp_row">';
				$output .= '<div class="wpb_fp_quick_view_img wpb_fp_col-md-6 wpb_fp_col-sm-12">';
				if( isset($qv_image_src) && !empty($qv_image_src) ){
					$output .= '<img src="'. esc_url( $qv_image_src[0] ) .'" '. ($thumbnail_mata ? 'alt="'. esc_html( $thumbnail_mata ) .'"' : '') .' />';
				}
				$output .= '</div>';
				$output .= '<div class="wpb_fp_quick_view_content wpb_fp_col-md-6 wpb_fp_col-sm-12">';
				$output .= '<h2>'. esc_html( get_the_title() ) .'</h2>';
				$output .= wpautop( apply_filters( 'the_content', get_the_content() ) );
				if( isset($wpb_fp_portfolio_ex_link) && !empty($wpb_fp_portfolio_ex_link) ){
					$output .= '<a class="wpb_fp_btn" href="'. esc_url( $wpb_fp_portfolio_ex_link ) .'" target="_blank">'. esc_html( wpb_fp_get_option( 'wpb_fp_view_portfolio_btn_text_', 'wpb_fp_advanced', esc_html__( 'View Portfolio', 'wpb_fp' ) ) ) .'</a>';
				}
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				// quick view end
			$output .= '</div>';
			
		endwhile;

		$output .= '</div><!-- wpb_portfolio -->';
		$output .= '</div><!-- wpb_portfolio_area -->';
		do_action('wpb_fp_after_portfolio');
	} else {
		$output = esc_html__( 'No portfolio found', 'wpb_fp' );
	}
	wp_reset_postdata();
	return $output;
}
add_shortcode( 'wpb-portfolio','wpb_fp_shortcode_funcation' );	




