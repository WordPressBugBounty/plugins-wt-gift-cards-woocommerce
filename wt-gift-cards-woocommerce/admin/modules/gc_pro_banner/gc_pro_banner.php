<?php
/**
 * Free vs Pro Comparison
 *
 * @link
 * @since 1.2.5
 *
 * @package  Wbte_Woocommerce_Gift_Cards_Free
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wbte_Gc_Upsell_Banner {

	public $module_id               = '';
	public static $module_id_static = '';
	public $module_base             = 'gc_pro_banner';
	private static $instance        = null;
	protected $dismiss_option_key   = 'wt_gift_cards_upsell_dismiss';

	public function __construct() {
		$this->module_id        = $this->module_base;
		self::$module_id_static = $this->module_id;

		add_action( 'wp_ajax_wt_gc_dismiss_upsell_banner', array( $this, 'ajax_dismiss_upsell_banner' ) );
		add_action( 'admin_footer', array( $this, 'maybe_hide_upsell_metabox_script' ) );
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

	}
	
	/**
	 *  Get Instance
	 *
	 *  @since 1.2.5
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Wbte_Gc_Upsell_Banner();
		}
		return self::$instance;
	}

	/**
	 * Enqueue required scripts and styles.
	 */
	public function enqueue_scripts($hook) { 
		if ( $this->is_banner_dismissed() ) {
			return;
		}

		wp_enqueue_style('wt-gc-upsell-banner',plugin_dir_url(__FILE__) . 'assets/css/wt-gc-upsell-banner.css',array(),WBTE_GC_FREE_VERSION);

		wp_enqueue_script('wt-gc-upsell-banner',plugin_dir_url(__FILE__) . 'assets/js/wt-gc-upsell-banner.js',array('jquery'),WBTE_GC_FREE_VERSION,true);

		// Localize script with AJAX data
		wp_localize_script('wt-gc-upsell-banner', 'wt_gc_upsell_banner_params', array(
			'ajax_url' => esc_url(admin_url('admin-ajax.php')),
			'nonce' => wp_create_nonce('wt_gc_dismiss_upsell_banner'),
			'action' => 'wt_gc_dismiss_upsell_banner'
		));
	}

	/**
	 * Check if the upsell banner is currently dismissed (permanent or within 7 days window)
	 *
	 * @since 1.2.5
	 * 
	 * @return bool
	 */
	private function is_banner_dismissed() {
		$option_value = get_option( $this->dismiss_option_key, false );
		if ( 'dismiss' === $option_value ) {
			return true;
		}
		$dismiss_until = (int) $option_value;
		return ( $dismiss_until && $dismiss_until > time() );
	}


	/**
	 *  Display the upsell banner
	 *
	 *  @since 1.2.5
	 *  @since 1.2.7 Updated new banner content for Send admin email settings page.
	 * 
	 *  @param string $page Page where the banner is shown. 'default' : template settings and GC product table.
	 */
	public function pro_banner_content( $page = 'default') {
		if ( $this->is_banner_dismissed() ) {
			return;
		}
		
		switch ( $page ) {
			case 'email_page':
				$banner_description = __( 'You can create multiple gift cards, choose from 20+ beautiful templates, and set advanced usage restrictions with the premium version of WebToffee WooCommerce Gift Cards.', 'wt-gift-cards-woocommerce' );
				$cta_link           = 'https://www.webtoffee.com/product/woocommerce-gift-cards/?utm_source=free_plugin_send_gift_card&utm_medium=Gift_card_basic&utm_campaign=WooCommerce_Gift_Cards';
				$upsell_action_class= 'wt_gc_gift_card_upsell_actions_email_page';
				$cta_text           = __( 'Get Plugin Now →', 'wt-gift-cards-woocommerce' );
				break;

			default:
				$banner_description = __( 'With the premium version, you’ll get access to 20+ templates for creating gift cards. You can also upload custom images as gift card images.', 'wt-gift-cards-woocommerce' );
				$cta_link           = 'https://www.webtoffee.com/product/woocommerce-gift-cards/?utm_source=free_plugin_templates&utm_medium=Gift_card_basic&utm_campaign=WooCommerce_Gift_Cards';
				$upsell_action_class= 'wt_gc_gift_card_upsell_actions';
				$cta_text           = __( 'Check out plugin →', 'wt-gift-cards-woocommerce' );
				break;
		}
		?>

			<section class="wt_gc_gift_card_upsell_banner_content">
				<div class="wt_gc_gift_card_pro_feature_banner">	
					<div class="wt_gc_gift_card_upsell_box">						
						<div class="wt_gc_gift_card_upsell_text">
							<img src="<?php echo esc_url( WBTE_GC_FREE_URL . 'admin/modules/gc_pro_banner/assets/images/bulb.svg' ); ?>" style="">
							<span class="wt_gc_gift_card_upsell_title"><?php esc_html_e( 'Did you know?', 'wt-gift-cards-woocommerce' ); ?></span>
							<?php echo esc_html($banner_description); ?>
						</div>
						<div class="<?php echo esc_html($upsell_action_class); ?>">
							<a href="<?php echo esc_url( $cta_link ); ?>" class="btn-primary" target="_blank"><?php echo esc_html( $cta_text ); ?></a>
							<a href="<?php echo esc_url( '#' ); ?>" class="btn-secondary wt_gc_gift_card_upsell_dismiss" ><?php esc_html_e( 'Dismiss', 'wt-gift-cards-woocommerce' ); ?></a>
							<button type="button" class="popup-close wt_gc_gift_card_upsell_closed">
								<svg class="wt_pklist_banner_dismiss" width="11" height="11" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9.5 1L1 9.5" stroke="#505050" stroke-width="1.5"></path>
									<path d="M1 1L9.5 9.5" stroke="#505050" stroke-width="1.5"></path>
								</svg>
						</button>
						</div>
					</div>
				</div>
			</section>

		<?php
	}

	/**
	 *  Hide the upsell metabox
	 *
	 *  @since 1.2.5
	 */
	public function maybe_hide_upsell_metabox_script() {
		if ( $this->is_banner_dismissed() ) {
			?>
			<script type="text/javascript">
				jQuery(function($){
					$('#wt-gc-upsell-banner-metabox').hide();
				});
			</script>
			<?php
		}
	}

	/**
	 * Handle AJAX request to dismiss/close the upsell banner
	 *
	 * If `dismiss` is 1, permanently hide the banner.
	 * If `dismiss` is 0, hide the banner for 7 days.
	 *
	 * @since 1.2.5
	 */
	public function ajax_dismiss_upsell_banner() {
		check_ajax_referer( 'wt_gc_dismiss_upsell_banner', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'wt-gift-cards-woocommerce' ) ), 403 );
		}

		$dismiss = isset( $_POST['dismiss'] ) ? absint( $_POST['dismiss'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$current_value = get_option( $this->dismiss_option_key, false );

		if ( 1 === $dismiss ) {
			update_option( $this->dismiss_option_key, 'dismiss' ); // Permanently hide the banner.
		} else { 
			if ( 'dismiss' !== $current_value ) {
				$until = time() + WEEK_IN_SECONDS;
				update_option( $this->dismiss_option_key, $until ); // Hide for 7 days, but do not override a permanent dismissal if already set.
			}
		}

		wp_send_json_success();
	}
}
Wbte_Gc_Upsell_Banner::get_instance();
