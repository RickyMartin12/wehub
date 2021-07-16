<?php

/*
	Advance Portfolio Grid
	By WPBean
	
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


//manage the columns of the "portfolio" post type

function wpb_fp_manage_columns_for_portfolio($columns){
 	
 	//remove columns
    unset($columns['date']);

    //add new columns
    $columns['portfolio_featured_image'] = esc_html__('Portfolio Featured Image','wpb_fp');
    $columns['date'] = esc_html__('Date','wpb_fp');
 
    return $columns;
}
add_action('manage_wpb_fp_portfolio_posts_columns','wpb_fp_manage_columns_for_portfolio');


//Populate custom columns for "portfolio" post type
function wpb_fp_populate_portfolio_columns($column,$post_id){
 
    //featured image column
    if($column == 'portfolio_featured_image'){
        //if this portfolio has a featured image
        if(has_post_thumbnail($post_id)){
            $portfolio_featured_image = get_the_post_thumbnail($post_id,array(100,100));
            echo $portfolio_featured_image;
        }else{
            echo esc_html__('This portfolio has no featured image','wpb_fp'); 
        }
    }
 
}
add_action('manage_wpb_fp_portfolio_posts_custom_column','wpb_fp_populate_portfolio_columns',10,2);