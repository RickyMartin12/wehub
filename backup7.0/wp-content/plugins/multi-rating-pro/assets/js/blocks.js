( function() { // local scope

	const { registerBlockType } = wp.blocks; // Blocks API
	const el = wp.element.createElement; // React.createElement
	const { __ } = wp.i18n; // translation functions
	const { InspectorControls } = wp.editor; 
	const { PanelBody, PanelRow, Panel, TextControl, ToggleControl, SelectControl } = wp.components; 
	const { serverSideRender, apiFetch } = wp;

	var ratingFormOptions = [{ value : '', label: __( 'Use default', 'multi-rating-pro' ) }];

	wp.apiFetch( { path: '/mrp/v1/rating-forms' } ).
	then( (ratingForms) => {
		var i;
		for (i=0; i<ratingForms.length;i++) {
		 	ratingFormOptions.push({ 
		  		value : ratingForms[i].id,
		   		label : ratingForms[i].name
		   	});
		}
	});

	/*
	 * Rating form block
	 */
	registerBlockType( 'multi-rating-pro/rating-form', {
		
		// Built-in attributes
		title: __( 'Rating Form', 'multi-rating-pro' ),
		description: __( 'Adds a rating form for a post.', 'multi-rating-pro' ),
		icon: 'star-filled',
		category: 'common',

		// Custom attributes - ser server side

		// Built-in functions
		edit: function( props ) {

			var className = props.className;
	        var setAttributes = props.setAttributes;

			return el('div', {}, [
				
				// Preview
				el( serverSideRender, {
					block: 'multi-rating-pro/rating-form',
					attributes: props.attributes,
					className : className
				} ),
				
				// Block inspector
				el( InspectorControls, { className : props.className }, [
					el( 
		    			PanelBody, 
		    			{},
		    			el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.rating_form_id,
								label: __( 'Rating Form', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { rating_form_id: value } );
			              		},
			              		options: ratingFormOptions
							})
						), 
		        		el(
		        			PanelRow,
		        			{},
		        			el( TextControl, {
								value: props.attributes.title,
								label: __( 'Title', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { title: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el( TextControl, {
								value: props.attributes.submit_button_text,
								label: __( 'Submit Button Text', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { submit_button_text: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el( TextControl, {
								value: props.attributes.update_button_text,
								label: __( 'Update Button Text', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { update_button_text: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el( TextControl, {
								value: props.attributes.delete_button_text,
								label: __( 'Delete Button Text', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { delete_button_text: value } );
			              		}
							})
						)
		        	)
				])
			]);
		},

		save: function( props ) {
			return null;
		}

	} );



	/*
	 * Rating result block
	 */
	registerBlockType( 'multi-rating-pro/rating-result', {
		
		// Built-in attributes
		title: __( 'Rating Result', 'multi-rating-pro' ),
		description: __( 'Displays an average rating result for a post.', 'multi-rating-pro' ),
		icon: 'star-filled',
		category: 'common',

		// Custom attributes - set server side

		// Built-in functions
		edit: function( props) {

			var className = props.className;
	        var setAttributes = props.setAttributes;

			return el('div', {}, [
				
				// Preview
				el( serverSideRender, {
					block: 'multi-rating-pro/rating-result',
					attributes: props.attributes,
					className : className
				} ),
				
				// Block inspector
				el( InspectorControls, {}, [
					el(
		    			PanelBody, 
		    			{},
		    			el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_title,
								label: __( 'Show Title', 'multi-rating-pro' ),
								onChange: ( value ) => {
									props.setAttributes( { show_title: value } );
			              		},
			              		help: __( 'Do you want to display the post title?', 'multi-rating-pro' )
							}),
						),
		    			el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.rating_form_id,
								label: __( 'Rating Form', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { rating_form_id: value } );
			              		},
			              		options: ratingFormOptions
							})
						),
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.result_type,
								label: __( 'Result Type', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { result_type: value } );
			              		},
			              		options: [
			              			{ value: 'star_rating', label: __( 'Stars', 'multi-rating-pro' ) },
			              			{ value: 'score', label: __( 'Score', 'multi-rating-pro' ) },
			              			{ value: 'percentage', label: __( 'Percentage', 'multi-rating-pro' ) }
			              		]
							})
						), 
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_count,
								label: __( 'Show Count', 'multi-rating-pro' ),
								onChange: ( value ) => {
									props.setAttributes( { show_count: value } );
			              		},
							})
						)
					)
				])
			]);
		},

		save: function( props ) {
			return null;
		}

	} );


	/*
	 * Rating result block
	 */
	registerBlockType( 'multi-rating-pro/rating-results-list', {
		
		// Built-in attributes
		title: __( 'Rating Results List', 'multi-rating-pro' ),
		description: __( 'Displays a list of the highest average rating results for posts.', 'multi-rating-pro' ),
		icon: 'star-filled',
		category: 'common',

		// Custom attributes - set server side

		// Built-in functions
		edit: function( props) {

			var className = props.className;
	        var setAttributes = props.setAttributes;

			return el('div', {}, [
				
				// Preview
				el( serverSideRender, {
					block: 'multi-rating-pro/rating-results-list',
					attributes: props.attributes,
					className : className
				} ),
				
				// Block inspector
				el( InspectorControls, {}, [
					el(
		    			PanelBody, 
		    			{}, 
						el( 
		        			PanelRow,
		        			{},
		        			el( TextControl, {
								value: props.attributes.title,
								label: __( 'Title', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { title: value } );
			              		},
			              		type: 'string'
							})
						),
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.rating_form_id,
								label: __( 'Rating Form', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { rating_form_id: value } );
			              		},
			              		options: ratingFormOptions
							})
						), 
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.result_type,
								label: __( 'Result Type', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { result_type: value } );
			              		},
			              		options: [
			              			{ value: 'star_rating', label: __( 'Stars', 'multi-rating-pro' ) },
			              			{ value: 'score', label: __( 'Score', 'multi-rating-pro' ) },
			              			{ value: 'percentage', label: __( 'Percentage', 'multi-rating-pro' ) }
			              		]
							})
						), 
						el(
		        			PanelRow,
		        			{},
							el( TextControl, {
								value: props.attributes.limit,
								label: __( 'Limit', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { limit: parseInt(value) } );
			              		},
			              		min: 1,
			              		max: 50,
			              		type: 'number'
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_count,
								label: __( 'Show Count', 'multi-rating-pro' ),
								onChange: ( value ) => {
									props.setAttributes( { show_count: value } );
			              		},
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_filter,
								label: __( 'Show Filter', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_filter: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_rank,
								label: __( 'Show Rank', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_rank: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_featured_img,
								label: __( 'Show Featured Image', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_featured_img: value } );
			              		}
							})
						)
					)
				])
			]);
		},

		save: function( props ) {
			return null;
		}

	} );



	/*
	 * Rating item results block
	 */
	registerBlockType( 'multi-rating-pro/rating-item-results', {
		
		// Built-in attributes
		title: __( 'Rating Item Results', 'multi-rating-pro' ),
		description: __( 'Displays a summary or a breakdown of rating item results.', 'multi-rating-pro' ),
		icon: 'star-filled',
		category: 'common',

		// Custom attributes - set server side

		// Built-in functions
		edit: function( props) {

			var className = props.className;
	        var setAttributes = props.setAttributes;

			return el('div', {}, [
				
				// Preview
				el( serverSideRender, {
					block: 'multi-rating-pro/rating-item-results',
					attributes: props.attributes,
					className : className
				} ),
				
				// Block inspector
				el( InspectorControls, {}, [
					el(
		    			PanelBody, 
		    			{}, 
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.rating_form_id,
								label: __( 'Rating Form', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { rating_form_id: value } );
			              		},
			              		options: ratingFormOptions
							})
						), 
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.result_type,
								label: __( 'Result Type', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { result_type: value } );
			              		},
			              		options: [
			              			{ value: 'star_rating', label: __( 'Stars', 'multi-rating-pro' ) },
			              			{ value: 'score', label: __( 'Score', 'multi-rating-pro' ) },
			              			{ value: 'percentage', label: __( 'Percentage', 'multi-rating-pro' ) }
			              		]
							})
						), 
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_count,
								label: __( 'Show Count', 'multi-rating-pro' ),
								onChange: ( value ) => {
									props.setAttributes( { show_count: value } );
			              		},
			              		help: __( 'Only applicable for no options layout.', 'multi-rating-pro' )
							})
						),
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.layout,
								label: __( 'Layout', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { layout: value } );
			              		},
			              		options: [
			              			{ value: 'no_options', label: __( 'No options', 'multi-rating-pro' ) },
			              			{ value: 'options_inline', label: __( 'Options inline', 'multi-rating-pro' ) },
			              			{ value: 'options_block', label: __( 'Options block', 'multi-rating-pro' ) },
			              		],
			              		help: __( 'No options shows a summary. Options inline shows a breakdown. Options block displays a bar chart.', 'multi-rating-pro' )
							}),
		        		),
						el(
			        		PanelRow,
			        		{},
							el(ToggleControl, {
								checked: props.attributes.preserve_max_option,
								label: __( 'Preserve Max Option', 'multi-rating-pro' ),
								onChange: ( value ) => {
				               		props.setAttributes( { preserve_max_option: value } );
				            	},
				            help: __( 'Check to preserve the maximum option values when calculating results. This only effects the options inline and options block layouts.', 'multi-rating-pro' )
							})
						)
					)
				])
			]);
		},

		save: function( props ) {
			return null;
		}

	} );


	/*
	 * Rating entry details list block
	 */
	registerBlockType( 'multi-rating-pro/rating-entry-details-list', {
		
		// Built-in attributes
		title: __( 'Rating Entry Details List', 'multi-rating-pro' ),
		description: __( 'Displays a list of rating entry details.', 'multi-rating-pro' ),
		icon: 'star-filled',
		category: 'common',

		// Custom attributes - ser server side

		// Built-in functions
		edit: function( props ) {

			var className = props.className;
	        var setAttributes = props.setAttributes;

			return el('div', {}, [
				
				// Preview
				el( serverSideRender, {
					block: 'multi-rating-pro/rating-entry-details-list',
					attributes: props.attributes,
					className : className
				} ),
				
				// Block inspector
				el( InspectorControls, { className : props.className }, [
					el( 
		    			PanelBody, 
		    			{},
		    			el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.rating_form_id,
								label: __( 'Rating Form', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { rating_form_id: value } );
			              		},
			              		options: ratingFormOptions
							})
						),
						el(
		        			PanelRow,
		        			{},
		        			el( TextControl, {
								value: props.attributes.title,
								label: __( 'Title', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { title: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.layout,
								label: __( 'Layout', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { layout: value } );
			              		},
			              		options: [
			              			{ value: 'table', label: __( 'Table', 'multi-rating-pro' ) },
			              			{ value: 'inline', label: __( 'Inline', 'multi-rating-pro' ) }
			              		],
			              		help: __( 'Review layout for display. Note inline is more responsive and better suited to widget areas.', 'multi-rating-pro' )
							})
						),  
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.sort_by,
								label: __( 'Sort By', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { sort_by: value } );
			              		},
			              		options: [
			              			{ value: 'highest_rated', label: __( 'Highest Rated', 'multi-rating-pro' ) },
			              			{ value: 'lowest_rated ', label: __( 'Lowest Rated', 'multi-rating-pro' ) },
			              			{ value: 'most_recent', label: __( 'Most Recent', 'multi-rating-pro' ) }
			              		]
							})
						), 
						el(
		        			PanelRow,
		        			{},
		        			el( SelectControl, {
								value: props.attributes.result_type,
								label: __( 'Result Type', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { result_type: value } );
			              		},
			              		options: [
			              			{ value: 'star_rating', label: __( 'Stars', 'multi-rating-pro' ) },
			              			{ value: 'score', label: __( 'Score', 'multi-rating-pro' ) },
			              			{ value: 'percentage', label: __( 'Percentage', 'multi-rating-pro' ) }
			              		]
							})
						), 
						el(
		        			PanelRow,
		        			{},
							el( TextControl, {
								value: props.attributes.limit,
								label: __( 'Limit', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { limit: parseInt(value) } );
			              		},
			              		min: 1,
			              		max: 50,
			              		type: 'number'
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_load_more,
								label: __( 'Show Load More', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_load_more: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_name,
								label: __( 'Show Name', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_name: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_avatar,
								label: __( 'Show Avatar', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_avatar: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
		        			el( ToggleControl, {
								checked: props.attributes.show_title,
								label: __( 'Show Title', 'multi-rating-pro' ),
								onChange: ( value ) => {
									props.setAttributes( { show_title: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
		        			el( ToggleControl, {
								checked: props.attributes.show_comment,
								label: __( 'Show Comment', 'multi-rating-pro' ),
								onChange: ( value ) => {
									props.setAttributes( { show_comment: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
		        			el( ToggleControl, {
								checked: props.attributes.show_date,
								label: __( 'Show Date', 'multi-rating-pro' ),
								onChange: ( value ) => {
									props.setAttributes( { show_date: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_overall_rating,
								label: __( 'Show Overall Rating', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_overall_rating: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_rating_items,
								label: __( 'Show Rating Items', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_rating_items: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_custom_fields,
								label: __( 'Show Custom Fields', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_custom_fields: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.add_author_link,
								label: __( 'Add Author Link', 'multi-rating-pro' ),
								onChange: ( value ) => {
			                		props.setAttributes( { add_author_link: value } );
			              		}
							})
						),
		        	)
				])
			]);
		},

		save: function( props ) {
			return null;
		}

	} );

} )( )