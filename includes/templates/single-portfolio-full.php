<?php

global $post;

$portfolio_title_option = get_post_meta( $post->ID, 'portfolio_title_meta_box_check', true ) ? get_post_meta( $post->ID, 'portfolio_title_meta_box_check', true ) : 'on';
$portfolio_class = ( 'on' === $portfolio_title_option ) ? 'page-title-shown' : 'page-title-hidden';

get_header();

?>

<div class="full-width-page page-portfolio-single <?php echo esc_attr($portfolio_class); ?>">
    <div id="primary" class="content-area">

		<div id="content" class="site-content" role="main">

            <?php if( 'on' === $portfolio_title_option ) { ?>
			    <header class="entry-header entry-header-portfolio-single">
    				<div class="row">
    					<div class="large-10 large-centered columns">
                            <?php $categories = get_the_term_list( get_the_ID(), 'portfolio_categories', '', ', ', '' ); ?>
                            <?php if( !empty($categories) ) { ?>
                                <div class="post_meta entry-meta">
                                    <?php echo wp_kses_post( $categories ); ?>
                                </div>
                            <?php } ?>
                        	<h1 class="page-title portfolio_item_title"><?php the_title(); ?></h1>
    					</div>
    				</div>
			    </header>
            <?php } ?>

			<div class="entry-content entry-content-portfolio">
				<?php the_content(); ?>
            </div>

		</div>

		<?php if( get_next_post() || get_previous_post() ) { ?>
			<div class="row navigation-row">
				<div class="large-12">
					<nav role="navigation" class="single-post-navigation" aria-label="Posts Navigation">
						<div class="post-navigation-inner">
							<?php if( get_previous_post() ) { ?>
								<div class="nav-previous">
									<?php previous_post_link( '%link', '<svg class="border-icon" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/></svg>' ); ?>
									<span><?php esc_html_e( 'Previous Post', 'shopkeeper' ); ?></span>
								</div>
							<?php } ?>
							<?php if( get_next_post() ) { ?>
								<div class="nav-next">
									<span><?php esc_html_e( 'Next Post', 'shopkeeper' ); ?></span>
									<?php next_post_link( '%link', '<svg class="border-icon" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/></svg>' ); ?>
								</div>
							<?php } ?>
						</div>
					</nav>
				</div>
			</div>
		<?php } ?>

	</div>
</div>

<?php get_footer(); ?>
