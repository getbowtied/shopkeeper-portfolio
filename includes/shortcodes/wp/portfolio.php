<?php

// [portfolio]
function shopkeeper_portfolio_shortcode($atts, $content = null) {

	global $post;

	extract( shortcode_atts( array(
		'items' 					=> '9999',
		'category'					=> '',
		'show_filters' 				=> 'yes',
		'order_by' 					=> 'date',
		'order' 					=> 'desc',
		'grid' 						=> 'default',
		'portfolio_items_per_row' 	=> '3'
	), $atts) );

	ob_start();

	$items_per_row_class = ( 'default' == $grid ) ? ' default_grid items_per_row_' . $portfolio_items_per_row : '';

	if( 'alphabetical' == $order_by) { $order_by = 'title'; }

	$args = array(
		'post_status' 			=> 'publish',
		'post_type' 			=> 'portfolio',
		'posts_per_page' 		=> $items,
		'portfolio_categories' 	=> $category,
		'orderby' 				=> $order_by,
		'order' 				=> $order,
	);

	$portfolio_items = new WP_Query( $args );

	if( $portfolio_items ) {
		?>

		<div class="portfolio-isotope-container<?php echo esc_html($items_per_row_class);?>">
			<?php

			if( empty($category) && ( 'yes' == $show_filters ) ) {
				$categories_list = array();

				while ( $portfolio_items->have_posts() ) {
					$portfolio_items->the_post();
					$terms = get_the_terms( get_the_ID(), 'portfolio_categories' ); // get an array of all the terms as objects.
					if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
						foreach($terms as $term) {
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
	                <?php

	                $post_counter = 0;

	                while ( $portfolio_items->have_posts() ) {
						$portfolio_items->the_post();

						$post_counter++;

						$portfolio_item_width  	= '';
						$portfolio_item_height 	= '';
						$related_thumb 			= wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'large' );
						$item_color_option 		= get_post_meta( $post->ID, 'portfolio_color_meta_box', true ) ? get_post_meta( $post->ID, 'portfolio_color_meta_box', true ) : '';
	                    $item_categories   		= get_the_terms( get_the_ID(), 'portfolio_categories' ); // get an array of all the terms as objects.
	                    $item_categories_list   = '';

	                    if ( !empty( $item_categories ) && !is_wp_error( $item_categories ) ) {
	                        foreach ( $item_categories as $term_slug ) {
	                            $item_categories_list .=  $term_slug->slug . ' ';
	                        }
	                    }

						switch( $grid ) {
							case 'grid1':

								if( ( $post_counter%8 === 0 ) || ( $post_counter === 1 ) ) {
									$portfolio_item_width  = 'width2';
									$portfolio_item_height = 'height2';
								}
								if( ( $post_counter%7 === 0 ) || ( $post_counter === 2 ) ) {
									$portfolio_item_width  = 'width2';
									$portfolio_item_height = '';
								}
								break;

							case 'grid2':

								if( ( $post_counter%19 === 0 ) || ( $post_counter === 3 ) ) {
									$portfolio_item_width  = 'width2';
									$portfolio_item_height = 'height2';
								}
								if( ( $post_counter%8 === 0 ) || ( $post_counter%13 === 0 ) ) {
									$portfolio_item_width  = 'width2';
									$portfolio_item_height = '';
								}
								break;

							case 'grid3':

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
                        <a href="<?php echo esc_url( get_permalink(get_the_ID()) ); ?>" class="portfolio-box-inner hover-effect-link" style="<?php echo !empty($item_color_option) ? 'background-color:' . esc_attr($item_color_option) . ';' : ''; ?>">
                            <div class="portfolio-content-wrapper hover-effect-content">

                                <?php if ( isset($related_thumb[0]) && ($related_thumb[0] != "") ) { ?>
                                    <span class="portfolio-thumb hover-effect-thumb" style="background-image: url(<?php echo esc_url($related_thumb[0]); ?>)"></span>
                                <?php } ?>

                                <h2 class="portfolio-title hover-effect-title"><?php the_title(); ?></h2>
                                <p class="portfolio-categories hover-effect-text"><?php echo strip_tags( get_the_term_list(get_the_ID(), 'portfolio_categories', "", ", ") ); ?></p>

                            </div>
                        </a>
                    </div>

                <?php } ?>
	        </div>

	    </div>

		<?php
	}

	wp_reset_query();
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

add_shortcode( 'portfolio', 'shopkeeper_portfolio_shortcode' );
