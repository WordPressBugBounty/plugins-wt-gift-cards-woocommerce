<?php
/**
 * Plain-text gift card email body.
 *
 * @package Wbte_Woocommerce_Gift_Cards_Free
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$wbte_coupon_id  = maybe_unserialize( $email_args['coupon_id'] );
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

echo esc_html( $email_heading ) . "\n\n";


/* Greetings */
if ( '' !== $wbte_reciever_name ) {
	/* translators: %s reciever name */
	echo esc_html( sprintf( __( 'Hi %s,', 'wt-gift-cards-woocommerce' ), $wbte_reciever_name ) );
} else {
	esc_html_e( 'Hi there,', 'wt-gift-cards-woocommerce' );
}

echo "\n\n"; // phpcs:ignore

/* from */
if ( '' !== $wbte_from ) {
	/* translators: from name */
	echo esc_html( sprintf( __( 'Congratulations! You have received a store credit from %s.', 'wt-gift-cards-woocommerce' ), $wbte_from ) );
} else {
	echo esc_html__( 'Congratulations! You have received a store credit.', 'wt-gift-cards-woocommerce' );
}

echo "\n\n"; // phpcs:ignore


echo esc_html( $wbte_coupon_message ) . "\n\n"; // phpcs:ignore


echo esc_html__( 'Coupon code:', 'wt-gift-cards-woocommerce' ) . esc_html( strtoupper( $wbte_coupon_title ) ) . "\n\n";


echo esc_html__( 'Amount:', 'wt-gift-cards-woocommerce' ) . esc_html( $wbte_coupon_amount ) . "\n\n";

/* translators: %s Expiry date */
echo esc_html( '' !== $wbte_expiry_date ? sprintf( __( 'Expiry date: %s', 'wt-gift-cards-woocommerce' ), $wbte_expiry_date ) . "\n\n" : '' );


$wbte_custom_addition_content = __( 'To redeem this store credit, you can enter the coupon code in the dedicated field during the checkout.', 'wt-gift-cards-woocommerce' );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
echo esc_html( apply_filters( 'wt_gc_alter_gift_card_email_custom_addition_content', $wbte_custom_addition_content, $wbte_coupon_obj ) );



// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook.
echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
