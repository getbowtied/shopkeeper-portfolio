<?php

add_action( 'init', 'create_portfolio_categories' );

function create_portfolio_categories() {
	
	$labels = array(
		'name'                       => __('Portfolio Categories', 'shopkeeper'),
		'singular_name'              => __('Portfolio Category', 'shopkeeper'),
		'search_items'               => __('Search Portfolio Categories', 'shopkeeper'),
		'popular_items'              => __('Popular Portfolio Categories', 'shopkeeper'),
		'all_items'                  => __('All Portfolio Categories', 'shopkeeper'),
		'edit_item'                  => __('Edit Portfolio Category', 'shopkeeper'),
		'update_item'                => __('Update Portfolio Category', 'shopkeeper'),
		'add_new_item'               => __('Add New Portfolio Category', 'shopkeeper'),
		'new_item_name'              => __('New Portfolio Category Name', 'shopkeeper'),
		'separate_items_with_commas' => __('Separate Portfolio Categories with commas', 'shopkeeper'),
		'add_or_remove_items'        => __('Add or remove Portfolio Categories', 'shopkeeper'),
		'choose_from_most_used'      => __('Choose from the most used Portfolio Categories', 'shopkeeper'),
		'not_found'                  => __('No Portfolio Category found.', 'shopkeeper'),
		'menu_name'                  => __('Portfolio Categories', 'shopkeeper'),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'hierarchical' 			=> true,
		'rest_base'				=> 'portfolio-category',
		'query_var'             => true,
		'show_in_rest'			=> true,
		'rewrite'               => array( 'slug' => 'portfolio-category' ),
	);

	register_taxonomy("portfolio_categories", "portfolio", $args);
}
