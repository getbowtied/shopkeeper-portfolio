<?php
/**
 * The Template for displaying portfolio archives.
 *
 * @package shopkeeper-portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_category = $wp_query->queried_object;
$portfolio_items    = new WP_Query(
	array(
		'post_status'          => 'publish',
		'post_type'            => 'portfolio',
		'posts_per_page'       => -1,
		'portfolio_categories' => $portfolio_category->slug,
		'orderby'              => 'date',
		'order'                => 'desc',
	)
);

get_header();
?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

		<header class="entry-header portfolio-category-header">
			<div class="row">
				<div class="large-12 columns">

					<h1 class="page-title portfolio-category-title"><?php echo esc_html( $portfolio_category->name ); ?></h1>

					<?php if ( isset( $portfolio_category->description ) && ! empty( $portfolio_category->description ) ) : ?>
						<div class="row">
							<div class="large-8 xlarge-6 large-centered columns">
								<div class="term-description"><?php echo esc_html( $portfolio_category->description ); ?></div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</header>

		<div class="entry-content entry-content-portfolio-category">

			<?php if ( $portfolio_items->have_posts() ) { ?>

				<div class="portfolio-items-grid columns-5">

					<?php
					while ( $portfolio_items->have_posts() ) {
						$portfolio_items->the_post();

						$portfolio_color_option = get_post_meta( get_the_ID(), 'portfolio_color_meta_box', true ) ? get_post_meta( get_the_ID(), 'portfolio_color_meta_box', true ) : '';
						$portfolio_color_style  = ! empty( $portfolio_color_option ) ? 'background-color:' . $portfolio_color_option : '';
						?>

						<div class="portfolio-box">
							<a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" class="portfolio-box-link" style="<?php echo esc_attr( $portfolio_color_style ); ?>">
								<span class="portfolio-box-content">

									<?php echo wp_get_attachment_image( get_post_thumbnail_id( get_the_ID() ), 'large' ); ?>

									<h2 class="portfolio-item-title"><?php the_title(); ?></h2>
									<p class="portfolio-item-categories"><?php echo esc_html( wp_strip_all_tags( get_the_term_list( get_the_ID(), 'portfolio_categories', '', ', ' ) ) ); ?></p>

								</span>
							</a>
						</div>

					<?php } ?>
				</div>
				<?php

			} else {
				?>
				<div class="row">
					<div class="large-12 columns">
						<h5 class="not-found-text">
							<?php esc_html_e( 'No portfolio items were found matching your selection.', 'shopkeeper-portfolio' ); ?>
						</h5>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
