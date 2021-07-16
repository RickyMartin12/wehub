<?php

/**
 * Gutenberg class. Registers plugin in Gutenberg Editor sidebar and registers 
 * blocks
 * 
 * @author dpowney
 *
 */
class MRP_Gutenberg {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'enqueue_block_editor_assets', array( $this, 'register_plugin' ) );
		add_action( 'init', array( $this, 'set_script_translations' ) );
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'init', array( $this, 'register_post_meta' ) );
	}

	/**
	 * Registers the plugin sidebar
	 */
	public function register_plugin() {

		wp_register_script( 
			'mrp-gutenberg-plugin-script', 
			plugins_url( '../assets/js/plugin.js', __FILE__ ), 
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-plugins', 
				'wp-edit-post', 'wp-data', 'wp-compose' ) 
		);

		wp_enqueue_script('mrp-gutenberg-plugin-script');

	}

	/**
	 * Adds support for script language translations
	 */
	public function set_script_translations() {
		wp_set_script_translations( 'mrp-gutenberg-plugin-script', 'multi-rating-pro' );
    	wp_set_script_translations( 'mrp-gutenberg-blocks-script', 'multi-rating-pro' );
	}


	/**
	 * Register blocks
	 */
	public function register_blocks() {

		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}
	 
	    wp_register_script( 'mrp-gutenberg-blocks-script', plugins_url( '../assets/js/blocks.js', __FILE__ ), array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-element', 'wp-api-fetch' ) );

	    $custom_text_settings = (array) MRP_Multi_Rating::instance()->settings->custom_text_settings;
	    $general_settings = (array) MRP_Multi_Rating::instance()->settings->general_settings;

	    register_block_type( 'multi-rating-pro/rating-form', array(
	        'editor_script' => 'mrp-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_form_block_render' ),
	        'attributes' => [
	        	'rating_form_id' => [
					'default' => '',
					'type' => 'string'
				],
				'title' => [
					'default' => $custom_text_settings[MRP_Multi_Rating::RATING_FORM_TITLE_TEXT_OPTION],
					'type' => 'string'
				],
				'submit_button_text' => [
					'type' => 'string',
					'default' => $custom_text_settings[MRP_Multi_Rating::SUBMIT_RATING_FORM_BUTTON_TEXT_OPTION],
				],
				'update_button_text' => [
					'type' => 'string',
					'default' => $custom_text_settings[MRP_Multi_Rating::UPDATE_RATING_FORM_BUTTON_TEXT_OPTION],
				],
				'delete_button_text' => [
					'type' => 'string',
					'default' => $custom_text_settings[MRP_Multi_Rating::DELETE_RATING_FORM_BUTTON_TEXT_OPTION],
				]
			]
	    ) );

	    register_block_type( 'multi-rating-pro/rating-result', array(
	        'editor_script' => 'mrp-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_result_block_render' ),
	        'attributes' => [
	        	'rating_form_id' => [
					'default' => '',
					'type' => 'string'
				],
				'show_title' => [
					'type' => 'boolean',
					'default' => false
				],
				'show_count' => [
					'type' => 'boolean',
					'default' => true
				],
				'result_type' => [
					'type' => 'string',
					'default' => $general_settings[MRP_Multi_Rating::DEFAULT_RATING_RESULT_TYPE_OPTION]
				]
			]
	    ) );

	    register_block_type( 'multi-rating-pro/rating-results-list', array(
	        'editor_script' => 'mrp-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_results_list_block_render' ),
	        'attributes' => [
	        	'rating_form_id' => [
					'default' => '',
					'type' => 'string'
				],
	        	'title' => [
					'default' => $custom_text_settings[MRP_Multi_Rating::RATING_RESULTS_LIST_TITLE_TEXT_OPTION],
					'type' => 'string'
				],
				'show_count' => [
					'type' => 'boolean',
					'default' => true
				],
				'show_filter' => [
					'type' => 'boolean',
					'default' => false
				],
				'limit' => [
					'type' => 'integer',
					'default' => 5
				],
				'show_rank' => [
					'type' => 'boolean',
					'default' => true
				],
				'show_featured_img' => [
					'type' => 'boolean',
					'default' => true
				],
				'result_type' => [
					'type' => 'string',
					'default' => $general_settings[MRP_Multi_Rating::DEFAULT_RATING_RESULT_TYPE_OPTION]
				]
			]
	    ) );


	     register_block_type( 'multi-rating-pro/rating-entry-details-list', array(
	        'editor_script' => 'mrp-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_entry_details_list_block_render' ),
	        'attributes' => [
	        	'rating_form_id' => [
					'default' => '',
					'type' => 'string'
				],
				'title' => [
					'default' => $custom_text_settings[MRP_Multi_Rating::RATING_ENTRIES_LIST_TITLE_TEXT_OPTION],
					'type' => 'string'
				],
	        	'layout' => [
					'default' => 'table',
					'type' => 'string'
				],
				'sort_by' => [
					'default' => 'highest_rated',
					'type' => 'string'
				],
				'result_type' => [
					'type' => 'string',
					'default' => $general_settings[MRP_Multi_Rating::DEFAULT_RATING_RESULT_TYPE_OPTION]
				],
				'limit' => [
					'type' => 'integer',
					'default' => 5
				],
				'show_load_more' => [
					'default' => false,
					'type' => 'boolean'
				],
				'show_name' => [
					'default' => true,
					'type' => 'boolean'
				],
				'show_avatar' => [
					'default' => true,
					'type' => 'boolean'
				],
				'show_title' => [
					'default' => true,
					'type' => 'boolean'
				],
				'show_comment' => [
					'default' => true,
					'type' => 'boolean'
				],
				'show_date' => [
					'default' => true,
					'type' => 'boolean'
				],
				'show_overall_rating' => [
					'default' => true,
					'type' => 'boolean'
				],
				'show_rating_items' => [
					'default' => true,
					'type' => 'boolean'
				],
				'show_custom_fields' => [
					'default' => true,
					'type' => 'boolean'
				],
				'add_author_link' => [
					'default' => true,
					'type' => 'boolean'
				],
			]
	    ) );

	    register_block_type( 'multi-rating-pro/rating-item-results', array(
	        'editor_script' => 'mrp-gutenberg-blocks-script',
	        'render_callback' => array( $this, 'rating_item_results_block_render' ),
	        'attributes' => [
	        	'rating_form_id' => [
					'default' => '',
					'type' => 'string'
				],
				'result_type' => [
					'type' => 'string',
					'default' => $general_settings[MRP_Multi_Rating::DEFAULT_RATING_RESULT_TYPE_OPTION]
				],
				'show_count' => [
					'type' => 'boolean',
					'default' => true
				],
				'preserve_max_option' => [
					'type' => 'boolean',
					'default' => false
				],
				'layout' => [
					'type' => 'string',
					'default' => 'options_block'
				]
			]
	    ) );
	 
	}

	/**
	 * Renders the rating form block
	 */
	public function rating_form_block_render( $attributes ) {
		global $post;

		$rating_form_id = $attributes['rating_form_id'];
		if ($rating_form_id === '') {
			$rating_form_id = MRP_Utils::get_rating_form( $post->ID );
		}
		
		$shortcode_format = '[mrp_rating_form title="%s" submit_button_text="%s" update_button_text="%s" delete_button_text="%s" rating_form_id=%d]';
		
		$shortcode_text = sprintf( $shortcode_format, $attributes['title'], $attributes['submit_button_text'],
			$attributes['update_button_text'], $attributes['delete_button_text'], intval( $rating_form_id )
		);
		
		return do_shortcode( $shortcode_text );
	}

	/**
	 * Renders the rating result block
	 */
	public function rating_result_block_render( $attributes ) {
		global $post;

		$rating_form_id = $attributes['rating_form_id'];
		if ($rating_form_id === '') {
			$rating_form_id = MRP_Utils::get_rating_form( $post->ID );
		}
		$show_count = $attributes['show_count'] === true ? 'true' : 'false';
		$show_title = $attributes['show_title'] === true ? 'true' : 'false';

		$shortcode_format = '[mrp_rating_result show_title="%s" show_count="%s" result_type="%s" rating_form_id="%s"]';

		$shortcode_text = sprintf( $shortcode_format, $show_title, $show_count, $attributes['result_type'], intval($rating_form_id) );

		return do_shortcode( $shortcode_text );
	}

	/**
	 * Renders the rating results list block
	 */
	public function rating_results_list_block_render( $attributes ) {
		global $post;

		$rating_form_id = $attributes['rating_form_id'];
		if ($rating_form_id === '') {
			$rating_form_id = MRP_Utils::get_rating_form( $post->ID );
		}
		$show_count = $attributes['show_count'] === true ? 'true' : 'false';
		$show_rank = $attributes['show_rank'] === true ? 'true' : 'false';
		$show_featured_img = $attributes['show_featured_img'] === true ? 'true' : 'false';
		$show_filter = $attributes['show_filter'] === true ? 'true' : 'false';

		$shortcode_format = '[mrp_rating_results_list title="%s" show_count="%s" show_rank="%s" 
			show_featured_img="%s" limit=%d show_filter="%s" result_type="%s" rating_form_id="%s"]';

		$shortcode_text = sprintf( $shortcode_format, $attributes['title'], $show_count, $show_rank, 
			$show_featured_img, $attributes['limit'], $show_filter, $attributes['result_type'], 
			intval($rating_form_id) );

		return do_shortcode( $shortcode_text );
	}


	/**
	 * Renders the rating results list block
	 */
	public function rating_entry_details_list_block_render( $attributes ) {
		global $post;

		$rating_form_id = $attributes['rating_form_id'];
		if ($rating_form_id === '') {
			$rating_form_id = MRP_Utils::get_rating_form( $post->ID );
		}
		$title = $attributes['title'];
		$layout = $attributes['layout'];
		$sort_by = $attributes['sort_by'];
		$result_type = $attributes['result_type'];
		$limit = $attributes['limit'];
		$show_load_more = $attributes['show_load_more'] === true ? 'true' : 'false';
		$show_name = $attributes['show_name'] === true ? 'true' : 'false';
		$show_avatar = $attributes['show_avatar'] === true ? 'true' : 'false';
		$show_title = $attributes['show_title'] === true ? 'true' : 'false';
		$show_comment = $attributes['show_comment'] === true ? 'true' : 'false';
		$show_date = $attributes['show_date'] === true ? 'true' : 'false';
		$show_overall_rating = $attributes['show_overall_rating'] === true ? 'true' : 'false';
		$show_rating_items = $attributes['show_rating_items'] === true ? 'true' : 'false';
        $show_custom_fields = $attributes['show_custom_fields'] === true ? 'true' : 'false';
		$add_author_link = $attributes['add_author_link'] === true ? 'true' : 'false';

		$shortcode_format = '[mrp_rating_entry_details_list rating_form_id="%s" title="%s" layout="%s" 
		    sort_by="%s" result_type="%s" limit=%d show_load_more="%s" show_name="%s"
		    show_avatar="%s" show_title="%s" show_comment="%s" show_date="%s" show_overall_rating="%s" 
		    show_rating_items="%s" show_custom_fields="%s" add_author_link="%s"]';

		$shortcode_text = sprintf( $shortcode_format, intval($rating_form_id), $title, $layout, $sort_by, 
			$result_type, $limit, $show_load_more, $show_name, $show_avatar, $show_title, $show_comment, 
			$show_date, $show_overall_rating, $show_rating_items, $show_custom_fields, $add_author_link );

		return do_shortcode( $shortcode_text );
	}

	/**
	 * Renders the rating item results block
	 */
	public function rating_item_results_block_render( $attributes ) {
		global $post;

		$rating_form_id = $attributes['rating_form_id'];
		if ($rating_form_id === '') {
			$rating_form_id = MRP_Utils::get_rating_form( $post->ID );
		}
		$result_type = $attributes['result_type'];
		$show_count = $attributes['show_count'] === true ? 'true' : 'false';
		$preserve_max_option = $attributes['preserve_max_option'] === true ? 'true' : 'false';
		$layout = $attributes['layout'];

		$shortcode_format = '[mrp_rating_item_results rating_form_id="%s" result_type="%s"  
		    show_count="%s" preserve_max_option="%s" layout="%s"]';

		$shortcode_text = sprintf( $shortcode_format, intval($rating_form_id), $result_type, 
			$show_count, $preserve_max_option, $layout );

		return do_shortcode( $shortcode_text );
	}

	/*
	 * Registers post meta fields with REST API visibility
	 */
	public function register_post_meta() {

		$post_types = get_post_types( array( 'public' => true ) );

		foreach ( $post_types as $post_type ) {

			register_post_meta( $post_type, MRP_Multi_Rating::RATING_FORM_POSITION_POST_META, array(
				'show_in_rest' => true,
		        'single' => true,
		        'type' => 'string',
		        'auth_callback' => function () { return current_user_can('edit_posts'); }
		    ));
			register_post_meta( $post_type, MRP_Multi_Rating::RATING_RESULTS_POSITION_POST_META, array(
				'show_in_rest' => true,
		        'single' => true,
		        'type' => 'string',
		        'auth_callback' => function () { return current_user_can('edit_posts'); }
			));
			register_post_meta( $post_type, MRP_Multi_Rating::STRUCTURED_DATA_TYPE_POST_META, array(
				'show_in_rest' => true,
		        'single' => true,
		        'type' => 'string',
		        'auth_callback' => function () { return current_user_can('edit_posts'); }
			));
			register_post_meta( $post_type, MRP_Multi_Rating::ALLOW_ANONYMOUS_POST_META, array(
				'show_in_rest' => true,
		        'single' => true,
		        'type' => 'string',
		        'auth_callback' => function () { return current_user_can('edit_posts'); }
			));
			register_post_meta( $post_type, MRP_Multi_Rating::RATING_FORM_ID_POST_META, array(
				'show_in_rest' => true,
		        'single' => true,
		        'type' => 'string',
		        'auth_callback' => function () { return current_user_can('edit_posts'); }
			));

		}
	}

}