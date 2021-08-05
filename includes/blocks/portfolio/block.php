<?php
/**
 * Portfolio block.
 *
 * @package shopkeeper-portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once dirname( __FILE__ ) . '/function-setup.php';

if ( ! function_exists( 'gbt_18_sk_render_frontend_portfolio' ) ) {
	/**
	 * Frontend Output.
	 *
	 * @param array $attributes The attributes.
	 */
	function gbt_18_sk_render_frontend_portfolio( $attributes ) {

		$attributes = shortcode_atts(
			array(
				'number'        => '12',
				'categoriesIDs' => array(),
				'showFilters'   => false,
				'columns'       => '3',
				'align'         => 'center',
				'orderby'       => 'date_desc',
				'className'     => 'is-style-default',
			),
			$attributes
		);

		ob_start();

		$columns_class = ( is_int( strpos( $attributes['className'], 'is-style-default' ) ) ) ? 'default-grid columns-' . $attributes['columns'] : $attributes['className'];

		$args = array(
			'post_status'    => 'publish',
			'post_type'      => 'portfolio',
			'posts_per_page' => $attributes['number'],
		);

		switch ( $attributes['orderby'] ) {
			case 'date_asc':
				$args['orderby'] = 'date';
				$args['order']   = 'asc';
				break;
			case 'date_desc':
				$args['orderby'] = 'date';
				$args['order']   = 'desc';
				break;
			case 'title_asc':
				$args['orderby'] = 'title';
				$args['order']   = 'asc';
				break;
			case 'title_desc':
				$args['orderby'] = 'title';
				$args['order']   = 'desc';
				break;
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'asc';
				break;
		}

		if ( ! empty( $attributes['categoriesIDs'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'portfolio_categories',
					'field'    => 'term_id',
					'terms'    => $attributes['categoriesIDs'],
				),
			);
		}

		$portfolio_items = get_posts( $args );

		if ( ! empty( $portfolio_items ) ) {
			?>

			<div class="wp-block-gbt-portfolio gbt_18_sk_portfolio align<?php echo esc_attr( $attributes['align'] ); ?>">

				<?php
				if ( $attributes['showFilters'] ) {
					$categories_list = array();

					foreach ( $portfolio_items as $post ) {
						$terms = get_the_terms( $post->ID, 'portfolio_categories' ); // get an array of all the terms as objects.
						if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								if ( in_array( $term->term_id, $attributes['categoriesIDs'], true ) ) {
									$categories_list[ $term->slug ] = $term->name;
								}
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
					foreach ( $portfolio_items as $key => $post ) {

						$item_categories      = get_the_terms( $post->ID, 'portfolio_categories' ); // get an array of all the terms as objects.
						$item_categories_list = '';
						if ( ! empty( $item_categories ) && ! is_wp_error( $item_categories ) ) {
							foreach ( $item_categories as $term_slug ) {
								$item_categories_list .= $term_slug->slug . ' ';
							}
						}

						$portfolio_color_option = get_post_meta( $post->ID, 'portfolio_color_meta_box', true ) ? get_post_meta( $post->ID, 'portfolio_color_meta_box', true ) : '';
						$portfolio_color_style  = ! empty( $portfolio_color_option ) ? 'background-color:' . $portfolio_color_option : '';
						?>

						<div class="portfolio-box <?php echo esc_attr( $item_categories_list ); ?>">
							<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="portfolio-box-link" style="<?php echo esc_attr( $portfolio_color_style ); ?>">
								<span class="portfolio-box-content">

									<?php echo wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), 'large' ); ?>

									<h4 class="portfolio-item-title"><?php echo esc_html( $post->post_title ); ?></h4>
									<p class="portfolio-item-categories"><?php echo esc_html( wp_strip_all_tags( get_the_term_list( $post->ID, 'portfolio_categories', '', ', ' ) ) ); ?></p>

								</span>
							</a>
						</div>

					<?php } ?>

				</div>
			</div>

			<?php
		}

		wp_reset_postdata();

		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}
