<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.

class WPB_FP_Widget_Portfolio extends Widget_Base {

	public function get_name() {
		return 'wpb_portfolio';
	}

	public function get_title() {
		return esc_html__( 'Advance Portfolio Grid and Slider', 'wpb_fp' );
	}

	public function get_icon() {
		return 'eicon-gallery-justified';
	}

  public function is_reload_preview_required(){
    return true;
  }

	public function get_categories() {
		return [ 'wpb_widgets' ];
	}

	protected function _register_controls() {

    $this->start_controls_section(
      'wpb_fp_settings',
      [
        'label' => esc_html__( 'Portfolio Settings', 'wpb_fp' )
      ]
    );

    $this->add_control(
        'column_settings',
        [
            'label'         => esc_html__( 'Columns', 'wpb_fp' ),
            'type'          => Controls_Manager::SELECT,
            'default'       => 3,              
            'options'       => [
                2           => esc_html__( '6 Columns', 'wpb_fp' ),
                3           => esc_html__( '4 Columns', 'wpb_fp' ),
                4           => esc_html__( '3 Columns', 'wpb_fp' ),
                6           => esc_html__( '2 Columns', 'wpb_fp' ),
                12          => esc_html__( '1 Columns', 'wpb_fp' )
            ]              
        ]
    );

    $this->add_control(
        'wpb_fp_posts',
        [
            'label'         => esc_html__( 'Count', 'wpb_fp' ),
            'description'   => esc_html__('Number of portfolios to show. Default 16.', 'wpb_fp'),
            'type'          => Controls_Manager::SLIDER,
            'default'       => [
              'size'      => 16,
            ],
            'range'         => [
              'px'        => [
                'min'   => 1,
                'max'   => 50,
              ],
            ],
        ]
    );

    $this->add_control(
      'order',
      [
          'type'          => Controls_Manager::SELECT,
          'label'         => esc_html__( 'Order', 'wpb_fp' ),
          'default'       => 'DESC',
          'description'   => esc_html__('Portfolio Order', 'wpb_fp'),
          'options'       => [
              'ASC'       => esc_html__( 'Ascending', 'wpb_fp' ),
              'DESC'      => esc_html__( 'Descending', 'wpb_fp' )
          ],
      ]
    );        

    $this->add_control(
      'order_by',
      [
          'type'          => Controls_Manager::SELECT,
          'label'         => esc_html__( 'Order By', 'wpb_fp' ),
          'default'       => 'date',
          'description'   => esc_html__('Portfolio OrderBy', 'wpb_fp'),
          'options'       => [
              'none'          => esc_html__('No order', 'wpb_fp' ),
              'ID'            => esc_html__('Post ID', 'wpb_fp' ),
              'author'        => esc_html__('Author', 'wpb_fp' ),
              'title'         => esc_html__('Title', 'wpb_fp' ),
              'date'          => esc_html__('Published date', 'wpb_fp' ),
              'modified'      => esc_html__('Modified date', 'wpb_fp' ),
              'parent'        => esc_html__('By parent', 'wpb_fp' ),
              'rand'          => esc_html__('Random order', 'wpb_fp' ),
              'comment_count' => esc_html__('Comment count', 'wpb_fp' ),
              'menu_order'    => esc_html__('Menu order', 'wpb_fp' ),
              'post__in'      => esc_html__('By include order', 'wpb_fp' )
          ],
      ]
    );

    $this->add_control(
        'hover_effect',
        [
            'label'         => esc_html__( 'Hover Effect', 'wpb_fp' ),
            'type'          => Controls_Manager::SELECT,
            'default'       => 'effect-oscar',              
            'options'       => [
                'effect-roxy'     => esc_html__( 'Roxy', 'wpb_fp' ),
                'effect-bubba'    => esc_html__( 'Bubba', 'wpb_fp' ),
                'effect-marley'   => esc_html__( 'Marley', 'wpb_fp' ),
                'effect-oscar'    => esc_html__( 'Oscar', 'wpb_fp' ),
                'effect-layla'    => esc_html__( 'Layla', 'wpb_fp' ),
            ]              
        ]
    );

    $this->add_control(
      'show_quickview_btn',
      [
        'type'          => Controls_Manager::SWITCHER,
        'label'         => esc_html__( 'Show Quick View Icon?', 'wpb_fp' ),
        'return_value'  => 'on',
        'default'       => 'on',
      ]
    );

    $this->add_control(
      'show_details_btn',
      [
        'type'          => Controls_Manager::SWITCHER,
        'label'         => esc_html__( 'Show Details Icon?', 'wpb_fp' ),
        'return_value'  => 'on',
        'default'       => 'on',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'wpb_fp_slider_settings',
      [
        'label' => esc_html__( 'Slider Settings', 'wpb_fp' )
      ]
    );

    $this->add_control(
      'enable_slider',
      [
        'type'          => Controls_Manager::SWITCHER,
        'label'         => esc_html__( 'Enable Slider?', 'wpb_fp' ),
        'return_value'  => 'yes',
      ]
    );

    $this->add_control(
      'autoplay',
      [
        'type'          => Controls_Manager::SWITCHER,
        'label'         => esc_html__( 'Slider Autoplay?', 'wpb_fp' ),
        'return_value'  => 'on',
        'default'       => 'on',
      ]
    );

    $this->add_control(
      'loop',
      [
        'type'          => Controls_Manager::SWITCHER,
        'label'         => esc_html__( 'Slider Loop?', 'wpb_fp' ),
        'return_value'  => 'on',
      ]
    );

    $this->add_control(
      'navigation',
      [
        'type'          => Controls_Manager::SWITCHER,
        'label'         => esc_html__( 'Slider Navigation?', 'wpb_fp' ),
        'return_value'  => 'on',
        'default'       => 'on',
      ]
    );

    $this->add_control(
      'slider_pagination',
      [
        'type'          => Controls_Manager::SWITCHER,
        'label'         => esc_html__( 'Slider Pagination?', 'wpb_fp' ),
        'return_value'  => 'on',
      ]
    );

    $this->add_control(
      'margin',
      [
        'label'         => esc_html__( 'Slider Margin', 'wpb_fp' ),
        'description'   => esc_html__('Default 15px.', 'wpb_fp'),
        'type'          => Controls_Manager::NUMBER,
        'min'           => 0,
        'default'       => '15',
      ]
    );

    $this->add_control(
      'items',
      [
        'label'         => esc_html__( 'Slider Columns', 'wpb_fp' ),
        'description'   => esc_html__('Default 3.', 'wpb_fp'),
        'type'          => Controls_Manager::NUMBER,
        'min'           => 0,
        'default'       => '3',
      ]
    );

    $this->add_control(
      'tablet',
      [
        'label'         => esc_html__( 'Slider Columns in Tablet', 'wpb_fp' ),
        'description'   => esc_html__('Default 2.', 'wpb_fp'),
        'type'          => Controls_Manager::NUMBER,
        'min'           => 0,
        'default'       => '2',
      ]
    );

    $this->add_control(
      'mobile',
      [
        'label'         => esc_html__( 'Slider Columns in Mobile', 'wpb_fp' ),
        'description'   => esc_html__('Default 1.', 'wpb_fp'),
        'type'          => Controls_Manager::NUMBER,
        'min'           => 0,
        'default'       => '1',
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'wpb_fp_query_settings',
      [
        'label' => esc_html__( 'Query Settings', 'wpb_fp' )
      ]
    );

    $post_types = get_post_types(array(
      'public'   => true,
      '_builtin' => false,
    ));

    unset($post_types['elementor_library']);
    $post_types['post'] = 'post';
    $post_type_options  = array();

    if( isset($post_types) && !empty($post_types) ){
      foreach ($post_types as  $key => $post_type ) {
        $post_type_options[$post_type] = $post_type;
      }
    }

    $this->add_control(
        'wpb_fp_post_type',
        [   
            'label'         => esc_html__( 'Portfolio Post Type', 'wpb_fp' ),
            'description'   => esc_html__( 'Default: wpb_fp_portfolio. Go to this plugin advance settings to add portfolio option support on your custom post type.', 'wpb_fp' ),
            'type'          => Controls_Manager::SELECT,
            'default'       => 'wpb_fp_portfolio',
            'options'       => $post_type_options,
        ]
    );

    $taxonomy_objects   = get_taxonomies( array( 'public' => true ), 'objects' );
    $taxonomy           = array();
    foreach ($taxonomy_objects as $taxonomy_object) {
      $taxonomy[$taxonomy_object->name] = $taxonomy_object->label;
    }

    $this->add_control(
        'wpb_fp_taxonomy',
        [   
          'label'         => esc_html__( 'Portfolio Taxonomy', 'wpb_fp' ),
          'description'   => esc_html__( 'Default: wpb_fp_portfolio_cat.', 'wpb_fp' ),
          'type'          => Controls_Manager::SELECT,
          'default'       => 'wpb_fp_portfolio_cat',
          'options'       => $taxonomy,
        ]
    );

    $this->add_control(
      'wpb_fp_cat_include',
      [
        'label'         => esc_html__( 'Category Include', 'wpb_fp' ),
        'description'   => esc_html__( 'Comma separated categories id to include.', 'wpb_fp' ),
        'type'          => Controls_Manager::TEXT,
      ]
    );

    $this->add_control(
      'wpb_fp_cat_exclude',
      [
        'label'         => esc_html__( 'Category Exclude', 'wpb_fp' ),
        'description'   => esc_html__( 'Comma separated categories id to exclude.', 'wpb_fp' ),
        'type'          => Controls_Manager::TEXT,
      ]
    );

    $this->end_controls_section();
	}

  protected function render() {
    $settings           = $this->get_settings();
    $posts              =  $settings['wpb_fp_posts']['size'];
    $column             =  $settings['column_settings'];
    $order              =  $settings['order'];
    $order_by           =  $settings['order_by'];
    $show_quickview_btn =  $settings['show_quickview_btn'];
    $show_details_btn   =  $settings['show_details_btn'];
    $post_type          =  $settings['wpb_fp_post_type'];
    $taxonomy           =  $settings['wpb_fp_taxonomy'];
    $cat_include        =  $settings['wpb_fp_cat_include'];
    $cat_exclude        =  $settings['wpb_fp_cat_exclude'];
    $type               =  ( $settings['enable_slider'] == 'yes' ? 'slider' : 'grid' );
    $autoplay           =  $settings['autoplay'];
    $loop               =  $settings['loop'];
    $navigation         =  $settings['navigation'];
    $slider_pagination  =  $settings['slider_pagination'];
    $margin             =  $settings['margin'];
    $items              =  $settings['items'];
    $tablet             =  $settings['tablet'];
    $mobile             =  $settings['mobile'];
    $hover_effect       =  $settings['hover_effect'];

    echo do_shortcode( '[wpb-portfolio type="'.$type.'" column="'.$column.'" posts="'.$posts.'" order="'.$order.'" orderby="'.$order_by.'" show_quickview_btn="'.$show_quickview_btn.'" show_details_btn="'.$show_details_btn.'" autoplay="'.$autoplay.'" loop="'.$loop.'" navigation="'.$navigation.'" pagination="'.$slider_pagination.'" margin="'.$margin.'" items="'.$items.'" tablet="'.$tablet.'" mobile="'.$mobile.'" post_type="'.$post_type.'" taxonomy="'.$taxonomy.'" exclude="'.$cat_exclude.'" include="'.$cat_include.'" hover_effect="'.$hover_effect.'"]' );
  }
}

Plugin::instance()->widgets_manager->register_widget_type( new WPB_FP_Widget_Portfolio() );