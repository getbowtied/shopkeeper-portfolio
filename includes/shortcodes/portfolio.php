<?php
/**
 * Portfolio shortcode.
 *
 * @package shopkeeper-portfolio
 */

add_shortcode( 'portfolio', 'shopkeeper_portfolio_shortcode' );
/**
 * Portfolio shortcode output.
 *
 * @param array $atts Shortcode attributes.
 * @return string Shortcode output.
 */
function shopkeeper_portfolio_shortcode( $atts ) {
	extract(
		shortcode_atts(
			array(
				'items'                   => '9999',
				'category'                => '',
				'show_filters'            => 'yes',
				'order_by'                => 'date',
				'order'                   => 'desc',
				'grid'                    => 'default',
				'portfolio_items_per_row' => '3',
			),
			$atts
		)
	);

	ob_start();

	$columns_class = ( 'default' === $grid ) ? 'default-grid columns-' . $portfolio_items_per_row : 'masonry-' . $grid;
	$order_by      = ( 'alphabetical' === $order_by ) ? 'title' : $order_by;

	$portfolio_items = new WP_Query(
		array(
			'post_status'          => 'publish',
			'post_type'            => 'portfolio',
			'posts_per_page'       => $items,
			'portfolio_categories' => $category,
			'orderby'              => $order_by,
			'order'                => $order,
		)
	);

	if ( $portfolio_items->have_posts() ) {

		if ( empty( $category ) && ( 'yes' === $show_filters ) ) {
			$categories_list = array();

			while ( $portfolio_items->have_posts() ) {
				$portfolio_items->the_post();
				$terms = get_the_terms( get_the_ID(), 'portfolio_categories' ); // get an array of all the terms as objects.
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$categories_list[ $term->slug ] = $term->name;
					}
				}
			}

			$categories_list = array_unique( $categories_list );

			if ( ! empty( $categories_list ) && is_array( $categories_list ) && ! is_wp_error( $categories_list ) ) {
				?>
				<div class="portfolio-filters list_categories_wrapper">
					<ul class="filters-group list_categories list-centered">
						<li class="filter-item is-checked" data-filter="*"><span><?php esc_html_e( 'Show all', 'shopkeeper-portfolio' ); ?></span></li>
						<?php foreach ( $categories_list as $key => $value ) { ?>
							<li class="filter-item" data-filter="<?php echo esc_attr( $key ); ?>"><span><?php echo esc_html( $value ); ?></span></li>
						<?php } ?>
					</ul>
				</div>
				<?php
			}
		}
		?>

		<div class="portfolio-items-grid <?php echo esc_html( $columns_class ); ?>">
			<?php
			while ( $portfolio_items->have_posts() ) {
				$portfolio_items->the_post();

				$item_categories      = get_the_terms( get_the_ID(), 'portfolio_categories' ); // get an array of all the terms as objects.
				$item_categories_list = '';
				if ( ! empty( $item_categories ) && ! is_wp_error( $item_categories ) ) {
					foreach ( $item_categories as $term_slug ) {
						$item_categories_list .= $term_slug->slug . ' ';
					}
				}

				$portfolio_color_option = get_post_meta( get_the_ID(), 'portfolio_color_meta_box', true ) ? get_post_meta( get_the_ID(), 'portfolio_color_meta_box', true ) : '';
				$portfolio_color_style  = ! empty( $portfolio_color_option ) ? 'background-color:' . $portfolio_color_option : '';
				?>

				<div class="portfolio-box <?php echo esc_attr( $item_categories_list ); ?>">
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
	}

	wp_reset_postdata();

	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
