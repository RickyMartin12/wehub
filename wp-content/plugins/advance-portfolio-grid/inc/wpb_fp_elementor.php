<?php


/**
 * Add a New Elementor Widegt Category
 */

add_action( 'elementor/init', 'wpb_fp_add_elementor_category' );

if( !function_exists('wpb_fp_add_elementor_category') ){
    function wpb_fp_add_elementor_category(){
        \Elementor\Plugin::instance()->elements_manager->add_category(
            'wpb_widgets',
            array(
              'title' => esc_html__( 'WpBean Plugins', 'wpb_fp' ),
            ),
            1
        );
    }
}


/**
 * Add Elementor Widegts
 */


class WPB_FP_Elementor_Element {

  private static $instance = null;

  public static function get_instance() {
    if ( ! self::$instance )
    self::$instance = new self;
    return self::$instance;
  }

  public function init(){
    add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );
    add_action( 'elementor/frontend/after_register_scripts', array( $this, 'widget_register_scripts' ) );
    add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'widget_enqueue_scripts' ) );
    add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'widget_enqueue_scripts' ) );
    add_action( 'elementor/frontend/after_register_styles', array( $this, 'widget_register_styles' ) );
    add_action( 'elementor/frontend/after_enqueue_styles', array($this, 'widget_enqueue_styles') );

  }

  public function widget_register_scripts() {
    // Slider
    $enable_slider = wpb_fp_get_option( 'wpb_fp_enable_slider', 'wpb_fp_slider', 'on' );

    if( $enable_slider && $enable_slider == 'on' ){
      wp_register_script('wpb-fp-owl-carousel', plugins_url('../assets/js/owl.carousel.min.js', __FILE__), array('jquery'), '2.3.4', false);
    }

    wp_register_script('wpb-fp-magnific-popup', plugins_url('../assets/js/jquery.magnific-popup.min.js', __FILE__), array('jquery'), '1.1', false);
    wp_register_script('wpb-fp-main', plugins_url('../assets/js/main.js', __FILE__), array('jquery'), '1.0', false);
    wp_register_script('wpb-fp-el-main', plugins_url('../assets/js/el-main.js', __FILE__) ,array('jquery'),'1.0', false);
  }

  public function widget_enqueue_scripts() {

    // Slider
    $enable_slider = wpb_fp_get_option( 'wpb_fp_enable_slider', 'wpb_fp_slider', 'on' );

    if( $enable_slider && $enable_slider == 'on' ){
      wp_enqueue_script('wpb-fp-owl-carousel', plugins_url('../assets/js/owl.carousel.min.js', __FILE__), array('jquery'), '2.3.4', false);
    }
    
    wp_enqueue_script('wpb-fp-magnific-popup', plugins_url('../assets/js/jquery.magnific-popup.min.js', __FILE__), array('jquery'), '1.1', false);
    wp_enqueue_script('wpb-fp-main', plugins_url('../assets/js/main.js', __FILE__), array('jquery'), '1.0', false);
    wp_enqueue_script('wpb-fp-el-main', plugins_url('../assets/js/el-main.js', __FILE__) ,array('jquery'),'1.0', false);
  }

  public function widget_register_styles(){
    $enable_slider = wpb_fp_get_option( 'wpb_fp_enable_slider', 'wpb_fp_slider', 'on' );

    if( $enable_slider && $enable_slider == 'on' ){
      wp_register_style('wpb-fp-owl-carousel', plugins_url('../assets/css/owl.carousel.min.css', __FILE__), '', '2.3.4');
    }

    wp_register_style('wpb-fp-bootstrap-grid', plugins_url('../assets/css/wpb-custom-bootstrap.css', __FILE__), '', '3.2');
    wp_register_style('wpb-fp-magnific-popup', plugins_url('../assets/css/magnific-popup.css', __FILE__), '', '1.1');
    wp_register_style('wpb-fp-main', plugins_url('../assets/css/main.css', __FILE__), '', '1.0');
  }

  public function widget_enqueue_styles(){

    $enable_slider = wpb_fp_get_option( 'wpb_fp_enable_slider', 'wpb_fp_slider', 'on' );

    if( $enable_slider && $enable_slider == 'on' ){
      wp_enqueue_style('wpb-fp-owl-carousel', plugins_url('../assets/css/owl.carousel.min.css', __FILE__), '', '2.3.4');
    }

    wp_enqueue_style('wpb-fp-bootstrap-grid', plugins_url('../assets/css/wpb-custom-bootstrap.css', __FILE__), '', '3.2');
    wp_enqueue_style('wpb-fp-magnific-popup', plugins_url('../assets/css/magnific-popup.css', __FILE__), '', '1.1');
    wp_enqueue_style('wpb-fp-main', plugins_url('../assets/css/main.css', __FILE__), '', '1.0');
  }

  public function widgets_registered() {
    if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')){
      require_once dirname( __FILE__ ) . '/../elementor/wpb-fp-portfolio-elementor-widget.php';
    }
  }
}

WPB_FP_Elementor_Element::get_instance()->init();