<?php

/**
* Plugin Name:       		Shopkeeper Portfolio Addon
* Plugin URI:        		https://shopkeeper.wp-theme.design/
* Description:       		Extends the functionality of your WordPress site by adding a 'Portfolio' custom post type allowing you to organize and showcase you your work or products.
* Version:           		1.3.5
* Author:            		GetBowtied
* Author URI:				https://getbowtied.com
* Text Domain:				shopkeeper-portfolio
* Domain Path:				/languages/
* Requires at least: 		5.0
* Tested up to: 			5.8
*
* @package  Shopkeeper Portfolio
* @author   GetBowtied
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Plugin Updater
require 'core/updater/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://raw.githubusercontent.com/getbowtied/shopkeeper-portfolio/master/core/updater/assets/plugin.json',
    __FILE__,
    'shopkeeper-portfolio'
);

if ( ! class_exists( 'Shopkeeper_Portfolio' ) ) :

    /**
    * Shopkeeper_Portfolio class.
    */
    class Shopkeeper_Portfolio {

        /**
        * The single instance of the class.
        *
        * @var Shopkeeper_Portfolio
        */
        protected static $_instance = null;

        /**
        * Shopkeeper_Portfolio constructor.
        *
        */
        public function __construct() {

            add_action( 'init', array( $this, 'register_post_type' ) );
            add_action( 'init', array( $this, 'register_taxonomy' ) );

            $this->add_portfolio_metabox();
            $this->register_portfolio_shortcode();
            $this->register_portfolio_block();

            add_action( 'customize_register', array( $this, 'register_customizer_options' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 300 );

            if ( is_admin() ) {
                add_action( 'enqueue_block_editor_assets', array( $this, 'register_admin_styles' ) );
                add_action( 'enqueue_block_editor_assets', array( $this, 'register_scripts' ) );
            }

            add_filter( 'single_template', array( $this, 'get_portfolio_single_template' ), 99 );
            add_filter( 'taxonomy_template', array( $this, 'get_portfolio_taxonomy_template' ), 99 );

            if ( defined(  'WPB_VC_VERSION' ) ) {
                include_once( dirname(__FILE__) . '/includes/shortcodes/wb/portfolio.php' );
                if( function_exists('vc_set_default_editor_post_types') ) {
                    vc_set_default_editor_post_types( array('post','page','product','portfolio') );
                }
            }
        }

        /**
        * Ensures only one instance of Shopkeeper_Portfolio is loaded or can be loaded.
        *
        * @return Shopkeeper_Portfolio
        */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
        * Creates customizer options.
        *
        * @param object $wp_customize WP Customize.
        */
        public function register_customizer_options( $wp_customize ) {

            // Section
            $wp_customize->add_section( 'portfolio', array(
                'title'       => esc_attr__( 'Portfolio', 'shopkeeper-portfolio' ),
                'priority'    => 20,
            ) );

            // Fields
            $wp_customize->add_setting( 'gbt_portfolio_category_slug', array(
                'type'		 			=> 'option',
                'capability' 			=> 'manage_options',
                'default'     			=> 'portfolio-category',
            ) );

            $wp_customize->add_control(
                new WP_Customize_Control(
                    $wp_customize,
                    'gbt_portfolio_category_slug',
                    array(
                        'type'			=> 'text',
                        'label'       	=> esc_attr__( 'Portfolio Category Slug', 'shopkeeper-portfolio' ),
                        'description' 	=> __('<span class="dashicons dashicons-editor-help"></span>Default slug is "portfolio-category". Enter a custom one to overwrite it. <br/><b>You need to regenerate your permalinks if you modify this!</b>', 'shopkeeper-portfolio'),
                        'section'     	=> 'portfolio',
                        'priority'    	=> 20,
                    )
                )
            );

            $wp_customize->add_setting( 'gbt_portfolio_item_slug', array(
                'type'		 			=> 'option',
                'capability' 			=> 'manage_options',
                'default'     			=> 'portfolio-item',
            ) );

            $wp_customize->add_control(
                new WP_Customize_Control(
                    $wp_customize,
                    'gbt_portfolio_item_slug',
                    array(
                        'type'			=> 'text',
                        'label'       	=> esc_attr__( 'Portfolio Item Slug', 'shopkeeper-portfolio' ),
                        'description' 	=> __('<span class="dashicons dashicons-editor-help"></span>Default slug is "portfolio-item". Enter a custom one to overwrite it. <br/><b>You need to regenerate your permalinks if you modify this!</b>', 'shopkeeper-portfolio'),
                        'section'     	=> 'portfolio',
                        'priority'    	=> 20,
                    )
                )
            );
        }

        /**
        * Registers portfolio post type.
        */
        public static function register_post_type() {
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
        		'menu_icon'   			=> 'dashicons-category',
        		'has_archive' 			=> true,
        		'hierarchical' 			=> true,
        		'menu_position' 		=> 4,
        		'supports' 				=> array('title', 'editor', 'block-editor', 'thumbnail', 'revisions'),
        		'rewrite' 				=> array('slug' => $the_slug),
        		'with_front' 			=> false,
        	);

        	register_post_type( 'portfolio', $args );
        }

        /**
        * Registers portfolio taxonomy.
        */
        public static function register_taxonomy() {
            $the_slug = get_option( 'gbt_portfolio_category_slug', 'portfolio-category' );

        	$labels = array(
        		'name'                       => __('Portfolio Categories', 'shopkeeper-portfolio'),
        		'singular_name'              => __('Portfolio Category', 'shopkeeper-portfolio'),
        		'search_items'               => __('Search Portfolio Categories', 'shopkeeper-portfolio'),
        		'popular_items'              => __('Popular Portfolio Categories', 'shopkeeper-portfolio'),
        		'all_items'                  => __('All Portfolio Categories', 'shopkeeper-portfolio'),
        		'edit_item'                  => __('Edit Portfolio Category', 'shopkeeper-portfolio'),
        		'update_item'                => __('Update Portfolio Category', 'shopkeeper-portfolio'),
        		'add_new_item'               => __('Add New Portfolio Category', 'shopkeeper-portfolio'),
        		'new_item_name'              => __('New Portfolio Category Name', 'shopkeeper-portfolio'),
        		'separate_items_with_commas' => __('Separate Portfolio Categories with commas', 'shopkeeper-portfolio'),
        		'add_or_remove_items'        => __('Add or remove Portfolio Categories', 'shopkeeper-portfolio'),
        		'choose_from_most_used'      => __('Choose from the most used Portfolio Categories', 'shopkeeper-portfolio'),
        		'not_found'                  => __('No Portfolio Category found.', 'shopkeeper-portfolio'),
        		'menu_name'                  => __('Portfolio Categories', 'shopkeeper-portfolio'),
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
        		'rewrite'               => array('slug' => $the_slug),
        	);

        	register_taxonomy( 'portfolio_categories', 'portfolio', $args );
        }

        /**
        * Adds portfolio metabox
        */
        public static function add_portfolio_metabox() {
            require dirname(__FILE__) . '/includes/portfolio-metabox.php';
        }

        /**
        * Registers portfolio shortcode
        *
        * @return void
        */
        public static function register_portfolio_shortcode() {
            include_once( dirname(__FILE__) . '/includes/shortcodes/portfolio.php' );
        }

        /**
        * Loads Gutenberg blocks
        *
        * @return void
        */
        public static function register_portfolio_block() {
            if( class_exists('WP_Block_Type_Registry') ) {
                $registry = new WP_Block_Type_Registry;
                if( !$registry->is_registered( 'getbowtied/sk-portfolio' ) ) {
                    include_once( dirname(__FILE__) . '/includes/blocks/index.php' );
                }
            }
        }

        /**
        * Enqueues portfolio styles
        */
        public static function register_styles() {
            wp_enqueue_style(
                'shopkeeper-portfolio-styles',
                plugins_url( 'assets/css/portfolio.css', __FILE__ ),
                NULL
            );
        }

        /**
        * Registers portfolio admin styles
        */
        public static function register_admin_styles() {
            wp_register_style(
                'shopkeeper_portfolio_editor_styles',
                plugins_url( 'assets/css/portfolio.css', __FILE__ ),
                array( 'wp-edit-blocks' )
            );
        }

        /**
        * Enqueues portfolio scripts
        */
        public static function register_scripts() {
            wp_enqueue_script(
                'shopkeeper-portfolio-scripts',
                plugins_url( 'assets/js/portfolio.js', __FILE__ ),
                array('jquery'),
                false,
                true
            );
        }

        /**
        * Returns portfolio template.
        *
        * @param string $template Template path.
        * @return string Template path.
        */
        public static function get_portfolio_single_template( $template ) {
            global $post;

            if ( $post->post_type == 'portfolio' ) {
                if ( file_exists( plugin_dir_path( __FILE__ ) . '/templates/single-portfolio.php' ) ) {
                    return plugin_dir_path( __FILE__ ) . '/templates/single-portfolio.php';
                }
            }

            return $template;
        }

        /**
        * Returns portfolio taxonomy template.
        *
        * @param string $template Template path.
        * @return string Template path.
        */
        public static function get_portfolio_taxonomy_template( $template ) {

            if( is_tax( 'portfolio_categories' ) ) {
                if ( file_exists( plugin_dir_path( __FILE__ ) . '/templates/taxonomy-portfolio-category.php' ) ) {
                    return plugin_dir_path( __FILE__ ) . '/templates/taxonomy-portfolio-category.php';
                }
            }

            return $template;
        }
    }

endif;

add_action( 'after_setup_theme', function() {
    // Shopkeeper Dependent Components.
    if( class_exists('Shopkeeper') ) {
        $shopkeeper_portfolio = new Shopkeeper_Portfolio;
    }
} );
