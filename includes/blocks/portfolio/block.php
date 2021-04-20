<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once( dirname(__FILE__) . '/functions/function-setup.php' );

//  Frontend Output.
if ( ! function_exists( 'gbt_18_sk_render_frontend_portfolio' ) ) {
    function gbt_18_sk_render_frontend_portfolio( $attributes ) {

        extract( shortcode_atts( array(
            'number'                    => '12',
            'categoriesSavedIDs'        => '',
            'showFilters'               => false,
            'columns'                   => '3',
            'align'                     => 'center',
            'orderby'                   => 'date_desc',
            'className'                 => 'is-style-default'
        ), $attributes) );
        ob_start();

        $items_per_row_class = strpos($className, 'is-style-default') ? 'default_grid items_per_row_' . $columns : '';

        if( substr($categoriesSavedIDs, - 1) == ',' ) {
            $categoriesSavedIDs = substr( $categoriesSavedIDs, 0, -1);
        }

        if( substr($categoriesSavedIDs, 0, 1) == ',' ) {
            $categoriesSavedIDs = substr( $categoriesSavedIDs, 1);
        }

        $args = array(
            'post_status'    => 'publish',
            'post_type'      => 'portfolio',
            'posts_per_page' => $number
        );

        switch ( $orderby ) {
            case 'date_asc' :
                $args['orderby'] = 'date';
                $args['order']   = 'asc';
                break;
            case 'date_desc' :
                $args['orderby'] = 'date';
                $args['order']   = 'desc';
                break;
            case 'title_asc' :
                $args['orderby'] = 'title';
                $args['order']   = 'asc';
                break;
            case 'title_desc':
                $args['orderby'] = 'title';
                $args['order']   = 'desc';
                break;
            default: break;
        }

        if( $categoriesSavedIDs != '' ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy'  => 'portfolio_categories',
                    'field'     => 'term_id',
                    'terms'     => explode(",",$categoriesSavedIDs)
                ),
           );
        }

        $portfolio_items = get_posts( $args );

        if( !empty($portfolio_items) ) {

            ?>

            <!-- Wrappers -->
            <div class="gbt_18_sk_portfolio wp-block-gbt-portfolio <?php echo $className; ?> align<?php echo $align; ?>">
                <div class="portfolio-isotope-container gbt_18_sk_portfolio_container <?php echo $items_per_row_class ;?>">
                    <?php

                    if( $showFilters ) {
                        $categories_list = array();

                        foreach( $portfolio_items as $post ) {
                            $terms = get_the_terms( $post->ID, 'portfolio_categories' ); // get an array of all the terms as objects.
                            if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
                                foreach( $terms as $term ) {
                                    $categories_list[$term->slug] = $term->name;
                                }
                            }
                        }

                        $categories_list = array_unique($categories_list);

                        if( !empty($categories_list) && is_array($categories_list) && !is_wp_error($categories_list) ) {
                            ?>
        					<div class="portfolio-filters list_categories_wrapper">
        						<ul class="filters-group list_categories list-centered">
        							<li class="filter-item is-checked" data-filter="*"><span><?php esc_html_e( 'Show all', 'shopkeeper-portfolio' ); ?></span></li>
        							<?php foreach ( $categories_list as $key => $value ) { ?>
        								<li class="filter-item" data-filter=".<?php echo esc_attr($key); ?>"><span><?php echo esc_html($value); ?></span></li>
        							<?php } ?>
        						</ul>
        			        </div>
        					<?php
                        }
                    }
                    ?>

                    <div class="portfolio-isotope">
                        <div class="portfolio-grid-sizer"></div>

                        <div class="portfolio-grid-items">
                            <?php

                            foreach( $portfolio_items as $key => $post ) {

                                $post_counter = $key+1;

                                $portfolio_item_width  	= '';
            					$portfolio_item_height 	= '';
                                $item_color_option 		= get_post_meta( $post->ID, 'portfolio_color_meta_box', true ) ? get_post_meta( $post->ID, 'portfolio_color_meta_box', true ) : '';
                                $related_thumb          = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
                                $item_categories        = get_the_terms( $post->ID, 'portfolio_categories' ); // get an array of all the terms as objects.
                                $item_categories_list   = '';

                                if ( !empty( $item_categories ) && !is_wp_error( $item_categories ) ) {
                                    foreach ( $item_categories as $term_slug ) {
                                        $item_categories_list .=  $term_slug->slug . ' ';
                                    }
                                }

                                switch( $className ) {
            						case 'is-style-masonry_1':

            							if( ( $post_counter%8 === 0 ) || ( $post_counter === 1 ) ) {
            								$portfolio_item_width  = 'width2';
            								$portfolio_item_height = 'height2';
            							}
            							if( ( $post_counter%7 === 0 ) || ( $post_counter === 2 ) ) {
            								$portfolio_item_width  = 'width2';
            								$portfolio_item_height = '';
            							}
            							break;

            						case 'is-style-masonry_2':

            							if( ( $post_counter%19 === 0 ) || ( $post_counter === 3 ) ) {
            								$portfolio_item_width  = 'width2';
            								$portfolio_item_height = 'height2';
            							}
            							if( ( $post_counter%8 === 0 ) || ( $post_counter%13 === 0 ) ) {
            								$portfolio_item_width  = 'width2';
            								$portfolio_item_height = '';
            							}
            							break;

            						case 'is-style-masonry_3':

            							if ( ( $post_counter === 3 ) || ( $post_counter%8 === 0 ) || ( $post_counter%11 === 0 ) || ( $post_counter%14 === 0 ) ) {
            								$portfolio_item_width = 'width2';
            								$portfolio_item_height = '';
            							}
            							break;

            						default:

            							$portfolio_item_width = '';
            							$portfolio_item_height = '';
            							break;
            					}
                                ?>

                                <div class="portfolio-box hidden <?php echo esc_attr($portfolio_item_width); ?> <?php echo esc_attr($portfolio_item_height); ?> <?php echo esc_attr($item_categories_list); ?>">
                                    <a href="<?php echo esc_url( get_permalink($post->ID) ); ?>" class="portfolio-box-inner hover-effect-link" style="<?php echo !empty($item_color_option) ? 'background-color:' . esc_attr($item_color_option) . ';' : ''; ?>">
                                        <div class="portfolio-content-wrapper hover-effect-content">

                                            <?php if ( isset($related_thumb[0]) && ($related_thumb[0] != "") ) { ?>
            	                                <span class="portfolio-thumb hover-effect-thumb" style="background-image: url(<?php echo esc_url($related_thumb[0]); ?>)"></span>
            	                            <?php } ?>

                                            <h2 class="portfolio-title hover-effect-title"><?php echo $post->post_title; ?></h2>
                                            <p class="portfolio-categories hover-effect-text"><?php echo strip_tags( get_the_term_list($post->ID, 'portfolio_categories', "", ", ") ); ?></p>

                                        </div>
                                    </a>
                                </div>

                            <?php } ?>

                        </div>
                    </div>

                <!-- Wrappers -->
                </div>
            </div>

            <?php
        }

        wp_reset_query();

        return ob_get_clean();
    }
}
