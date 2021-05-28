<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//	Enqueue Editor Assets.
add_action( 'enqueue_block_editor_assets', 'gbt_18_sk_portfolio_editor_assets' );
if ( ! function_exists( 'gbt_18_sk_portfolio_editor_assets' ) ) {
	function gbt_18_sk_portfolio_editor_assets() {

		wp_register_script(
			'gbt_18_sk_portfolio_script',
			plugins_url( 'block.js', dirname(__FILE__) ),
			array( 'wp-blocks', 'wp-components', 'wp-editor', 'wp-i18n', 'wp-element' )
		);

		wp_register_style(
			'gbt_18_sk_portfolio_editor_styles',
			plugins_url( 'assets/css/editor.css', dirname(__FILE__) ),
			array( 'wp-edit-blocks' )
		);
	}
}

//	Register Block Type.
if ( function_exists( 'register_block_type' ) ) {
    register_block_type( 'getbowtied/sk-portfolio', array(
        'editor_style'      => 'gbt_18_sk_portfolio_editor_styles',
        'editor_script'     => 'gbt_18_sk_portfolio_script',
        'attributes'      => array(
            'number'                        => array(
                'type'                      => 'integer',
                'default'                   => 12,
            ),
            'categoriesSavedIDs'            => array(
                'type'                      => 'string',
                'default'                   => '',
            ),
            'showFilters'                   => array(
                'type'                      => 'boolean',
                'default'                   => false,
            ),
            'columns'                       => array(
                'type'                      => 'number',
                'default'                   => '3',
            ),
            'align'                         => array(
                'type'                      => 'string',
                'default'                   => 'center',
            ),
            'orderby'                       => array(
                'type'                      => 'string',
                'default'                   => 'date_desc',
            ),
            'className'                     => array(
                'type'                      => 'string',
                'default'                   => 'is-style-default',
            ),
        ),

        'render_callback' => 'gbt_18_sk_render_frontend_portfolio',
    ) );
}

//	Portfolio Helpers.
add_action('rest_api_init', 'gbt_18_sk_register_rest_portfolio_images' );
if ( ! function_exists( 'gbt_18_sk_register_rest_portfolio_images' ) ) {
    function gbt_18_sk_register_rest_portfolio_images(){
        register_rest_field( array('portfolio'),
            'fimg_url',
            array(
                'get_callback'    => 'gbt_18_sk_get_rest_portfolio_featured_image',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }
}

if ( ! function_exists( 'gbt_18_sk_get_rest_portfolio_featured_image' ) ) {
    function gbt_18_sk_get_rest_portfolio_featured_image( $object, $field_name, $request ) {
        if( $object['featured_media'] ){
            $img = wp_get_attachment_image_src( $object['featured_media'], 'large' );
            return $img[0];
        }
        return false;
    }
}
