<?php
/**
 * Template Name: Single Portfolio
 * Template Post Type: portfolio
 *
 * @package shopkeeper-portfolio
 */

global $post;

$portfolio_title_option         = get_post_meta( $post->ID, 'portfolio_title_meta_box_check', true ) ? get_post_meta( $post->ID, 'portfolio_title_meta_box_check', true ) : 'on';
$portfolio_class                = ( 'on' === $portfolio_title_option ) ? 'page-title-shown' : 'page-title-hidden';
$single_post_header_thumb_class = has_post_thumbnail() ? 'with-thumb alignfull' : '';
$page_portfolio_layout          = get_post_meta( get_the_ID(), 'portfolio_layout', true );

get_header();
?>

<div class="<?php echo esc_html( $portfolio_class ); ?>">

	<?php if ( ( 'on' === $portfolio_title_option ) ) { ?>
		<header class="single-post-header entry-header entry-header-portfolio-single <?php echo esc_attr( $single_post_header_thumb_class ); ?>">
			<?php if ( has_post_thumbnail() ) { ?>
				<div class="single-post-header-overlay"></div>
				<?php the_post_thumbnail( 'full' ); ?>
			<?php } ?>

			<div class="single-post-title-wrapper">
				<div class="small-12 large-6 small-centered single-post-title-wrap">
					<div class="post_meta entry-meta">
						<?php $categories = get_the_term_list( get_the_ID(), 'portfolio_categories', '', ', ', '' ); ?>
						<?php if ( ! empty( $categories ) ) { ?>
							<div class="post_meta entry-meta">
								<?php echo wp_kses_post( $categories ); ?>
							</div>
						<?php } ?>
					</div>
					<h1 class="entry-title portfolio_item_title"><?php the_title(); ?></h1>
				</div>
			</div>
		</header>
	<?php } ?>

	<?php if ( 'boxed' === $page_portfolio_layout ) { ?>
		<div class="columns large-centered large-6 post-content-row">
	<?php } ?>

			<div class="entry-content entry-content-portfolio">
				<?php the_content(); ?>
			</div>

	<?php if ( 'boxed' === $page_portfolio_layout ) { ?>
		</div>
	<?php } ?>

	<?php if ( get_next_post() || get_previous_post() ) { ?>
		<nav role="navigation" class="single-post-navigation" aria-label="Posts Navigation">
			<div class="post-navigation-inner">
				<?php if ( get_previous_post() ) { ?>
					<div class="nav-previous">
						<?php previous_post_link( '%link', '<svg class="border-icon" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/></svg>' ); ?>
						<span><?php esc_html_e( 'Previous Post', 'shopkeeper' ); ?></span>
					</div>
				<?php } ?>
				<?php if ( get_next_post() ) { ?>
					<div class="nav-next">
						<span><?php esc_html_e( 'Next Post', 'shopkeeper' ); ?></span>
						<?php next_post_link( '%link', '<svg class="border-icon" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/></svg>' ); ?>
					</div>
				<?php } ?>
			</div>
		</nav>
	<?php } ?>
</div>

<?php get_footer(); ?>
