( function() { // local scope

	const el = wp.element.createElement; // React.createElement
	const { __ } = wp.i18n; // translation functions
	const { SelectControl, PanelBody, PanelRow, Panel, ToggleControl } = wp.components; //Block inspector wrapper
	const { Fragment } = wp.element;
	const { registerPlugin } = wp.plugins;
	const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
	const { withSelect, withDispatch, dispatch, select } = wp.data;
	const { compose } = wp.compose;

	dispatch( 'core' ).addEntities([{
        name: 'rating-forms', // route name
        kind: 'mrp/v1', // namespace
        baseURL: '/mrp/v1/rating-forms' // API path without /wp-json
    }]);

	/**
	 * Rating form position select control
	 */
	var ratingFormPositionSelect = compose(
		withDispatch( function( dispatch, props ) {
			return {
				setRatingFormPosition: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ 'mrp_rating_form_position' ]: metaValue } }
					);
				}
			}
		} ),
		withSelect( function( select, props ) {
			return {
				ratingFormPosition: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ 'mrp_rating_form_position' ],
			}
		} ) )( function( props ) {
			return el( SelectControl, { 
		      	label: __( 'Rating Form Position', 'multi-rating-pro' ),
		       	options: [
		       		{ value: 'do_not_show', label: __( 'Do not show', 'multi-rating-pro' ) },
		       		{ value: '', label: __( 'Use default settings', 'multi-rating-pro' ) },
		       		{ value: 'before_content', label: __( 'Before content', 'multi-rating-pro' ) },
		       		{ value: 'after_content', label: __( 'After content', 'multi-rating-pro' ) },
					{ value: 'comment_form', label: __( 'Comment form', 'multi-rating-pro' ) }
		       	],
		       	help : __( 'Auto placement position for the rating form on the post.', 'multi-rating-pro' ),
		       	value: props.ratingFormPosition,
		       	onChange: function( value ) {
	               	props.setRatingFormPosition( value );
	            },
		    });
		}
	);

	var ratingFormOptions = [];
    var ratingForms = wp.data.select( 'core' ).getEntityRecords( 'mrp/v1', 'rating-forms' );

	/**
	 * Rating form select control
	 */
	var ratingFormSelect = compose(
		withDispatch( function( dispatch, props ) {
			return {
				setRatingForm: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ 'mrp_rating_form_id' ]: metaValue } }
					);
				}
			}
		} ),
		withSelect( function( select, props ) {
			const { isResolving } = select( 'core/data' );
			return {
				ratingForm: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ 'mrp_rating_form_id' ],
				ratingForms: select( 'core' ).getEntityRecords( 'mrp/v1', 'rating-forms' ),
				isRequesting: isResolving( 'core', 'getEntityRecords', [ 'mrp/v1', 'rating-forms' ] )
			}
		} ) )( function( props ) {

			// wait until ready
			if ( props.isRequesting ) {
		        return el( 'div', null, );
		    }

			var ratingFormOptions = [{ value : '', label: __( 'Use default settings', 'multi-rating-pro' ) }];
			
    		var i;
    		for (i=0; i<props.ratingForms.length;i++) {
    			ratingFormOptions.push({ 
    				value : props.ratingForms[i].id,
    				label : props.ratingForms[i].name
    			});
    		}

			return el( SelectControl, { 
		      	label: __( 'Rating Form', 'multi-rating-pro' ),
		       	description: __( 'Set a default rating form for the post', 'multi-rating-pro' ),
		       	options: ratingFormOptions,
		       	value: props.ratingForm,
		       	onChange: function( value ) {
	               	props.setRatingForm( value );
	            },
		    });
		}
	);

	/**
	 * Allow anonymous ratings toggle control
	 */
	var allowAnonymousSelect = compose(
		withDispatch( function( dispatch, props ) {
			return {
				setAllowAnonymous: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ 'mrp_allow_anonymous' ]: metaValue } }
					);
				}
			}
		} ),
		withSelect( function( select, props ) {
			return {
				allowAnonymous: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ 'mrp_allow_anonymous' ],
			}
		} ) )( function( props ) {
			return el( SelectControl, { 
		      	label: __( 'Allow Anonymous', 'multi-rating-pro' ),
		       	options: [
		       		{ value: '', label: __( 'Use default settings', 'multi-rating-pro' ) },
		       		{ value: 'true', label: __( 'Yes', 'multi-rating-pro' ) },
		       		{ value: 'false', label: __( 'No', 'multi-rating-pro' ) }
		       	],
		       	help : __( 'Do you want to allow anyone to submit ratings? This includes unauthenticated and non logged in users.', 'multi-rating-pro' ),
		       	value: props.allowAnonymous,
		       	onChange: function( value ) {
	               	props.setAllowAnonymous( value );
	            },
		    });
		}
	);

	/**
	 * Rating result position select control
	 */
	var ratingResultPositionSelect = compose(
		withDispatch( function( dispatch, props ) {
			return {
				setRatingResultPosition: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ 'mrp_rating_results_position' ]: metaValue } }
					);
				}
			}
		} ),
		withSelect( function( select, props ) {
			return {
				ratingResultPosition: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ 'mrp_rating_results_position' ],
			}
		} ) )( function( props ) {
			return el( SelectControl, { 
		      	label: __( 'Rating Result Position', 'multi-rating-pro' ),
		       	description: __( 'Add the rating result to the post.', 'multi-rating-pro' ),
		       	options: [
		       		{ value: 'do_not_show', label: __( 'Do not show', 'multi-rating-pro' ) },
		       		{ value: '', label: __( 'Use default settings', 'multi-rating-pro' ) },
		       		{ value: 'before_title', label: __( 'Before title', 'multi-rating-pro' ) },
		      		{ value: 'after_title', label: __( 'After title', 'multi-rating-pro' ) },
		       		{ value: 'before_content', label: __( 'Before content', 'multi-rating-pro' ) },
		      		{ value: 'after_content', label: __( 'After content', 'multi-rating-pro' ) }
		       	],
		       	help : __( 'Auto placement position for the rating result on the post.', 'multi-rating-pro' ),
		       	value: props.ratingResultPosition,
		       	onChange: function( value ) {
	               	props.setRatingResultPosition( value );
	            },
		    });
		}
	);


	/**
	 * Structured data type select
	 */
	var structuredDataTypeSelect = compose(
		withDispatch( function( dispatch, props ) {
			return {
				setStruturedDataType: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ 'mrp_structured_data_type' ]: metaValue } }
					);
				}
			}
		} ),
		withSelect( function( select, props ) {
			return {
				structuredDataType: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ 'mrp_structured_data_type' ],
			}
		} ) )( function( props ) {

			return el( SelectControl, { 
		      	label: 'Create New Type',
		       	description: __( 'Schema.org item type for post.', 'multi-rating-pro' ),
		       	options: [
		       		{ value: '', label: '' },
		       		{ value: 'Book', label: __( 'Book', 'multi-rating-pro' ) },
		       		{ value: 'Course', label: __( 'Course', 'multi-rating-pro' ) },
		       		{ value: 'CreativeWorkSeason', label: __( 'CreativeWorkSeason', 'multi-rating-pro' ) },
		       		{ value: 'CreativeWorkSeries', label: __( 'CreativeWorkSeries', 'multi-rating-pro' ) },
					{ value: 'Episode', label: __( 'Episode', 'multi-rating-pro' ) },
					{ value: 'Event', label: __( 'Event', 'multi-rating-pro' ) },
					{ value: 'Game', label: __( 'Game', 'multi-rating-pro' ) },
					{ value: 'HowTo', label: __( 'HowTo', 'multi-rating-pro' ) },
					{ value: 'LocalBusiness', label: __( 'LocalBusiness', 'multi-rating-pro' ) },
					{ value: 'MediaObject', label: __( 'MediaObject', 'multi-rating-pro' ) },
					{ value: 'Movie', label: __( 'Movie', 'multi-rating-pro' ) },
					{ value: 'MusicPlaylist', label: __( 'MusicPlaylist', 'multi-rating-pro' ) },
					{ value: 'MusicRecording', label: __( 'MusicRecording', 'multi-rating-pro' ) },
		       		{ value: 'Organization', label: __( 'Organization', 'multi-rating-pro' ) },
		       		{ value: 'Product', label: __( 'Product', 'multi-rating-pro' ) },
		       		{ value: 'Recipe', label: __( 'Recipe', 'multi-rating-pro' ) },
		       		{ value: 'SoftwareApplication', label: __( 'SoftwareApplication', 'multi-rating-pro' ) }
		       	],
		       	help :	__( 'Schema.org item type for post. If you have the WordPress SEO or WooCommerce plugins adding structured data for the type already, do not set. Note some types may require additional structured data.', 'multi-rating-pro' ),
		       	value: props.structuredDataType,
		       	onChange: function( value ) {
	               	props.setStruturedDataType( value );
	            },
		    });

		}
	);


	/**
	 * Adds to the plugin post settings to the Gutenberg plugin and sidebar menus
	 */
	registerPlugin( 'multi-rating-pro', {

		icon: 'star-filled',
	    
	    render: function () {
	    	return el( 
	    		Fragment, 
	    		{},
		        el( 
		        	PluginSidebarMoreMenuItem, 
		        	{
		            	target: 'multi-rating-pro',
		            	icon: 'star-filled'
		        	},
		        	__( 'Multi Rating Pro', 'multi-rating-pro' )
		    	),
		    	el( 
		    		PluginSidebar, 
		    		{
		    			name: 'multi-rating-pro',
		    			icon: 'star-filled',
		    			title: __( 'Multi Rating Pro', 'multi-rating-pro' ),
		    			className: 'mrp-plugin-sidebar'
		    		},
		    		el( 
		    			Panel,
		    			{},
		    			el(
			    			PanelBody, 
			    			{ 
			    				//title: __( 'General', 'multi-rating-pro' ),
			    				//initialOpen: true
			    				opened: true
			    			}, 
			        		el(
			        			PanelRow,
			        			{},
			        			el( ratingFormSelect )
			        		),
			        		el(
			        			PanelRow,
			        			{},
			        			el( allowAnonymousSelect )
			        		)
			        	),
		    			el(
			    			PanelBody, 
			    			{ 
			    				title: __( 'Auto Placement', 'multi-rating-pro' ),
			    				initialOpen: false
			    			}, 
			        		el(
			        			PanelRow,
			        			{},
			        			el( ratingFormPositionSelect )
			        		),
			        		el(
			        			PanelRow,
			        			{},
			        			el( ratingResultPositionSelect )
			        		)
			        	),
		        		el(
			    			PanelBody, 
			    			{ 
			    				title: __( 'Structured Data', 'multi-rating-pro' ),
			    				initialOpen: false
			    			}, 
			        		el(
				        		PanelRow,
				        		{},
				        		el( 
				        			'div', 
				        			{}, 
				        			__( 'Supports rich snippets with aggregate ratings for the post in search engine results pages (SERP).', 'multi-rating-pro' )
				        		)
				        	),
				        	el(
				        		PanelRow,
				        		{},
				        		el( structuredDataTypeSelect )
				        	)
			        	)
		        	)
		    	)
		    )
	    }
	} );

} )( )