<?php
/**
 * Gift card public area
 *
 * @link
 * @since 1.0.0
 *
 * @package  Wbte_Woocommerce_Gift_Cards_Free
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wbte_Gc_Gift_Card_Free_Common' ) ) {
	return;
}

class Wbte_Gc_Gift_Card_Free_Public extends Wbte_Gc_Gift_Card_Free_Common {

	public $module_base             = 'gift_card';
	public $module_id               = '';
	public $module_path             = '';
	public $module_url              = '';
	public static $module_id_static = '';

	private static $instance = null;

	public function __construct() {
		$this->init_vars();

		/**
		 *  Store credit purchase related functions
		 */
		include_once $this->module_path . 'classes/class-wbte-gc-gift-card-free-purchase.php';
		Wbte_Gc_Gift_Card_Free_Purchase::get_instance();
	}

	/**
	 *  Get Instance
	 *
	 *  @since 1.0.0
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Wbte_Gc_Gift_Card_Free_Public();
		}
		return self::$instance;
	}

	/**
	 *  Init mandatory variables. This will be usefull in the included class files
	 *
	 *  @since 1.0.0
	 */
	public function init_vars() {
		$this->module_id        = Wbte_Woocommerce_Gift_Cards_Free_Common::get_module_id( $this->module_base );
		self::$module_id_static = $this->module_id;
		$this->module_path      = plugin_dir_path( __FILE__ );
		$this->module_url       = plugin_dir_url( __FILE__ );
	}


	/**
	 *  Get the form fields list that are enabled on gift card purchase page
	 *
	 *  @since 1.0.0
	 *  @return array of form fields list
	 */
	public static function get_enabled_product_page_fields() {
		return Wbte_Woocommerce_Gift_Cards_Free_Common::get_option( 'fields_to_be_shown', self::$module_id_static );
	}



	/**
	 *  Print gift card product page templates section
	 *
	 *  @since 1.0.0
	 *  @param int   $product_id         Product ID
	 *  @param array $product_suggests   ID of suggested product.
	 *  @param bool  $via_shortcode      Calling via shortcode(Optional) Default: false
	 */
	public function print_gift_card_product_page_templates_section( $product_id, $product_suggests = array(), $via_shortcode = false ) {
		$templates = $this->get_visible_templates( $product_id );

		if ( empty( $templates ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				esc_html_e( 'Error. There is no template(s) selected for this product.', 'wt-gift-cards-woocommerce' );
			}
			return;
		}

		$user         = wp_get_current_user();
		$display_name = ( $user ? $user->display_name : __( 'Your name', 'wt-gift-cards-woocommerce' ) );

		wc_get_template(
			'gift-card.php',
			array(
				'templates'                    => $templates,
				'from'                         => $display_name,
				'product_id'                   => $product_id,
				'preview_html'                 => self::get_gift_card_email_preview(),
				'delete_icon'                  => plugin_dir_url( __FILE__ ) . 'assets/images/custom_template_delete_icon.svg',
				'dummy_img'                    => Wbte_Woocommerce_Gift_Cards_Free_Common::$no_image,
				'gift_card_product_page_title' => Wbte_Woocommerce_Gift_Cards_Free_Common::get_option( 'product_page_title_text', $this->module_id ),
				'templates_main_title'         => Wbte_Woocommerce_Gift_Cards_Free_Common::get_option( 'product_page_templates_title_text', $this->module_id ),
				'how_to_send_title_text'       => Wbte_Woocommerce_Gift_Cards_Free_Common::get_option( 'product_page_how_to_send_title_text', $this->module_id ),
				'via_shortcode'         => $via_shortcode,
			),
			'',
			$this->module_path . 'templates/'
		);
	}


	/**
	 *  Get product object on single product page. In some themes, '$product' is a string instead of an object.
	 *
	 *  @since 1.0.0
	 *  @return object|null     WC_Product object
	 */
	public static function get_product_object() {

		global $product, $post;

		if ( is_product() ) {

			if ( ! is_object( $product ) ) { // In some themes the $product object is not ready
				$product = wc_get_product( $post->ID );
			}

			return $product;
		}

		return null;
	}
}

Wbte_Gc_Gift_Card_Free_Public::get_instance();
