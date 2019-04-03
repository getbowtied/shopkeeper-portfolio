<?php

add_action( 'init', 'create_portfolio_item' );

function create_portfolio_item() {
	
	$the_slug = get_option( 'gbt_portfolio_item_slug', 'portfolio-item' );

	$labels = array(
		'name' 					=> __('Portfolio', 'shopkeeper'),
		'singular_name' 		=> __('Portfolio Item', 'shopkeeper'),
		'add_new' 				=> __('Add New', 'shopkeeper'),
		'add_new_item' 			=> __('Add New Portfolio item', 'shopkeeper'),
		'edit_item' 			=> __('Edit Portfolio item', 'shopkeeper'),
		'new_item' 				=> __('New Portfolio item', 'shopkeeper'),
		'all_items' 			=> __('All Portfolio items', 'shopkeeper'),
		'view_item' 			=> __('View Portfolio item', 'shopkeeper'),
		'search_items' 			=> __('Search Portfolio item', 'shopkeeper'),
		'not_found' 			=> __('No Portfolio item found', 'shopkeeper'),
		'not_found_in_trash' 	=> __('No Portfolio item found in Trash', 'shopkeeper'), 
		'parent_item_colon' 	=> '',
		'menu_name' 			=> __('Portfolio', 'shopkeeper'),
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
		'has_archive' 			=> true, 
		'hierarchical' 			=> true,
		'menu_position' 		=> 4,
		'supports' 				=> array('title', 'editor', 'block-editor', 'thumbnail', 'revisions'),
		'rewrite' 				=> array('slug' => $the_slug),
		'with_front' 			=> false,
	);
	
	register_post_type('portfolio',$args);
}