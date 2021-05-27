<?php

$portfolio_category = $wp_query->queried_object;

$grid = 'default';
$args = array(
    'post_status' 			=> 'publish',
    'post_type' 			=> 'portfolio',
    'posts_per_page' 		=> -1,
    'portfolio_categories' 	=> $portfolio_category->slug,
    'orderby' 				=> 'date',
    'order' 				=> 'desc'
);

$portfolio_items = new WP_Query( $args );

get_header();
?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

        <header class="entry-header">
            <div class="row">
                <div class="large-12 columns">
                    <h1 class="page-title"><?php echo esc_html($portfolio_category->name); ?></h1>
                </div>
            </div>
        </header>

		<div class="portfolio-isotope-container">
			<div class="portfolio-isotope">
				<div class="portfolio-grid-sizer"></div>
					<?php

					$post_counter = 0;

                    if( $portfolio_items ) {

    					while ( $portfolio_items->have_posts() ) {
                            $portfolio_items->the_post();

    						$post_counter++;

    						$related_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'large' );
                            $portfolio_color_option = get_post_meta( $post->ID, 'portfolio_color_meta_box', true ) ? get_post_meta( $post->ID, 'portfolio_color_meta_box', true ) : '';
                            ?>

    						<div class="portfolio-box hidden">
    							<a href="<?php echo esc_url( get_permalink(get_the_ID()) ); ?>" class="portfolio-box-inner hover-effect-link">
    								<span class="portfolio-content-wrapper hover-effect-content" style="<?php echo !empty($portfolio_color_option) ? 'background-color:' . esc_attr($portfolio_color_option) . ';' : ''; ?>">

    									<?php if ( isset($related_thumb[0]) && !empty($related_thumb[0]) ) { ?>
    										<span class="portfolio-thumb hover-effect-thumb" style="background-image: url(<?php echo esc_url($related_thumb[0]); ?>)"></span>
    									<?php } ?>

    									<h2 class="portfolio-title hover-effect-title"><?php the_title(); ?></h2>
    									<p class="portfolio-categories hover-effect-text">
                                            <?php echo strip_tags( get_the_term_list( get_the_ID(), 'portfolio_categories', '', ', ' ) );?>
                                        </p>

    								</span>
    							</a>
    						</div>

    					<?php
                        }
                    }
                    ?>
			</div>
		</div>

    </div>
</div>

<?php get_footer(); ?>
