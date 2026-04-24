<?php
/**
 * HTML gift card email (extended template image).
 *
 * @package Wbte_Woocommerce_Gift_Cards_Free
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook.
do_action( 'woocommerce_email_header', $email_heading, $email );


$wbte_coupon_id  = isset( $email_args['coupon_id'] ) ? maybe_unserialize( $email_args['coupon_id'] ) : '';
$wbte_coupon_id  = absint( is_array( $wbte_coupon_id ) ? $wbte_coupon_id[0] : $wbte_coupon_id );
$wbte_coupon_obj = new WC_Coupon( $wbte_coupon_id );

$wbte_coupon_amount = 0;
$wbte_expiry_date   = '';
$wbte_coupon_title  = '';

if ( $wbte_coupon_obj ) {
	$wbte_coupon_title = wc_sanitize_coupon_code( $wbte_coupon_obj->get_code() );
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
	$wbte_coupon_amount = apply_filters( 'wt_gc_alter_giftcard_email_price', $wbte_coupon_obj->get_amount(), $wbte_coupon_obj );


	$wbte_expiry      = Wbte_Woocommerce_Gift_Cards_Free_Common::get_store_credit_expiry( $wbte_coupon_obj );
	$wbte_expiry_date = ! is_null( $wbte_expiry ) ? $wbte_expiry->date( 'd M Y' ) : '';
}

$wbte_template = isset( $email_args['template'] ) ? $email_args['template'] : 'general';

$wbte_coupon_title   = ( ! $wbte_coupon_title ? 'XXXX-XXXX-XXXX-XXXX' : $wbte_coupon_title ); // For email preview.
$wbte_coupon_message = ( isset( $email_args['message'] ) ? $email_args['message'] : Wbte_Gc_Gift_Card_Free_Common::get_gift_card_message( $wbte_template ) );
$wbte_from           = isset( $email_args['from_name'] ) ? $email_args['from_name'] : '';
$wbte_reciever_name  = isset( $email_args['reciever_name'] ) ? $email_args['reciever_name'] : '';
$wbte_order = isset( $email_args['order_id'] ) ? wc_get_order( $email_args['order_id'] ) : false;
$wbte_order_currency = $wbte_order ? $wbte_order->get_currency() : get_woocommerce_currency();
?>
<div class="wt_gc_email_wrapper">
	<div class="wt_gc_email_wrapper_inner">
		<div class="wt_gc_email_top">
			
			<div class="wt_gc_reciever_name_block">
				<?php

				/* Greetings */
				if ( '' !== $wbte_reciever_name ) {
					/* translators: %s reciever name wrapped by HTML span. */
					echo wp_kses_post( sprintf( __( 'Hi %s,', 'wt-gift-cards-woocommerce' ), '<span class="wt_gc_reciever_name">' . $wbte_reciever_name . '</span>' ) );
				} else {
					echo esc_html__( 'Hi there,', 'wt-gift-cards-woocommerce' );	
				}

				?>
			</div>

			<div class="wt_gc_from_name_block">
				<?php

				/* from */
				if ( '' !== $wbte_from ) {
					/* translators: 1.HTML tag open, 2. HTML tag closing */
					echo wp_kses_post( sprintf( __( 'Congratulations! You have received a gift card %1$sfrom %2$s.', 'wt-gift-cards-woocommerce' ), '<span class="wt_gc_from_name_box"><span class="wt_gc_from_name_prefix">', '</span><span class="wt_gc_from_name"> ' . $wbte_from . '</span></span>' ) );
				} else {
					/* translators: HTML code */
					echo esc_html__( 'Congratulations! You have received a gift card.', 'wt-gift-cards-woocommerce' );	
				}

				?>
			</div>

			<div class="wt_gc_email_message" style="<?php echo esc_attr( '' === $wbte_coupon_message ? 'display: none;' : '' ); ?>">
				<?php echo wp_kses_post( $wbte_coupon_message ); ?>
			</div>
		</div>
		<div class="wt_gc_email_img">
			<?php
			if ( isset( $email_args['extended'] ) && true === $email_args['extended'] ) {
				$wbte_img_template      = isset( $email_args['template'] ) ? $email_args['template'] : '';
				$wbte_img_template_data = Wbte_Gc_Gift_Card_Free_Common::get_gift_card_template( $wbte_img_template );
				if ( ! empty( $wbte_img_template_data['image_url'] ) ) {
					echo wp_kses_post( '<img src="' . esc_attr( $wbte_img_template_data['image_url'] ) . '" />' );
				}
			}
			?>
		</div>
		<?php
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
		do_action( 'wt_gc_gift_card_email_content', $wbte_coupon_obj );
		?>
		<div class="wt_gc_email_bottom">
			<table style="width:100%; border-spacing:0px; border-collapse:collapse;" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left" valign="bottom" class="wt_gc_email_coupon_code_block">
						<b><?php esc_html_e( 'Gift card code:', 'wt-gift-cards-woocommerce' ); ?></b>
						<div class="wt_gc_email_coupon_code"><?php echo esc_html( strtoupper( $wbte_coupon_title ) ); ?></div>
						<?php
						// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
						do_action( 'wt_gc_gift_card_email_after_coupon_code', $wbte_coupon_obj );
						?>
					</td>
					<td align="right" valign="bottom" class="wt_gc_email_price_expiry_block">
						<b><?php esc_html_e( 'Amount:', 'wt-gift-cards-woocommerce' ); ?></b>
						<div class="wt_gc_email_coupon_price"> 
							<?php
							$wbte_price_args = array(
								'coupon'   => $wbte_coupon_obj,
								'currency' => $wbte_order_currency,
								'order'    => $wbte_order,
								'product'  => null,
							);
							echo wp_kses_post( Wbte_Woocommerce_Gift_Cards_Free_Common::get_giftcard_price( $wbte_price_args ) );
							?>
						</div>
						<div class="wt_gc_email_coupon_expiry">
							<?php
							/* translators: %s Expiry date */
							echo wp_kses_post( '' !== $wbte_expiry_date ? sprintf( __( 'Expiry date: %s', 'wt-gift-cards-woocommerce' ), $wbte_expiry_date ) : '' );
							?>
						</div>
					</td>
				</tr>
			</table>  
		</div>
	</div>

	<div class="wt_gc_email_wrapper_inner">
		<div class="wt_gift_coupon_additional_content">     
			<div class="wt_gift_coupon_custom_additional_content">
				<?php
					$wbte_custom_addition_content = __( 'To redeem this gift card, you can enter the gift card code in the dedicated field during the checkout.', 'wt-gift-cards-woocommerce' );
					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
					echo wp_kses_post( apply_filters( 'wt_gc_alter_gift_card_email_custom_addition_content', $wbte_custom_addition_content, $wbte_coupon_obj ) );
				?>
				  
			</div>

			<?php
			/**
			 * Show user-defined additional content - Alter this content on WC email's settings.
			 */
			if ( $additional_content ) {
				echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
			}
			?>
		</div>
	</div>
</div>


<?php
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook.
do_action( 'woocommerce_email_footer', $email );
?>
