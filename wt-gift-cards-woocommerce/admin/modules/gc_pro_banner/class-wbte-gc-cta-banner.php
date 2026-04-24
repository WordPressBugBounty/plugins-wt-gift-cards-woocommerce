<?php
/**
 * CTA Banner for Gift Cards
 *
 * @since 1.2.9
 * @package Wt_Gift_Cards
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wbte_Gc_Cta_Banner' ) ) {

	/**
	 * Class Wbte_Gc_Cta_Banner
	 *
	 * @since 1.2.9
	 */
	class Wbte_Gc_Cta_Banner {

		const WBTE_GC_MILESTONE_ORDER_IDS = 'wbte_gc_milestone_gc_order_ids'; // All order ids having at least one gift card product.
		const MILESTONE_BANNER_MIN_TRACKED = 11; // Minimum tracked GC orders to show banner.
		private static $wt_gc_tracking_removal_statuses = array( 'failed', 'cancelled', 'refunded', 'trash' ); // Failed order statuses.

		/**
		 * Cache for GC order IDs.
		 *
		 * @var int[]|null
		 */
		private static $gc_order_ids_cache = null;

		/**
		 * Constructor
		 *
		 * @since 1.2.9
		 */
		public function __construct() {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			$installed_plugins = get_plugins();

			if ( ! array_key_exists( 'wt-woocommerce-gift-cards/wt-woocommerce-gift-cards.php', $installed_plugins ) ) {
				add_action( 'admin_head-edit.php', array( $this, 'coupons_page_banners' ), 10 );
				add_action( 'admin_head', array( $this, 'order_page_banners' ), 10 );

				if ( self::wt_gc_get_gift_card_orders_count() < self::MILESTONE_BANNER_MIN_TRACKED ) {
					add_action( 'woocommerce_order_status_changed', array( $this, 'on_order_status_changed_milestone' ), 10, 4 );
					add_action( 'woocommerce_before_trash_order', array( $this, 'gc_milestone_on_order_removal' ), 10, 2 );
					add_action( 'woocommerce_before_delete_order', array( $this, 'gc_milestone_on_order_removal' ), 10, 2 );
				}
			}
			add_action( 'admin_notices', array( $this, 'plugin_update_pending_notice' ) );
		}

		/**
		 * Order IDs stored for the gift-card milestone (unique, as integers).
		 *
		 * @since 1.2.9
		 * @return int[]
		 */
		public static function wt_gc_get_gift_card_orders() {
			if ( null === self::$gc_order_ids_cache ) {
				$raw = (array) get_option( self::WBTE_GC_MILESTONE_ORDER_IDS, array() );
				self::$gc_order_ids_cache = array_values( array_unique( array_map( 'absint', $raw ) ) );
			}
			return self::$gc_order_ids_cache;
		}

		/**
		 * Update the full ID list on add/remove.
		 *
		 * @since 1.2.9
		 * @param int[] $ids Order IDs.
		 */
		public static function update_gc_order_ids( array $ids ) {
			$ids = array_values( array_unique( array_map( 'intval', $ids ) ) );
			update_option( self::WBTE_GC_MILESTONE_ORDER_IDS, $ids );
			self::$gc_order_ids_cache = null;
		}

		/**
		 * True if any line item is a WebToffee Gift Card product (`_wt_gc_gift_card_product`).
		 *
		 * @since 1.2.9
		 * @param WC_Order $order Order.
		 */
		public static function is_gift_card_order( $order ) {
			if ( ! $order instanceof WC_Order
				|| ! class_exists( 'WC_Order_Item_Product', false )
				|| ! class_exists( 'WC_Product', false ) ) {
				return false;
			}
			foreach ( $order->get_items( 'line_item' ) as $item ) {
				if ( ! is_a( $item, 'WC_Order_Item_Product' ) ) {
					continue;
				}
				$product = $item->get_product();
				if ( ! $product instanceof WC_Product ) {
					continue;
				}
				$product_id = absint( $product->get_id() );

				if ( !empty($product_id) && Wbte_Gc_Gift_Card_Free_Common::is_gift_card_product( $product_id ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * @return int
		 */
		private static function wt_gc_get_gift_card_orders_count() {
			return count( self::wt_gc_get_gift_card_orders() );
		}

		/**
		 * @param int $order_id Order ID.
		 */
		private static function add_gc_order_id( $order_id ) {
			$oid = (int) $order_id;
			$ids = self::wt_gc_get_gift_card_orders();
			if ( ! in_array( $oid, $ids, true ) ) {
				self::update_gc_order_ids( array_merge( $ids, array( $oid ) ) );
			}
		}

		/**
		 * @param int $order_id Order ID.
		 */
		private static function remove_gc_order_id( $order_id ) {
			$order_id = (int) $order_id;
			$ids = self::wt_gc_get_gift_card_orders();
			if ( ! in_array( $order_id, $ids, true ) ) {
				return;
			}
			self::update_gc_order_ids( array_values( array_diff( $ids, array( $order_id ) ) ) );
		}

		/**
		 * @since 1.2.9
		 * @return int[]
		 */
		public static function get_milestone_tracked_order_ids() {
			return self::wt_gc_get_gift_card_orders();
		}

		/**
		 * @since 1.2.9
		 * @return int
		 */
		public static function get_milestone_tracked_order_count() {
			return self::wt_gc_get_gift_card_orders_count();
		}

		/**
		 * State-driven GC order ID list (single option `WBTE_GC_MILESTONE_ORDER_IDS`):
		 * count < 11 — add on transition if GC order; remove on failed/cancelled/refunded/trash if listed.
		 * count ≥ 11 — no order status / trash / delete handling (milestone reached).
		 *
		 * @since 1.2.9
		 * @param int      $order_id   Order ID.
		 * @param string   $old_status Old status (no wc- prefix).
		 * @param string   $new_status New status.
		 * @param WC_Order $order      Order object.
		 */
		public function on_order_status_changed_milestone( $order_id, $old_status, $new_status, $order ) {
			if ( ! $order instanceof WC_Order ) {
				return;
			}

			if ( self::wt_gc_get_gift_card_orders_count() >= self::MILESTONE_BANNER_MIN_TRACKED ) {
				return;
			}

			if ( in_array( $new_status, self::$wt_gc_tracking_removal_statuses, true ) ) {
				self::remove_gc_order_id( $order->get_id() );
				return;
			}

			if ( self::is_gift_card_order( $order ) ) {
				self::add_gc_order_id( $order->get_id() );
			}
		}

		/**
		 * WooCommerce `before_trash_order` / `before_delete_order`: remove order ID from GC milestone list when present.
		 *
		 * @since 1.2.9
		 * @param int           $order_id Order ID.
		 * @param WC_Order|bool $order    Order object when available.
		 */
		public function gc_milestone_on_order_removal( $order_id, $order ) {
			if ( self::wt_gc_get_gift_card_orders_count() >= self::MILESTONE_BANNER_MIN_TRACKED ) {
				return;
			}
			$id = ( $order instanceof WC_Order ) ? $order->get_id() : (int) $order_id;
			if ( $id > 0 ) {
				self::remove_gc_order_id( $id );
			}
		}

		

		/**
		 * Show banner on edit pages
		 *
		 * @since 1.2.9
		 */
		public function coupons_page_banners() {
			global $current_screen;

			// Only show banner if SC Pro is not installed and current page is coupons page.
			if ( ! is_object( $current_screen ) || 'shop_coupon' !== $current_screen->post_type ) {
				return;
			}

			if ( self::sc_order_milestone_eligible() ) {
				return;
			}

			$banner_html = self::get_milestone_banner_html();

			if ( false !== $banner_html && ! defined( 'WBTE_BFCM_SC_COUPONS_PAGE' ) ) {
				define( 'WBTE_BFCM_SC_COUPONS_PAGE', true );
			}

			if ( false === $banner_html ) {
				$banner_html = self::sc_pro_coupons_page_banner();
			}

			if ( $banner_html ) {
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var html = <?php echo wp_json_encode( wp_kses_post( $banner_html ) ); ?>;
						var $anchor = $('.page-title-action.wt_sc_plugin_settings_btn').first();
						if (!$anchor.length) {
							$anchor = $('.page-title-action').first();
						}
						$anchor.after(html);
					});
				</script>
				<?php
			}

		}

		/**
		 * Render banners on orders list page.
		 *
		 * @since 1.2.9
		 */
		public static function order_page_banners() {
			$screen = get_current_screen();

			if ( ! $screen || ! isset( $screen->id ) ) {
				return;
			}
			// Do not show on individual order edit pages.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only query arg for list vs. edit screen; no privileged action.
			$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			if ( 'shop_order' === $screen->id || ( 'woocommerce_page_wc-orders' === $screen->id && 'edit' === $action ) ) {
				return;
			}

			if ( ! in_array( $screen->id, array( 'edit-shop_order', 'woocommerce_page_wc-orders' ), true ) ) {
				return;
			}

			if ( self::sc_order_milestone_eligible() ) {
				return;
			}

			$banner_html = self::get_milestone_banner_html();

			if ( false !== $banner_html ) {
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						jQuery( '.page-title-action' ).after( <?php echo wp_json_encode( wp_kses_post( $banner_html ) ); ?> );
					});
				</script>
				<?php
			}
		}

		/**
		 * Whether SC-free's order_milestone_banner would render (shares the banner slot with GC).
		 *
		 * @since 1.2.9
		 * @return bool
		 */
		private static function sc_order_milestone_eligible() {
			if ( ! class_exists( 'Wbte_Cta_Banner' ) ) {
				return false;
			}
			if ( defined( 'WBTE_MILESTONE_BANNER' ) ) {
				return false;
			}
			$hidden_banners = get_option( 'wbte_sc_hidden_promotion_banners', array() );
			$hidden_banners = is_array( $hidden_banners ) ? $hidden_banners : array();
			return ! in_array( 'sc_order_page_milestone', $hidden_banners, true );
		}

		/**
		 * Milestone banner: show only when number of completed Gift Card purchase orders is > 10.
		 *
		 * @since 1.2.9
		 * @return string|bool Banner HTML or false.
		 */
		private static function get_milestone_banner_html() {
			$hidden_banners = get_option( 'wbte_sc_hidden_promotion_banners', array() );
			if ( defined( 'WBTE_MILESTONE_BANNER' ) || in_array( 'gc_milestone_banner', $hidden_banners, true ) ) {
				return false;
			}

			define( 'WBTE_MILESTONE_BANNER', true );

			$gc_count = self::get_milestone_tracked_order_count();
			if ( $gc_count < self::MILESTONE_BANNER_MIN_TRACKED ) {
				return false;
			}
		
			$campaign_url = 'https://www.webtoffee.com/product/woocommerce-gift-cards/?utm_source=free_plugin&utm_medium=milestone_cta&utm_campaign=WooCommerce_Gift_Cards';
			$args = array(
				'banner_id'          => 'gc_milestone_banner',
				'title'              => sprintf(
					// translators: 1: formatted amount e.g. 10+.
					__( 'Congratulations! 🎉 You\'ve already sold %1$s gift cards using the WebToffee Gift Cards plugin.', 'wt-gift-cards-woocommerce' ),
					'<strong>10+</strong>'
				),
				'content'            => __( 'Upgrade to Gift Cards Premium to create unlimited gift card products, offer both digital and physical gift cards, and give customers more flexible gifting options.', 'wt-gift-cards-woocommerce' ),
				'primary_btn_url'    => esc_url( $campaign_url ),
				'primary_btn_text'   => __( 'Upgrade to Premium', 'wt-gift-cards-woocommerce' ),
				'secondary_btn_url'  => '',
				'secondary_btn_text' => __( 'Maybe later', 'wt-gift-cards-woocommerce' ),
			);

			return self::render_cta_banner( $args );
		}

		/**
		 * Render Smart Coupons CTA banner on coupons page.
		 *
		 * @since 1.2.9
		 * @return bool|string Banner HTML or false if banner is not needed.
		 */
		private static function sc_pro_coupons_page_banner() {
			$hidden_banners = get_option( 'wbte_sc_hidden_promotion_banners', array() );
			if ( defined( 'WBTE_BFCM_SC_COUPONS_PAGE' ) || in_array( 'sc_cpns_page', $hidden_banners, true ) ) {
				return false;
			}

			define( 'WBTE_BFCM_SC_COUPONS_PAGE', true );

			$campaign_url = 'https://www.webtoffee.com/product/smart-coupons-for-woocommerce/?utm_source=free_plugin_add_coupon_menu&utm_medium=smart_coupon_basic&utm_campaign=smart_coupons';

			$sc_pro_cta_args = array(
				'banner_id'          => 'sc_cpns_page',
				'title'              => sprintf(
					// translators: 1: image URL, 2: title.
					'<img src="%1$s" style="width: 16px;" />&nbsp;<span>%2$s</span>',
					esc_url( WBTE_GC_FREE_URL . 'admin/images/idea_bulb_purple.svg' ),
					esc_html__( 'Did you know?', 'wt-gift-cards-woocommerce' )
				),
				'content'            => sprintf(
					// translators: 1: a tag opening, 2: a tag closing.
					__( 'With the %1$s Smart Coupons %2$s plugin, you can create advanced coupons and Buy One Get One Offers for your WooCommerce store.', 'wt-gift-cards-woocommerce' ),
					'<a href="' . esc_url( $campaign_url ) . '" target="_blank"><b>',
					'</b></a>'
				),
				'primary_btn_url'    => esc_url( $campaign_url ),
				'primary_btn_text'   => __( 'Get Plugin Now', 'wt-gift-cards-woocommerce' ),
				'secondary_btn_text' => __( 'Maybe later', 'wt-gift-cards-woocommerce' ),
			);
			return self::render_cta_banner( $sc_pro_cta_args );
		}

		/**
		 * Render CTA banner from template
		 *
		 * @since 1.2.9
		 * @param array $args Banner arguments.
		 * @return string Banner HTML.
		 */
		private static function render_cta_banner( $args ) {
			$args = (array) $args;
			ob_start();
			include __DIR__ . '/views/cta-banner.php';
			return ob_get_clean();
		}

		/**
		 * Show admin notice when plugin update is available and has been available for 5+ days.
		 *
		 * @since 1.2.9
		 */
		public function plugin_update_pending_notice() {

			$update_plugins = get_site_transient( 'update_plugins' );

			if ( empty( $update_plugins->response ) || ! isset( $update_plugins->response[ WBTE_GC_FREE_BASE_NAME ] ) ) {
				delete_option( 'wbte_gc_plugin_update_available_since' );
				return;
			}

			$plugin_update = $update_plugins->response[ WBTE_GC_FREE_BASE_NAME ];
			$new_version   = isset( $plugin_update->new_version ) ? $plugin_update->new_version : '';
			if ( '' === $new_version ) {
				return;
			}
			$banner_id = 'gc_update_pending_' . sanitize_key( str_replace( '.', '_', $new_version ) );

			$hidden_banners = get_option( 'wbte_sc_hidden_promotion_banners', array() );
			if ( in_array( $banner_id, $hidden_banners, true ) ) {
				return;
			}

			$since = (int) get_option( 'wbte_gc_plugin_update_available_since', 0 );
			if ( 0 === $since ) {
				update_option( 'wbte_gc_plugin_update_available_since', time() );
				return;
			}

			$five_days_ago_ts = time() - ( 5 * DAY_IN_SECONDS );
			if ( $since >= $five_days_ago_ts ) {
				return;
			}

			$update_url = wp_nonce_url(
				self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . rawurlencode( WBTE_GC_FREE_BASE_NAME ) ),
				'upgrade-plugin_' . WBTE_GC_FREE_BASE_NAME
			);
			?>
			<div class="notice notice-info is-dismissible wbte-gc-update-pending-notice">
				<p style="font-size: 14px; font-weight: 700;"><?php esc_html_e( 'Update pending ⚠️', 'wt-gift-cards-woocommerce' ); ?></p>
				<p><?php esc_html_e( 'It looks like you haven\'t updated the WooCommerce Gift Cards plugin in a while. We release new features and security improvements with every update. Keep it up-to-date to ensure better performance, compatibility, and protection.', 'wt-gift-cards-woocommerce' ); ?></p>
				<p><a href="<?php echo esc_url( $update_url ); ?>" class="button button-primary"><?php esc_html_e( 'Update Now', 'wt-gift-cards-woocommerce' ); ?></a></p>
			</div>
			<script>
			(function($){
				$( document ).on( 'click', '.wbte-gc-update-pending-notice .notice-dismiss', function() {
					const ajaxurl  = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";
					const data     = {
						action   : 'wbte_gc_hide_promotion_banner',
						banner_id: "<?php echo esc_attr( $banner_id ); ?>",
						_wpnonce : "<?php echo esc_attr( wp_create_nonce( 'wt_gc_admin_nonce' ) ); ?>"
					}
					if ( ajaxurl ) {
						$.post( ajaxurl, data );
					}
				});
			})( jQuery );
			</script>
			<?php
		}

	}

	new Wbte_Gc_Cta_Banner();
}
