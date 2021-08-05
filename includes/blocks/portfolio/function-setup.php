<?php
/**
 * Portfolio block setup.
 *
 * @package shopkeeper-portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'enqueue_block_editor_assets', 'gbt_18_sk_portfolio_editor_assets' );
if ( ! function_exists( 'gbt_18_sk_portfolio_editor_assets' ) ) {
	/**
	 * Enqueue Editor Assets.
	 */
	function gbt_18_sk_portfolio_editor_assets() {

		wp_register_script(
			'gbt_18_sk_portfolio_script',
			plugins_url( 'block.js', __FILE__ ),
			array( 'wp-blocks', 'wp-components', 'wp-editor', 'wp-i18n', 'wp-element' ),
			SK_PORTFOLIO_VERSION,
			true
		);
	}
}

/**
 * Register Block Type.
 */
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'getbowtied/sk-portfolio',
		array(
			'editor_style'    => 'shopkeeper_portfolio_editor_styles',
			'editor_script'   => 'gbt_18_sk_portfolio_script',
			'attributes'      => array(
				'number'        => array(
					'type'    => 'integer',
					'default' => 12,
				),
				'firstLoad'     => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'categoriesIDs' => array(
					'type'    => 'array',
					'default' => array(),
				),
				'showFilters'   => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'columns'       => array(
					'type'    => 'number',
					'default' => '3',
				),
				'align'         => array(
					'type'    => 'string',
					'default' => 'center',
				),
				'orderby'       => array(
					'type'    => 'string',
					'default' => 'date_desc',
				),
				'className'     => array(
					'type'    => 'string',
					'default' => 'is-style-default',
				),
			),

			'render_callback' => 'gbt_18_sk_render_frontend_portfolio',
		)
	);
}

if ( ! function_exists( 'gbt_18_sk_register_rest_portfolio_data' ) ) {
	/**
	 * Portfolio Helpers.
	 */
	function gbt_18_sk_register_rest_portfolio_data() {
		register_rest_field(
			array( 'portfolio' ),
			'fimg_url',
			array(
				'get_callback'    => 'gbt_18_sk_get_rest_portfolio_featured_image',
				'update_callback' => null,
				'schema'          => null,
			)
		);

		register_rest_field(
			array( 'portfolio' ),
			'color_meta_box',
			array(
				'get_callback'    => 'gbt_18_sk_get_rest_portfolio_item_color',
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}
}
add_action( 'rest_api_init', 'gbt_18_sk_register_rest_portfolio_data' );

if ( ! function_exists( 'gbt_18_sk_get_rest_portfolio_featured_image' ) ) {
	/**
	 * Portfolio Featured Image.
	 *
	 * @param array  $object The object.
	 * @param string $field_name Field name.
	 * @param mixed  $request The request.
	 */
	function gbt_18_sk_get_rest_portfolio_featured_image( $object, $field_name, $request ) {
		if ( $object['featured_media'] ) {
			$img = wp_get_attachment_image_src( $object['featured_media'], 'large' );
			return $img[0];
		}
		return false;
	}
}

if ( ! function_exists( 'gbt_18_sk_get_rest_portfolio_item_color' ) ) {
	/**
	 * Portfolio Item Color.
	 *
	 * @param array  $object The object.
	 * @param string $field_name Field name.
	 * @param mixed  $request The request.
	 */
	function gbt_18_sk_get_rest_portfolio_item_color( $object, $field_name, $request ) {
		if ( $object['id'] ) {
			$color = get_post_meta( $object['id'], 'portfolio_color_meta_box', true ) ? get_post_meta( $object['id'], 'portfolio_color_meta_box', true ) : '';
			return $color;
		}
		return false;
	}
}
