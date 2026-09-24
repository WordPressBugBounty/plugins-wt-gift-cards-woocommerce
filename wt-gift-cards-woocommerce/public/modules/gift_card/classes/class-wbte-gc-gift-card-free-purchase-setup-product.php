<?php
/**
 * Store credit purchase as gift card or coupon.
 *
 * @link
 * @since 1.0.0
 *
 * @package  Wbte_Woocommerce_Gift_Cards_Free
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Wbte_Gc_Gift_Card_Free_Purchase' ) ) {
	return;
}

class Wbte_Gc_Gift_Card_Free_Purchase_Setup_Product extends Wbte_Gc_Gift_Card_Free_Purchase {

	private static $instance = null;
	public function __construct() {
		// Make the gift card product purchasable (Without setting any Price)
		add_filter( 'woocommerce_is_purchasable', array( $this, 'make_product_purchasable' ), 10, 2 );

		// Alter the cart item
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'alter_cart_item_data' ), 10, 1 );

		// Make the gift card product virtual by default
		add_filter( 'woocommerce_is_virtual', array( $this, 'make_the_gift_card_product_virtual' ), 10, 2 );
	}

	/**
	 * Get Instance
	 *
	 * @since 1.0.0
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Wbte_Gc_Gift_Card_Free_Purchase_Setup_Product();
		}
		return self::$instance;
	}

	/**
	 *  Make the gift card product purchasable (Without setting any Price)
	 *
	 *  @since  1.0.0
	 *  @param  bool       $purchasable    is purchasable or not
	 *  @param  WC_Product $product        Product object
	 *  @return bool            is purchasable or not
	 */
	public function make_product_purchasable( $purchasable, $product ) {
		return self::is_gift_card_product( $product->get_id() ) ? true : $purchasable;
	}


	/**
	 *  Alter the cart item. Change the product price using user choosed/entered price.
	 *
	 *  @since 1.0.0
	 *  @param  WC_Cart $cart_obj        Cart object
	 */
	public function alter_cart_item_data( $cart_obj ) {
		if ( is_admin() ) {
			return;
		}

		$invalid_item_keys = array();

		foreach ( $cart_obj->get_cart() as $key => $item ) {
			if ( ! isset( $item['wt_credit_amount'] ) ) {
				continue;
			}

			/* Re-check on every recalculation, whatever route added the item. */
			$product_id    = ( isset( $item['product_id'] ) ? absint( $item['product_id'] ) : 0 );
			$product_price = $this->get_validated_credit_amount( $product_id, $item['wt_credit_amount'] );

			if ( false === $product_price ) {
				$invalid_item_keys[] = $key;
				continue;
			}

			$item['data']->set_price( $product_price );
		}

		foreach ( $invalid_item_keys as $key ) {
			$cart_obj->remove_cart_item( $key );
		}
	}

	/**
	 * Make the store credit product virtual by default
	 *
	 *  @since  1.0.0
	 *  @param  bool       $is_virtual        is virtual or not
	 *  @param  WC_Product $product        Product object
	 *  @return bool            is virtual or not
	 */
	public function make_the_gift_card_product_virtual( $is_virtual, $product ) {
		if ( ! $product || ! self::is_gift_card_product( $product->get_id() ) ) {
			return $is_virtual;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
		return apply_filters( 'wt_gc_gift_card_product_is_virtual', true );
	}
}
