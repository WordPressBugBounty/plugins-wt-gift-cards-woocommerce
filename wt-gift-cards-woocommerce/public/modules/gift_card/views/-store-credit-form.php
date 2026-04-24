<?php
/**
 * Gift card purchase form HTML.
 *
 * @link
 * @since 1.0.0
 *
 * @package  Wbte_Woocommerce_Gift_Cards_Free
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wt_gc_gift_card_product_page_form_wrapper">
 
	<input type="hidden" name="wt_gc_gift_card_action" value="email">
	<?php
	$wbte_denominations        = $this->process_denomination_list( $settings['_wt_gc_amounts']['value'] );
	$wbte_is_single_predefined = ( 1 === count( $wbte_denominations ) );
	?>
	<div class="wt_gc_gift_card_product_page_form_item" style="<?php echo esc_attr( $wbte_is_single_predefined && $is_templates_enabled ? 'display:none;' : '' ); ?>">
		<div class="radio-toolbar wt_gc_credit_denominations">
			<label class="wt_gc_credit_amount_label"><?php esc_html_e( 'Amount', 'wt-gift-cards-woocommerce' ); ?></label>
			<?php
			// Only single predefined and on first page load, so set the predefined amount as default value.
			$wbte_wt_credit_amount = ( 0 === $wbte_wt_credit_amount && 0 === $form_submit_triggered && $wbte_is_single_predefined ? $wbte_denominations[0] : $wbte_wt_credit_amount );

			if ( ! $wbte_is_single_predefined || ! $is_templates_enabled ) { // Only when multiple items are available or templates are disabled.
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
				$wbte_credit_denominaton = ( isset( $_REQUEST['credit_denominaton'] ) ? floatval( wp_unslash( $_REQUEST['credit_denominaton'] ) ) : 0 );
				$wbte_i                  = 0;

				foreach ( $wbte_denominations as $wbte_denomination ) {
					$wbte_show_custom_label = true;
					?>
					<span class="wt_gc_credit_denomination">
						<input type="radio" id="denomination_<?php echo esc_attr( $wbte_i ); ?>" name="credit_denominaton" value="<?php echo esc_attr( $wbte_denomination ); ?>" <?php checked( $wbte_credit_denominaton, $wbte_denomination ); ?>> <label class="denominaton_label" for="denomination_<?php echo esc_attr( $wbte_i ); ?>"><?php echo wp_kses_post( wc_price( $wbte_denomination ) ); ?></label>
					</span>
					<?php
					++$wbte_i;
				}
			}
			?>
		</div>                      
	</div> 
	<?php



	/**
	 *  Customizable fields
	 */
	$wbte_fields_enabled = $fields_enabled;
	if ( ! in_array( 'reciever_email', $wbte_fields_enabled, true ) ) {
		$wbte_fields_enabled[] = 'reciever_email'; // Mandatory field.
	}

	foreach ( $all_fields as $wbte_field => $wbte_field_title ) {
		if ( ! in_array( $wbte_field, $wbte_fields_enabled, true ) ) {
			continue; // Field not enabled.
		}

		$wbte_required_attr = ( in_array( $wbte_field, $mandatory_fields, true ) ? ' required="required"' : '' );

		$wbte_field_name  = 'wt_gc_gift_card_' . $wbte_field;
		$wbte_field_title = ( isset( $all_fields[ $wbte_field ] ) ? $all_fields[ $wbte_field ] : '' );

		if ( 'message' === $wbte_field ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$wbte_field_vl = ( isset( $_REQUEST[ $wbte_field_name ] ) ? sanitize_textarea_field( wp_unslash( $_REQUEST[ $wbte_field_name ] ) ) : '' );
			?>
			<div class="wt_gc_gift_card_product_page_form_item">
				<label><?php echo esc_html( $wbte_field_title ); ?></label>
				<textarea name="<?php echo esc_attr( $wbte_field_name ); ?>" class="wt_gc_gift_card_field" id="<?php echo esc_attr( $wbte_field_name ); ?>" placeholder="<?php echo esc_attr( $wbte_field_title ); ?>" <?php echo $wbte_required_attr ? 'required' : ''; ?>><?php echo esc_textarea( $wbte_field_vl ); ?></textarea>
			</div>
			<?php
		} else {
            // phpcs:disable WordPress.Security.NonceVerification.Recommended
			$wbte_field_vl = ( isset( $_REQUEST[ $wbte_field_name ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $wbte_field_name ] ) ) : '' );

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( 'sender_email' === $wbte_field && ! isset( $_REQUEST[ $wbte_field_name ] ) ) {
				$wbte_field_vl = $user_email;
			}

			?>
			<div class="wt_gc_gift_card_product_page_form_item <?php echo esc_attr( $wbte_field_name . '_wt_gc_form_item' ); ?>">
				<label><?php echo esc_html( $wbte_field_title ); ?></label>
				<input type="<?php echo esc_attr( false !== stripos( $wbte_field, 'email' ) ? 'email' : 'text' ); ?>" name="<?php echo esc_attr( $wbte_field_name ); ?>" class="wt_gc_gift_card_field" id="<?php echo esc_attr( $wbte_field_name ); ?>" placeholder="<?php echo esc_attr( $wbte_field_title ); ?>" value="<?php echo esc_attr( $wbte_field_vl ); ?>" <?php echo $wbte_required_attr ? 'required' : ''; ?>/>
			</div>
			<?php
		}
	}

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
	do_action( 'wt_gc_gift_card_after_gift_to_friend_form' );

	?>
	<input type="hidden" name="wt_gc_gift_card_send_today" value="1">
	<?php

	if ( self::is_templates_enabled( $product_id ) ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$wbte_wt_gc_gift_card_image = ( isset( $_REQUEST['wt_gc_gift_card_image'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wt_gc_gift_card_image'] ) ) : '' );
		?>
		<input type="hidden" name="wt_gc_gift_card_image"  id="wt_gc_gift_card_image"  value="<?php echo esc_attr( $wbte_wt_gc_gift_card_image ); ?>" />
		<?php
	}

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
	do_action( 'wt_gc_gift_card_after_send_gift_card_form' );

	?>
	<input type="hidden" name="wt_gift_card_form_submit_triggered" value="<?php echo esc_attr( $form_submit_triggered ); ?>" />
	<input type="hidden" name="wt_credit_amount" id="wt_credit_amount" value="<?php echo esc_attr( $wbte_wt_credit_amount ); ?>" />

	<div style="clear:both;"></div>
</div>
