<?php

add_action( 'init', 'create_portfolio_item' );

function create_portfolio_item() {
	
	$the_slug = get_option( 'gbt_portfolio_item_slug', 'portfolio-item' );

	$labels = array(
		'name' 					=> __('Portfolio', 'shopkeeper-portfolio'),
		'singular_name' 		=> __('Portfolio Item', 'shopkeeper-portfolio'),
		'add_new' 				=> __('Add New', 'shopkeeper-portfolio'),
		'add_new_item' 			=> __('Add New Portfolio item', 'shopkeeper-portfolio'),
		'edit_item' 			=> __('Edit Portfolio item', 'shopkeeper-portfolio'),
		'new_item' 				=> __('New Portfolio item', 'shopkeeper-portfolio'),
		'all_items' 			=> __('All Portfolio items', 'shopkeeper-portfolio'),
		'view_item' 			=> __('View Portfolio item', 'shopkeeper-portfolio'),
		'search_items' 			=> __('Search Portfolio item', 'shopkeeper-portfolio'),
		'not_found' 			=> __('No Portfolio item found', 'shopkeeper-portfolio'),
		'not_found_in_trash' 	=> __('No Portfolio item found in Trash', 'shopkeeper-portfolio'), 
		'parent_item_colon' 	=> '',
		'menu_name' 			=> __('Portfolio', 'shopkeeper-portfolio'),
	);

	$args = array(
		'labels' 				=> $labels,
		'public' 				=> true,
		'publicly_queryable' 	=> true,
		'exclude_from_search' 	=> true,
		'show_ui' 				=> true, 
		'show_in_menu' 			=> true, 
		'show_in_nav_menus' 	=> true,
		'query_var' 			=> true,
		'rewrite' 				=> true,
		'show_in_rest'			=> true,
		'capability_type' 		=> 'post',
		'rest_base'				=> 'portfolio-item',
		'menu_icon'   			=> 'data:image/svg+xml;base64,' . base64_encode('<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>'),
		'has_archive' 			=> true, 
		'hierarchical' 			=> true,
		'menu_position' 		=> 4,
		'supports' 				=> array('title', 'editor', 'block-editor', 'thumbnail', 'revisions'),
		'rewrite' 				=> array('slug' => $the_slug),
		'with_front' 			=> false,
	);
	
	register_post_type('portfolio',$args);
}