<?php
/**
 * Gift card HTML for PDF.
 *
 * @link
 * @since 1.1.0
 *
 * @package  Wt_Woocommerce_Gift_Cards
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$wbte_coupon_id  = maybe_unserialize( $args['coupon_id'] );
$wbte_coupon_id  = absint( is_array( $wbte_coupon_id ) ? $wbte_coupon_id[0] : $wbte_coupon_id );
$wbte_coupon_obj = new WC_Coupon( $wbte_coupon_id );

$wbte_coupon_amount = 0;
$wbte_expiry_date   = '';
$wbte_coupon_code   = '';

if ( $wbte_coupon_obj ) {
	$wbte_coupon_code = wc_sanitize_coupon_code( $wbte_coupon_obj->get_code() );
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
	$wbte_coupon_amount = apply_filters( 'wt_gc_alter_giftcard_pdf_price', $wbte_coupon_obj->get_amount(), $wbte_coupon_obj );


	$wbte_expiry      = Wbte_Woocommerce_Gift_Cards_Free_Common::get_store_credit_expiry( $wbte_coupon_obj );
	$wbte_expiry_date = ! is_null( $wbte_expiry ) ? $wbte_expiry->date( 'd M Y' ) : '';

}

$wbte_template       = isset( $args['template'] ) ? $args['template'] : 'general';
$wbte_coupon_message = ( isset( $args['message'] ) ? $args['message'] : Wbte_Gc_Gift_Card_Free_Common::get_gift_card_message( $wbte_template ) );
$wbte_from           = isset( $args['from_name'] ) ? $args['from_name'] : '';
$wbte_reciever_name  = isset( $args['reciever_name'] ) ? $args['reciever_name'] : '';
$wbte_order          = isset( $args['order_id'] ) ? wc_get_order( $args['order_id'] ) : false;
$wbte_order_currency = $wbte_order ? $wbte_order->get_currency() : get_woocommerce_currency();

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php esc_html_e( 'Gift card', 'wt-gift-cards-woocommerce' ); ?></title>
		<style>
		body, html{margin:0px; padding:0px; font-family:Verdana; }

		.wt_gc_pdf_wrapper{ margin:0 auto; max-width:600px; background:#ffffff; line-height:22px; font-size:14px; margin-top:20px; padding:20px; border:solid 1px #ccc; }  
		.wt_gc_pdf_message{width:100%; height:auto; border-left:solid 5px #DEDEDE; font-style:italic; margin-top:25px; padding:5px 15px; }
		.wt_gc_pdf_img{ width:100%; height:auto; margin-top:25px;}
		.wt_gc_pdf_img img{ width:100%;}
		.wt_gc_pdf_coupon_info{ width:100%; height:auto; text-align:left; margin-top:25px;}
		.wt_gc_pdf_coupon_code_block{ text-align:left; }
		.wt_gc_pdf_coupon_code{ font-size:18px; font-weight:400; margin-top:7px; }
		.wt_gc_pdf_price_expiry_block{ text-align:right;}
		.wt_gc_pdf_coupon_price{ font-size:28px; font-weight:700; margin-top:7px; }
		.wt_gc_pdf_coupon_expiry{ margin-top:7px;  }
		.wt_gc_pdf_sender_info, .wt_gc_pdf_bottom{ width:100%; height:auto; text-align:left; margin-top:25px; }
		.wt_gc_pdf_sender_info table td{ padding:5px; }
		<?php
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
		do_action( 'wt_gc_pdf_coupon_css' );
		?>
		</style>
	</head>
<body>
	<div class="wt_gc_pdf_wrapper">
		<div class="wt_gc_pdf_img">
			<?php
			if ( isset( $args['extended'] ) && true === $args['extended'] ) {
				$wbte_pdf_template  = isset( $args['template'] ) ? $args['template'] : 'general';
				$wbte_template_data = Wbte_Gc_Gift_Card_Free_Common::get_gift_card_template( $wbte_pdf_template );
				echo '<img src="' . esc_url( $wbte_template_data['image_url'] ) . '"/>';
			}
			?>
		</div>
		<div class="wt_gc_pdf_coupon_info">
			<table style="width:100%; border-spacing:0px; border-collapse:collapse;" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left" valign="bottom" class="wt_gc_pdf_coupon_code_block">
						<b><?php esc_html_e( 'Gift card code:', 'wt-gift-cards-woocommerce' ); ?></b>
						<div class="wt_gc_pdf_coupon_code"><?php echo esc_html( strtoupper( $wbte_coupon_code ) ); ?></div>
					</td>
					<td align="right" valign="bottom" class="wt_gc_pdf_price_expiry_block">                         
						<b><?php esc_html_e( 'Amount:', 'wt-gift-cards-woocommerce' ); ?></b>
						<div class="wt_gc_pdf_coupon_price"> 
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
						<div class="wt_gc_pdf_coupon_expiry">
							<?php
							/* translators: 1: Expiry date */
							echo ( '' !== $wbte_expiry_date ? sprintf( esc_html__( 'Expiry date: %s', 'wt-gift-cards-woocommerce' ), esc_html( $wbte_expiry_date ) ) : '' );
							?>
						</div>
					</td>
				</tr>
			</table>  
		</div>
		<div class="wt_gc_pdf_sender_info">
			<table style="border-spacing:0px; border-collapse:collapse;" cellpadding="0" cellspacing="0">
				<?php

				if ( '' !== trim( $wbte_from ) ) {
					?>
					<tr>
						<td align="left" valign="bottom"><?php esc_html_e( 'From:', 'wt-gift-cards-woocommerce' ); ?></td>
						<td><?php echo esc_html( $wbte_from ); ?></td>
					</tr>
					<?php
				}

				if ( '' !== trim( $wbte_reciever_name ) ) {
					?>
				<tr>
					<td align="left" valign="bottom"><?php esc_html_e( 'To:', 'wt-gift-cards-woocommerce' ); ?></td>
					<td><?php echo esc_html( $wbte_reciever_name ); ?></td>
				</tr>
					<?php
				}

				if ( '' !== trim( $wbte_coupon_message ) ) {
					?>
				<tr>
					<td align="left" valign="bottom"><?php esc_html_e( 'Message:', 'wt-gift-cards-woocommerce' ); ?></td>
					<td><?php echo esc_html( $wbte_coupon_message ); ?></td>
				</tr>
					<?php
				}
				?>
			</table>
		</div>

		<div class="wt_gc_pdf_bottom">
			<div class="wt_gift_coupon_custom_additional_content">
				<?php
				$wbte_custom_addition_content = __(
					'To redeem this gift card, you can enter the gift card code in the dedicated field during checkout.',
					'wt-gift-cards-woocommerce'
				);

				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
				echo esc_html( apply_filters( 'wt_gc_alter_gift_card_pdf_custom_addition_content', $wbte_custom_addition_content, $wbte_coupon_obj ) );

				?>
							</div>
		</div>
	</div>
	</body>
</html>
