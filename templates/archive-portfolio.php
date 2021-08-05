<?php
/**
 * The Template for displaying portfolio archives.
 *
 * @package shopkeeper-portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_items = new WP_Query(
	array(
		'post_status'          => 'publish',
		'post_type'            => 'portfolio',
		'posts_per_page'       => -1,
		'orderby'              => 'date',
		'order'                => 'desc',
	)
);

get_header();
?>

<header class="entry-header portfolio-category-header">
	<div class="large-12 large-centered columns">
		<h1 class="page-title portfolio-category-title"><?php esc_html_e( 'Portfolio', 'shopkeeper-portfolio' ); ?></h1>
		<?php
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
		?>
	</div>
</header>

<div class="entry-content entry-content-portfolio-category">

	<?php if ( $portfolio_items->have_posts() ) { ?>

		<div class="portfolio-items-grid columns-5">

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

							<h4 class="portfolio-item-title"><?php the_title(); ?></h4>
							<p class="portfolio-item-categories"><?php echo esc_html( wp_strip_all_tags( get_the_term_list( get_the_ID(), 'portfolio_categories', '', ', ' ) ) ); ?></p>

						</span>
					</a>
				</div>

			<?php } ?>
		</div>
		<?php

	} else {
		?>
		<div class="large-12 columns">
			<h5 class="not-found-text">
				<?php esc_html_e( 'No portfolio items were found matching your selection.', 'shopkeeper-portfolio' ); ?>
			</h5>
		</div>
		<?php
	}
	?>
</div>

<?php get_footer(); ?>
