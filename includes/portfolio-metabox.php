<?php
/**
 * Portfolio custom metaboxes.
 *
 * @package shopkeeper-portfolio
 */

add_action( 'add_meta_boxes', 'shopkeeper_portfolio_add_options_metabox' );
/**
 * Add page metabox.
 */
function shopkeeper_portfolio_add_options_metabox() {
    add_meta_box( 'portfolio_options_meta_box', 'Portfolio Item Options', 'shopkeeper_portfolio_add_page_options_content', 'portfolio', 'side', 'high' );
}

/**
 * Add page option content.
 *
 * @param object $post The page.
 */
function shopkeeper_portfolio_add_page_options_content( $post ) {
    $values = get_post_custom( $post->ID );
	$check = isset($values['portfolio_title_meta_box_check']) ? esc_attr($values['portfolio_title_meta_box_check'][0]) : 'on';
	$portfolio_color_meta_box_value = isset($values['portfolio_color_meta_box']) ? esc_attr($values['portfolio_color_meta_box'][0]) : '';
	$selected = isset($values['page_header_transparency']) ? esc_attr( $values['page_header_transparency'][0]) : '';
    $layout = isset($values['portfolio_layout']) ? esc_attr( $values['portfolio_layout'][0]) : '';
    ?>

    <div class="components-panel__row">
        <div class="components-base-control">
            <div class="components-base-control__field">
                <span class="components-checkbox-control__input-container">
                    <input type="checkbox" id="portfolio_title_meta_box_check" class="components-checkbox-control__input" name="portfolio_title_meta_box_check" <?php checked( $check, 'on' ); ?> />
                </span>
                <label for="portfolio_title_meta_box_check">Show Portfolio Item Title</label>
            </div>
        </div>
    </div>

    <div class="components-panel__row">
        <div class="components-base-control select-control">
            <label for="page_header_transparency" class="components-base-control__label">Layout</label>
            <div class="components-base-control__field">
                <select name="portfolio_layout" id="portfolio_layout" style="width:100%">
                    <option value="full" <?php selected( $layout, 'full' ); ?>>Full Width</option>
                    <option value="boxed" <?php selected( $layout, 'boxed' ); ?>>Boxed</option>
                </select>
            </div>
        </div>
    </div>

    <div class="components-panel__row">
        <div class="components-base-control select-control">
            <label for="page_header_transparency" class="components-base-control__label">Header Transparency</label>
            <div class="components-base-control__field">
                <select name="page_header_transparency" id="page_header_transparency" style="width:100%">
                    <option value="inherit" <?php selected( $selected, 'inherit' ); ?>>Inherit</option>
                    <option value="transparency_light" <?php selected( $selected, 'transparency_light' ); ?>>Light</option>
                    <option value="transparency_dark" <?php selected( $selected, 'transparency_dark' ); ?>>Dark</option>
                    <option value="no_transparency" <?php selected( $selected, 'no_transparency' ); ?>>No Transparency</option>
                </select>
            </div>
        </div>
    </div>

    <div class="components-panel__row">
        <div class="components-base-control">
            <div class="components-base-control__field">
                <label for="portfolio_color_meta_box">Portfolio Item Color</label>
                <input type="text" name="portfolio_color_meta_box" id="portfolio_color_meta_box" value="<?php echo esc_attr($portfolio_color_meta_box_value); ?>" />
            </div>
        </div>
    </div>

    <?php

	// We'll use this nonce field later on when saving.
    wp_nonce_field( 'portfolio_options_meta_box', 'portfolio_options_meta_box_nonce' );
}

add_action( 'save_post', 'shopkeeper_portfolio_save_page_options' );
/**
 * Save page custom options.
 *
 * @param int $post_id The page ID.
 */
function shopkeeper_portfolio_save_page_options($post_id) {
    // Bail if we're doing an auto save.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

    // if our nonce isn't there, or we can't verify it, bail.
    if( ! isset( $_POST['portfolio_options_meta_box_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portfolio_options_meta_box_nonce'] ) ), 'portfolio_options_meta_box' ) ) {
        return;
    }

    // if our current user can't edit this post, bail.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$chk = isset($_POST['portfolio_title_meta_box_check']) ? 'on' : 'off';
    update_post_meta( $post_id, 'portfolio_title_meta_box_check', $chk );

	if( isset( $_POST['page_header_transparency'] ) ) {
        update_post_meta( $post_id, 'page_header_transparency', esc_attr( $_POST['page_header_transparency'] ) );
    }

    if( isset( $_POST['portfolio_layout'] ) ) {
        update_post_meta( $post_id, 'portfolio_layout', esc_attr( $_POST['portfolio_layout'] ) );
    }

	if( isset( $_POST['portfolio_color_meta_box'] ) ) {
        update_post_meta( $post_id, 'portfolio_color_meta_box', wp_kses($_POST['portfolio_color_meta_box'], wp_kses_allowed_html('post') ) );
    }
}
