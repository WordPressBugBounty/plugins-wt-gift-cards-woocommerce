<?php
/**
 * Gift card product page template
 * Version: 1.0.1
 *
 * @link
 * @since 1.0.0
 * @package  Wbte_Woocommerce_Gift_Cards_Free
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
?>
<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders. ?>
<div class="wt_gc_gift_card_product_page_wrapper <?php echo esc_attr( apply_filters( 'wt_gc_add_gift_card_product_page_css_class', '' ) ); ?>">
	<div class="wt_gc_gift_card_product_page_title">
		<?php
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy wt_gc hook for extenders.
		$wbte_gift_card_product_page_title = apply_filters( 'wt_gc_alter_gift_card_product_page_title', $wbte_gift_card_product_page_title, $product_id );
		if ( '' === $wbte_gift_card_product_page_title ) {
			$wbte_title_product = wc_get_product( $product_id );
			if ( $wbte_title_product ) {
				?>
					<h1><?php echo wp_kses_post( $wbte_title_product->get_name() ); ?></h1>
				<?php
			}
		} else {
			?>
					<h1><?php echo wp_kses_post( $wbte_gift_card_product_page_title ); ?></h1>
			<?php
		}
		?>
	</div>
	<div class="wt_gc_gift_card_product_page_bottom">
		<div class="wt_gc_gift_card_product_page_preview_wrapper">
			<div class="wt_gc_email_preview">              
				<?php echo wp_kses_post( $preview_html ); ?>       
			</div>
		</div>
		<div class="wt_gc_gift_card_product_page_form">
			<h2 style="<?php echo esc_attr( 1 === count( $templates ) ? 'display:none;' : '' ); ?>"><?php
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy wt_gc hook for extenders.
			echo wp_kses_post( apply_filters( 'wt_gc_gift_card_product_page_templates_main_title', $templates_main_title, $product_id ) );
			?></h2>
			<div style="<?php echo esc_attr( 1 === count( $templates ) || empty( $templates ) ? 'display:none;' : '' ); ?>" class="wt_gc_gift_card_product_page_templates_container">
				
				<div class="wt_gc_carousal wt_gc_gift_card_product_page_templates">
					<div class="wt_gc_carousal_inner wt_gc_gift_card_product_page_templates_inner">
						<?php
						$wbte_i = 0;

						foreach ( $templates as $wbte_template_key => $wbte_template ) {
							$wbte_class = 'wt_gc_carousal_item ';

							if ( 0 === $wbte_i ) {
								$wbte_class .= 'active';
							}

							echo '<div class="' . esc_attr( $wbte_class ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo '<img design="' . esc_attr( $wbte_template_key ) . '" src="' . esc_url( $wbte_template['image_url'] ) . '" alt="' . esc_attr( $wbte_template_key ) . '"/>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							++$wbte_i;
						}
						?>
					</div>
				</div>
			</div>

			<h2 class="wt_gc_gift_card_product_page_form_hd"><?php echo wp_kses_post( $how_to_send_title_text ); ?></h2>
			<?php 

            /**
             *  Added compatibility while printing the template via shortcode
             * 
             *  @since 1.1.0
             */
            if ( isset( $via_shortcode ) && true === $via_shortcode ) {
                
                // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- WooCommerce core template global for shortcode add-to-cart.
                global $product;
                $backup_product = $product; // Maybe null value
                $product        = wc_get_product( $product_id ); // Create product variable from product ID.
                // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

                // Change form action to avoid redirect.
                add_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
            }
                 
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy wt_gc hook for extenders.
            do_action('wt_gc_gift_card_setup_form'); 

            
            if ( isset( $via_shortcode ) && true === $via_shortcode ) {
                // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Restore WooCommerce template global after shortcode form.
                // Re-assign the existing product value.
                $product = $backup_product;
                // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

                remove_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
            }
            ?>
		</div>
	</div>    
</div>