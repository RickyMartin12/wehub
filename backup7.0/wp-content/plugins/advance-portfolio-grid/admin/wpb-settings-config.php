<?php

/*
    WPB Portfolio PRO
    By WPBean
    
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


/**
 * installing setting api class by wpbean
 */
if ( !class_exists('WPB_FP_Settings_Config' ) ):
class WPB_FP_Settings_Config {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WPB_FP_WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }
    
    function admin_menu() {

        add_submenu_page( 
            'edit.php?post_type=wpb_fp_portfolio', 
            esc_html__( 'Portfolio Settings', 'wpb_fp' ),
            esc_html__( 'Portfolio Settings', 'wpb_fp' ),
            'delete_posts',
            'portfolio-settings',
            array( $this, 'plugin_page' )
        ); 

    }
    // setings tabs
    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'wpb_fp_general',
                'title' => esc_html__( 'General Settings', 'wpb_fp' )
            ),
            array(
                'id'    => 'wpb_fp_advanced',
                'title' => esc_html__( 'Advanced Settings', 'wpb_fp' )
            ),
            array(
                'id'    => 'wpb_fp_style',
                'title' => esc_html__( 'Style Settings', 'wpb_fp' )
            ),
            array(
                'id'    => 'wpb_fp_slider',
                'title' => esc_html__( 'Slider Settings', 'wpb_fp' )
            ),
        );

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array( 
            
            'wpb_fp_general' => array(
                array(
                    'name'      => 'wpb_fp_column_',
                    'label'     => esc_html__( 'Columns', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Number of portfolio columns.', 'wpb_fp' ),
                    'type'      => 'select',
                    'default'   => 4,
                    'options'   => array(
                        '2'     => esc_html__( '6 Columns', 'wpb_fp' ),
                        '3'     => esc_html__( '4 Columns', 'wpb_fp' ),
                        '4'     => esc_html__( '3 Columns', 'wpb_fp' ),
                        '6'     => esc_html__( '2 Columns', 'wpb_fp' ),
                        '12'    => esc_html__( '1 Column', 'wpb_fp' ),
                    )
                ),
                array(
                    'name'      => 'wpb_fp_number_of_post_',
                    'label'     => esc_html__( 'Number of post', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Number of post to show. Default -1, means show all.', 'wpb_fp' ),
                    'type'      => 'number',
                    'default'   => -1
                ),
                array(
                    'name'      => 'wpb_fp_number_of_title_character',
                    'label'     => esc_html__( 'Number of Characters in Title', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Number of characters in title to show. Default 16. You have to must check Portfolio Title Character Limit to function this limit.', 'wpb_fp' ),
                    'type'      => 'number',
                    'default'   => 16
                ),
            ),
            'wpb_fp_advanced' => array(
                array(
                    'name'      => 'wpb_post_type_select_',
                    'label'     => esc_html__( 'Post Type', 'wpb_fp' ),
                    'desc'      => esc_html__( 'You can select your own custom post type. Default: Our portfolio post type that come with plugin.', 'wpb_fp' ),
                    'type'      => 'select',
                    'default'   => 'wpb_fp_portfolio',
                    'options'   => wpb_fp_post_type_select(),
                ),
                array(
                    'name'      => 'wpb_taxonomy_select_',
                    'label'     => esc_html__( 'Taxonomy', 'wpb_fp' ),
                    'desc'      => esc_html__( 'You can select your own custom taxonomy ( taxonomy means custom category ).  Default: Our portfolio category that come with plugin.', 'wpb_fp' ),
                    'type'      => 'select',
                    'default'   => 'wpb_fp_portfolio_cat',
                    'options'   => wpb_fp_taxonomy_select(),
                ),
                array(
                    'name'      => 'wpb_fp_cat_exclude_',
                    'label'     => esc_html__( 'Exclude Categories', 'wpb_fp' ),
                    'desc'      => esc_html__( 'You can exclude selected categories from the portfolio.', 'wpb_fp' ),
                    'type'      => 'multicheck',
                    'options'   => wpb_fp_exclude_categories(),
                ),
                array(
                    'name'      => 'wpb_fp_cat_include_',
                    'label'     => esc_html__( 'Include Categories', 'wpb_fp' ),
                    'desc'      => esc_html__( 'You can include selected categories from the portfolio.', 'wpb_fp' ),
                    'type'      => 'multicheck',
                    'options'   => wpb_fp_exclude_categories(),
                ),
                array(
                    'name'      => 'wpb_fp_image_width_',
                    'label'     => esc_html__( 'Image Width', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Portfolio thumbnail width in Px. Minimum 200. Default 480', 'wpb_fp' ),
                    'type'      => 'number',
                    'min'       => 200,
                    'default'   => 480
                ),
                array(
                    'name'      => 'wpb_fp_image_height_',
                    'label'     => esc_html__( 'Image height', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Portfolio thumbnail height in Px. Minimum 200. Default 480', 'wpb_fp' ),
                    'type'      => 'number',
                    'min'       => 200,
                    'default'   => 480
                ),
                array(
                    'name'      => 'wpb_fp_show_overlay_',
                    'label'     => esc_html__( 'Portfolio overlay', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Portfolio overlay on mouse hover. Default: Show.', 'wpb_fp' ),
                    'type'      => 'radio',
                    'default'   => 'show',
                    'options'   => array(
                        'show'  => esc_html__( 'Show', 'wpb_fp' ),
                        'hide'  => esc_html__( 'Hide', 'wpb_fp' ),
                    )
                ),
                array(
                    'name'      => 'wpb_fp_hover_bg',
                    'label'     => esc_html__( 'Portfolio Hover Background Color', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Need hover background color for portfolio items. Default: Yes.', 'wpb_fp' ),
                    'type'      => 'radio',
                    'default'   => 'yes',
                    'options'   => array(
                        'yes'  => esc_html__( 'Yes', 'wpb_fp' ),
                        'no'  => esc_html__( 'No', 'wpb_fp' ),
                    )
                ),
                array(
                    'name'      => 'wpb_fp_show_links_',
                    'label'     => esc_html__( 'Portfolio overlay Links', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Portfolio overlay on mouse hover showing two links. Default: Show.', 'wpb_fp' ),
                    'type'      => 'radio',
                    'default'   => 'show',
                    'options'   => array(
                        'show'  => esc_html__( 'Show', 'wpb_fp' ),
                        'hide'  => esc_html__( 'Hide', 'wpb_fp' ),
                    )
                ),
                array(
                    'name'      => 'wpb_fp_full_grid_link',
                    'label'     => esc_html__( 'Portfolio Full Grid Link', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Link the full grid to the popup. Default: No.', 'wpb_fp' ),
                    'type'      => 'radio',
                    'default'   => 'no',
                    'options'   => array(
                        'yes'  => esc_html__( 'Yes', 'wpb_fp' ),
                        'no'   => esc_html__( 'No', 'wpb_fp' ),
                    )
                ),
                array(
                    'name'      => 'wpb_fp_view_portfolio_btn_text_',
                    'label'     => esc_html__( 'View Portfolio Button Text', 'wpb_fp' ),
                    'desc'      => esc_html__( 'View portfolio button that allow you to link your external site or anything else. You can change that button text.', 'wpb_fp' ),
                    'type'      => 'text',
                    'default'   => esc_html__( 'View Portfolio', 'wpb_fp' ),
                ),
            ),
            'wpb_fp_style' => array(
                array(
                    'name'      => 'wpb_fp_primary_color_',
                    'label'     => esc_html__( 'Primary color', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Select your portfolio primary color. Default: #21cdec', 'wpb_fp' ),
                    'type'      => 'color',
                    'default'   => '#21cdec'
                ),
                array(
                    'name'      => 'wpb_fp_primary_color_hover',
                    'label'     => esc_html__( 'Primary color hover', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Select your portfolio primary hover color. Default: #009cba', 'wpb_fp' ),
                    'type'      => 'color',
                    'default'   => '#009cba'
                ),
                array(
                    'name'      => 'wpb_fp_popup_effect_',
                    'label'     => esc_html__( 'Quick View Effect.', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Select your Quick View Effect popup effect.', 'wpb_fp' ),
                    'type'      => 'select',
                    'default'   => 'mfp-zoom-in',
                    'options'   => array(
                        'mfp-zoom-in'           => esc_html__( 'Zoom effect', 'wpb_fp' ),
                        'mfp-newspaper'         => esc_html__( 'Newspaper effect', 'wpb_fp' ),
                        'mfp-move-horizontal'   => esc_html__( 'Move-horizontal effect', 'wpb_fp' ),
                        'mfp-move-from-top'     => esc_html__( 'Move-from-top effect', 'wpb_fp' ),
                        'mfp-3d-unfold'         => esc_html__( '3d unfold', 'wpb_fp' ),
                        'mfp-zoom-out'          => esc_html__( 'Zoom-out effect', 'wpb_fp' ),
                    ),
                ),
                array(
                    'name'      => 'wpb_fp_hover_effect_',
                    'label'     => esc_html__( 'Hover Effect.', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Select an effect for mouse hover on portfolio.', 'wpb_fp' ),
                    'type'      => 'select',
                    'default'   => 'effect-oscar',
                    'options'   => array(
                        'effect-roxy'     => esc_html__( 'Roxy', 'wpb_fp' ),
                        'effect-bubba'    => esc_html__( 'Bubba', 'wpb_fp' ),
                        'effect-marley'   => esc_html__( 'Marley', 'wpb_fp' ),
                        'effect-oscar'    => esc_html__( 'Oscar', 'wpb_fp' ),
                        'effect-layla'    => esc_html__( 'Layla', 'wpb_fp' ),
                    ),
                ),
                array(
                    'name'      => 'wpb_fp_title_font_size_',
                    'label'     => esc_html__( 'Portfolio title font size.', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Font size for portfolio title. Default 20px.', 'wpb_fp' ),
                    'type'      => 'number',
                    'default'   => 20
                ),
                array(
                    'name'      => 'wpb_fp_qv_max_width',
                    'label'     => esc_html__( 'Quick View Max Width', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Quick View LightBox Max Width. Default 980px.', 'wpb_fp' ),
                    'type'      => 'number',
                    'default'   => 980
                ),
                array(
                    'name' => 'wpb_fp_custom_css_',
                    'label' => esc_html__( 'Portfolio Custom CSS', 'wpb_fp' ),
                    'desc' => esc_html__( 'You can write you own custom css code here.', 'wpb_fp' ),
                    'type' => 'textarea',
                    'rows' => 8
                ),

            ),

            'wpb_fp_slider' => array(
                array(
                    'name'  => 'wpb_fp_enable_slider',
                    'label' => esc_html__( 'Enable Slider', 'wpb_fp' ),
                    'desc'  => esc_html__( 'Check this to enable the portfolio slider', 'wpb_fp' ),
                    'type'  => 'checkbox'
                ),
                array(
                    'name'      => 'wpb_fp_autoplay',
                    'label'     => esc_html__( 'Slider Autoplay', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Check this to enable the slider autoplay', 'wpb_fp' ),
                    'type'      => 'checkbox',
                    'default'   => 'on',
                ),
                array(
                    'name'      => 'wpb_fp_loop',
                    'label'     => esc_html__( 'Slider Loop', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Check this to enable the slider loop', 'wpb_fp' ),
                    'type'      => 'checkbox'
                ),
                array(
                    'name'      => 'wpb_fp_navigation',
                    'label'     => esc_html__( 'Slider Navigation', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Check this to enable the slider navigation', 'wpb_fp' ),
                    'type'      => 'checkbox',
                    'default'   => 'on',
                ),
                array(
                    'name'      => 'wpb_fp_pagination',
                    'label'     => esc_html__( 'Slider Pagination', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Check this to enable the slider pagination', 'wpb_fp' ),
                    'type'      => 'checkbox',
                    'default'   => 'on',
                ),
                array(
                    'name'      => 'wpb_fp_margin',
                    'label'     => esc_html__( 'Slider Margin', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Space between slider items. Default 15px.', 'wpb_fp' ),
                    'type'      => 'number',
                    'default'   => 15
                ),
                array(
                    'name'      => 'wpb_fp_items',
                    'label'     => esc_html__( 'Slider Column', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Slider column. Default 3.', 'wpb_fp' ),
                    'type'      => 'number',
                    'default'   => 3
                ),
                array(
                    'name'      => 'wpb_fp_items_tablet',
                    'label'     => esc_html__( 'Slider Column in Tablet', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Slider column in tablet. Default 2.', 'wpb_fp' ),
                    'type'      => 'number',
                    'default'   => 2
                ),
                array(
                    'name'      => 'wpb_fp_items_mobile',
                    'label'     => esc_html__( 'Slider Column in Mobile', 'wpb_fp' ),
                    'desc'      => esc_html__( 'Slider column in mobile. Default 1.', 'wpb_fp' ),
                    'type'      => 'number',
                    'default'   => 1
                ),
            ),
        );
        return $settings_fields;
    }
    
    // warping the settings
    function plugin_page() {
        ?>
            <?php do_action ( 'wpb_fp_before_settings' ); ?>
            <div class="wpb_fp_settings_area">
                <div class="wrap wpb_fp_settings">
                    <?php
                        $this->settings_api->show_navigation();
                        $this->settings_api->show_forms();
                    ?>
                </div>
                <div class="wpb_fp_settings_content">
                    <?php do_action ( 'wpb_fp_settings_content' ); ?>
                </div>
            </div>
            <?php do_action ( 'wpb_fp_after_settings' ); ?>
        <?php
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }
        return $pages_options;
    }
}
endif;

$settings = new WPB_FP_Settings_Config();


//--------- trigger setting api class---------------- //

function wpb_fp_get_option( $option, $section, $default = '' ) {
 
    $options = get_option( $section );
 
    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }
 
    return $default;
}