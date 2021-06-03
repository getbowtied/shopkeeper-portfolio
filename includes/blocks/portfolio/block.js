( function( blocks, components, editor, i18n, element ) {

	const el = element.createElement;

	/* Blocks */
	const registerBlockType = wp.blocks.registerBlockType;

	const {
		TextControl,
		SelectControl,
		ToggleControl,
		RadioControl,
		RangeControl,
		SVG,
		Path,
	} = wp.components;

	const {
		InspectorControls,
	} = wp.blockEditor;

	const {
		ServerSideRender,
	} = wp.editor;

	const apiFetch = wp.apiFetch;

	/* Register Block */
	registerBlockType( 'getbowtied/sk-portfolio', {
		title: i18n.__( 'Portfolio', 'shopkeeper-portfolio' ),
		icon:
			el( SVG, { xmlns:'http://www.w3.org/2000/svg', viewBox:'0 0 24 24' },
				el( Path, { d:'M14 6V4h-4v2h4zM4 8v11h16V8H4zm16-2c1.11 0 2 .89 2 2v11c0 1.11-.89 2-2 2H4c-1.11 0-2-.89-2-2l.01-11c0-1.11.88-2 1.99-2h4V4c0-1.11.89-2 2-2h4c1.11 0 2 .89 2 2v2h4z' } ),
			),
		category: 'shopkeeper',
		supports: {
			align: [ 'center', 'wide', 'full' ],
		},
		styles: [
			{ name: 'default', label:    i18n.__( 'Equal Boxes', 'shopkeeper-portfolio' ), isDefault: true },
			{ name: 'masonry_1', label:  i18n.__( 'Masonry Style V1', 'shopkeeper-portfolio' ) },
			{ name: 'masonry_2', label:  i18n.__( 'Masonry Style V2', 'shopkeeper-portfolio' ) },
			{ name: 'masonry_3', label:  i18n.__( 'Masonry Style V3', 'shopkeeper-portfolio' ) },
		],
		attributes: {
			/* Display by category */
			categoriesIDs: {
				type: 'array',
				default: [],
			},
			/* First Load */
			firstLoad: {
				type: 'boolean',
				default: true
			},
			/* Number of Portfolio Items */
			number: {
				type: 'number',
				default: '12'
			},
			/* Columns */
			columns: {
				type: 'number',
				default: '3'
			},
			/* Filters */
			showFilters: {
				type: 'boolean',
				default: false,
			},
			/* Orderby */
			orderby: {
				type: 'string',
				default: 'date_desc'
			},
		},

		edit: function( props ) {

			let attributes = props.attributes;
			let className  = props.className;

			attributes.categoryOptions 		= attributes.categoryOptions || [];

			if( className.indexOf('is-style-') == -1 ) { className += ' is-style-default'; }

			//==============================================================================
			//	Helper functions
			//==============================================================================

			function _sortCategories( index, arr, newarr = [], level = 0) {
				for ( let i = 0; i < arr.length; i++ ) {
					if ( arr[i].parent == index) {
						arr[i].level = level;
						newarr.push(arr[i]);
						_sortCategories(arr[i].value, arr, newarr, level + 1 );
					}
				}

				return newarr;
			}

			function _isChecked( needle, haystack ) {

				let idx = haystack.indexOf(needle);
				if ( idx != - 1) {
					return true;
				}
				return false;
			}

			function _categoryClassName(parent, value) {
				if ( parent == 0) {
					return 'parent parent-' + value;
				} else {
					return 'child child-' + parent;
				}
			}

			//==============================================================================
			//	Display Categories
			//==============================================================================

			function getCategories() {

				let categories_list = [];
				let options = [];
				let optionsIDs = [];
				let sorted = [];

				apiFetch({ path: '/wp/v2/portfolio-category?per_page=-1' }).then(function (categories) {

				 	for( let i = 0; i < categories.length; i++) {
	        			options[i] = {'label': categories[i].name.replace(/&amp;/g, '&'), 'value': categories[i].id, 'parent': categories[i].parent, 'count': categories[i].count };
				 		optionsIDs[i] = categories[i].id.toString();
				 	}

				 	sorted = _sortCategories(0, options);

		        	props.setAttributes({categoryOptions: sorted });

					if( attributes.firstLoad && attributes.categoriesIDs.length === 0 ) {
						if ( sorted.length > 0 ) {
							for ( let i = 0; i < sorted.length; i++ ) {
								categories_list[i] = sorted[i].value;
							}
						}
			        	props.setAttributes({categoriesIDs: categories_list });
						props.setAttributes({firstLoad: false });

					}
				});
			}

			function renderCategories( parent = 0, level = 0 ) {

				let categoryElements = [];
				let catArr = attributes.categoryOptions;
				if ( catArr.length > 0 )
				{
					for ( let i = 0; i < catArr.length; i++ ) {
						 if ( catArr[i].parent !=  parent ) { continue; };
						categoryElements.push(
							el(
								'li',
								{
									key: 'portfolio_category_' + i,
									className: 'level-' + catArr[i].level,
								},
								el(
								'label',
									{
										key: 'portfolio_cat_label_' + i,
										className: _categoryClassName( catArr[i].parent, catArr[i].value ) + ' ' + catArr[i].level,
									},
									el(
									'input',
										{
											type:  'checkbox',
											key:   'category-checkbox-' + catArr[i].value,
											value: catArr[i].value,
											'data-index': i,
											'data-parent': catArr[i].parent,
											checked: attributes.categoriesIDs.indexOf(catArr[i].value) > -1, // _isChecked(catArr[i].value, attributes.categoriesIDs),
											onChange: function onChange(evt){
												let newCategoriesSelected = attributes.categoriesIDs;
												let checkbox_value = parseInt(evt.target.value);
												let index = newCategoriesSelected.indexOf(checkbox_value);
												if (evt.target.checked === true) {
													if (index == -1) {
														newCategoriesSelected.push(checkbox_value);
													}
												} else {
													if (index > -1) {
														newCategoriesSelected = newCategoriesSelected.filter(function(item) {
    														return item !== checkbox_value;
														});
													}
												}
												console.log(newCategoriesSelected);
												props.setAttributes({ categoriesIDs: newCategoriesSelected });
											},
										},
									),
									catArr[i].label,
									el(
										'sup',
										{},
										catArr[i].count,
									),
								),
								renderCategories( catArr[i].value, level+1)
							),
						);
					}
				}
				if (categoryElements.length > 0 ) {
					let wrapper = el('ul', {className: 'level-' + level}, categoryElements);
					return wrapper;
				} else {
					return;
				}
			}

			return [
				el(
					InspectorControls,
					{
						key: 'sk-portfolio-inspector'
					},
					el(
						'div',
						{
							className: 'main-inspector-wrapper',
						},
						el( 'label', { className: 'components-base-control__label' }, i18n.__( 'Categories:', 'shopkeeper-portfolio' ) ),
						el(
							'div',
							{
								className: 'category-result-wrapper',
							},
							attributes.categoryOptions.length < 1 && getCategories(),
							renderCategories(),
						),
						el(
							SelectControl,
							{
								key: 'sk-latest-posts-order-by',
								options:
									[
										{ value: 'title_asc',   label: i18n.__( 'Alphabetical Ascending', 'shopkeeper-portfolio' ) },
										{ value: 'title_desc',  label: i18n.__( 'Alphabetical Descending', 'shopkeeper-portfolio' ) },
										{ value: 'date_asc',   	label: i18n.__( 'Date Ascending', 'shopkeeper-portfolio' ) },
										{ value: 'date_desc',  	label: i18n.__( 'Date Descending', 'shopkeeper-portfolio' ) },
									],
	              				label: i18n.__( 'Order By', 'shopkeeper-portfolio' ),
	              				value: attributes.orderby,
	              				onChange: function( value ) {
	              					props.setAttributes( { orderby: value } );
								},
							}
						),
						el(
							RangeControl,
							{
								key: "sk-portfolio-number",
								className: 'range-wrapper',
								value: attributes.number,
								allowReset: false,
								initialPosition: 12,
								min: 1,
								max: 20,
								label: i18n.__( 'Number of Portfolio Items', 'shopkeeper-portfolio' ),
								onChange: function onChange(newNumber){
									props.setAttributes( { number: newNumber } );
								},
							}
						),
						el(
							ToggleControl,
							{
								key: "portfolio-filters-toggle",
	              				label: i18n.__( 'Show Filters?', 'shopkeeper-portfolio' ),
	              				checked: attributes.showFilters,
	              				onChange: function() {
									props.setAttributes( { showFilters: ! attributes.showFilters } );
								},
							}
						),
						props.className.indexOf('is-style-default') !== -1 && el(
							RangeControl,
							{
								key: "sk-portfolio-columns",
								value: attributes.columns,
								allowReset: false,
								initialPosition: 3,
								min: 2,
								max: 5,
								label: i18n.__( 'Columns', 'shopkeeper-portfolio' ),
								onChange: function( newColumns ) {
									props.setAttributes( { columns: newColumns } );
								},
							}
						),
					),
				),
				el(
					ServerSideRender,
					{
						key: 'gbt_18_sk_portfolio-render',
						block: 'getbowtied/sk-portfolio',
						attributes: {
							number: attributes.number,
							categoriesIDs: attributes.categoriesIDs,
							showFilters: attributes.showFilters,
							columns: attributes.columns,
							orderby: attributes.orderby,
							className: className
						}
					}
				),
			];
		},

		save: function() {
			return null;
		},
	} );

} )(
	window.wp.blocks,
	window.wp.components,
	window.wp.editor,
	window.wp.i18n,
	window.wp.element,
);
