<?php
/**
 * Class Wt_Invoice_Cta_Banner
 *
 * This class is responsible for displaying the CTA banner on the order edit page.
 *
 * @package Wbte_Woocommerce_Gift_Cards_Free
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wt_Invoice_Cta_Banner' ) ) {

	/**
	 * Class Wt_Invoice_Cta_Banner
	 */
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	class Wt_Invoice_Cta_Banner {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Check if premium plugin is active.
			/**
			 * Filter hook to alter active plugins.
			 *
			 * @since 2.2.2
			 * @param array $active_plugins Active plugins.
			 * @return array Active plugins.
			 */
			if ( ! in_array( 'wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			) {

				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
				add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
				add_action( 'wp_ajax_wt_dismiss_invoice_cta_banner', array( $this, 'dismiss_banner' ) );
			}
		}
		/**
		 * Enqueue required scripts and styles.
		 */
		public function enqueue_scripts() {

			$current_screen = get_current_screen();

			// Check if current screen is allowed.
			if ( 'woocommerce_page_wc-orders' !== $current_screen->id && 'shop_order' !== $current_screen->id ) {
				return;
			}

			wp_enqueue_style(
				'wt-wbte-cta-banner',
				plugin_dir_url( __FILE__ ) . 'assets/css/wbte-cross-promotion-banners.css',
				array(),
				Wbte_Cross_Promotion_Banners::get_banner_version(),
			);

			wp_enqueue_script(
				'wt-wbte-cta-banner',
				plugin_dir_url( __FILE__ ) . 'assets/js/wbte-cross-promotion-banners.js',
				array( 'jquery' ),
				Wbte_Cross_Promotion_Banners::get_banner_version(),
				true
			);

			// Localize script with AJAX data.
			wp_localize_script(
				'wt-wbte-cta-banner',
				'wt_invoice_cta_banner_ajax',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'wt_dismiss_invoice_cta_banner_nonce' ),
					'action'   => 'wt_dismiss_invoice_cta_banner',
				)
			);
		}

		/**
		 * Add the meta box to the product edit screen
		 */
		public function add_meta_box() {
			if ( ! defined( 'WT_PDF_INVOICE_PLUGIN_DISPLAY_BANNER' ) ) {
				add_meta_box(
					'wt_pdf_invoice_pro',
					__( 'WooCommerce PDF Invoices, Packing Slips and Credit Notes', 'wt-gift-cards-woocommerce' ),
					array( $this, 'render_banner' ),
					array( 'woocommerce_page_wc-orders', 'shop_order' ),
					'side',
					'low'
				);
				define( 'WT_PDF_INVOICE_PLUGIN_DISPLAY_BANNER', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
			}
		}

		/**
		 * Render the banner HTML.
		 */
		public function render_banner() {

			$plugin_url        = 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_cross_promotion&utm_medium=add_new_order_sidebar&utm_campaign=PDF_invoice';
			$wt_admin_img_path = plugin_dir_url( __FILE__ ) . 'assets/images';

			?>
			<style type="text/css">
				#wt_pdf_invoice_pro .postbox-header{  height:80px; background:url( <?php echo esc_attr( $wt_admin_img_path . '/pdf_invoice.svg' ); ?> ) no-repeat 18px 18px #fff; padding-left:65px; margin-bottom:18px; background-size: 45px 45px; }
			</style>
			<div class="wt-cta-banner">
				<div class="wt-cta-content">

					<ul class="wt-cta-features">
						<li><?php esc_html_e( 'Automatically generate PDF invoices, packing slips, and credit notes', 'wt-gift-cards-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Use ready-made, customizable templates to match your brand', 'wt-gift-cards-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Print or download invoices individually or in bulk', 'wt-gift-cards-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Set custom invoice numbering for better organization', 'wt-gift-cards-woocommerce' ); ?></li>
						<li class="hidden-feature"><?php esc_html_e( 'Customize documents fully with visual or code editors', 'wt-gift-cards-woocommerce' ); ?></li>
						<li class="hidden-feature"><?php esc_html_e( 'Include VAT, GST, ABN, and other tax details', 'wt-gift-cards-woocommerce' ); ?></li>
						<li class="hidden-feature"><?php esc_html_e( 'Add "Pay Now" link on invoices', 'wt-gift-cards-woocommerce' ); ?></li>
						<li class="hidden-feature"><?php esc_html_e( 'Add custom fields to any order document with ease', 'wt-gift-cards-woocommerce' ); ?></li>
					</ul>

					<div class="wt-cta-footer">
						<div class="wt-cta-footer-links">
							<a href="#" class="wt-cta-toggle" data-show-text="<?php esc_attr_e( 'View all premium features', 'wt-gift-cards-woocommerce' ); ?>" data-hide-text="<?php esc_attr_e( 'Show less', 'wt-gift-cards-woocommerce' ); ?>"><?php esc_html_e( 'View all premium features', 'wt-gift-cards-woocommerce' ); ?></a>
							<a href="<?php echo esc_url( $plugin_url ); ?>" class="wt-cta-button" target="_blank"><img src="<?php echo esc_url( $wt_admin_img_path . '/promote_crown.svg' ); ?>" style="width: 15.01px; height: 10.08px; margin-right: 8px;" alt=""><?php esc_html_e( 'Get the plugin', 'wt-gift-cards-woocommerce' ); ?></a>
						</div>
						<a href="#" class="wt-cta-dismiss" style="display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none;"><?php esc_html_e( 'Dismiss', 'wt-gift-cards-woocommerce' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Handle the dismiss action via AJAX
		 */
		public function dismiss_banner() {
			check_ajax_referer( 'wt_dismiss_invoice_cta_banner_nonce', 'nonce' );

			// Check if user has permission.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Insufficient permissions' );
			}

			// Update the option to hide the banner.
			update_option( 'wt_hide_invoice_cta_banner', true );

			wp_send_json_success( 'Banner dismissed successfully' );
		}
	}

	new Wt_Invoice_Cta_Banner();
}
