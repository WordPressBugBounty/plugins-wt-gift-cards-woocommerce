<?php
/**
 * EMA Banner
 * 
 * @since 1.2.7
 *
 * @package  Wbte_Woocommerce_Gift_Cards_Free
 */

if (!defined('ABSPATH')) {
    exit;
}

if ( ! class_exists( 'Wbte_Ema_Banner' ) ) {
    class Wbte_Ema_Banner { 
        public $module_id               = '';
        public static $module_id_static = '';
        public $module_base             = 'gc_pro_banner';
        /**
         * The single instance of the class
         *
         * @var self
         */
        private static $instance = null;

        /**
         * The dismiss option name in WP Options table
         *
         * @var string
         */
        private $dismiss_option = 'wbte_ema_banner_analytics_page_dismiss';

        /**
         * Constructor
         * @since 1.2.7
         */
        public function __construct() {
            $this->module_id        = $this->module_base;
            self::$module_id_static = $this->module_id;

            if ( ! in_array( 'decorator-woocommerce-email-customizer/decorator.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
                add_action('admin_footer', array($this, 'ema_inject_analytics_script'));
                add_action('wp_ajax_wt_gc_dismiss_ema_banner', array($this, 'wt_gc_dismiss_ema_banner'));
            }
        }

        /**
         * Ensures only one instance is loaded or can be loaded.
         *
         * @since 1.2.7
         * @return self
         */
        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Enqueue banner styles
         * 
         * @since 1.2.7
         */
        public function enqueue_styles() {
            if (!$this->ema_should_display_banner()) {
                return;
            }

            wp_enqueue_style('wt-gc-ema-banner',plugin_dir_url(__FILE__) . 'assets/css/wt-gc-ema-banner.css',array(),WBTE_GC_FREE_VERSION);
            wp_enqueue_script('wt-gc-ema-banner',plugin_dir_url(__FILE__) . 'assets/js/wt-gc-ema-banner.js',array('jquery'),WBTE_GC_FREE_VERSION,true);

            wp_localize_script('wt-gc-ema-banner', 'wt_gc_ema_banner_params', array(
                'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
                'nonce' => wp_create_nonce('wt_gc_ema_banner_nonce'),
            ));
        }

        /**
         * Check if we should display the banner
         * 
         * @since 1.2.7
         * @return boolean
         */
        private function ema_should_display_banner() {
            $screen = get_current_screen();
            
            // Only inject on analytics page
 			$analytics_page_path = isset($_GET['path']) ? sanitize_text_field( wp_unslash( $_GET['path'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if (
				!$screen ||
				'woocommerce_page_wc-admin' !== $screen->id ||
				empty($analytics_page_path) ||
				false === strpos($analytics_page_path, '/analytics')
			) {
				return false;
			}
             
            return ! get_option( $this->dismiss_option ) && ! defined( 'WBTE_EMA_ANALYTICS_BANNER' );
        }

        /**
         * Ajax handler to dismiss the BFCM banner
         * 
         * @since 1.2.7
         */
        public function wt_gc_dismiss_ema_banner() {
            check_ajax_referer('wt_gc_ema_banner_nonce', 'nonce');
            update_option($this->dismiss_option, true);
            wp_send_json_success();
        }

        /**
         * Inject analytics script in admin footer
         * 
         * @since 1.2.7
         */
        public function ema_inject_analytics_script() {
            
            ob_start();

            if ( !$this->ema_should_display_banner() ) {
                return;
            }
            
            $sale_link = 'https://www.webtoffee.com/product/ecommerce-marketing-automation/?utm_source=free_plugin_analytics_overview_tab&utm_medium=gift_cards_free&utm_campaign=EMA' ;

            ?>
            
                <div class="wt_gc_gift_card_ema_banner hide">	
                    <div class="wt_gc_gift_card_ema_box">						
                        <div class="wt_gc_gift_card_ema_text">
                            <img src="<?php echo esc_url( WBTE_GC_FREE_URL . 'admin/modules/gc_pro_banner/assets/images/bulb.svg' ); ?>" style="">
                            <span class="wt_gc_gift_card_ema_title"><?php esc_html_e( 'Did you know?', 'wt-gift-cards-woocommerce' ); ?></span>
                            <?php esc_html_e('You can boost your store revenue and recover lost sales with automated email campaigns, cart recovery, and upsell popups using the WebToffee Marketing Automation App.','wt-gift-cards-woocommerce'); ?>
                        </div>
                        <div class="wt_gc_gift_card_ema_actions">
                            <a href="<?php echo esc_url( $sale_link); ?>" class="btn-primary" target="_blank"><?php esc_html_e('Sign Up for Free', 'wt-gift-cards-woocommerce'); ?></a>
                            <button type="button" class="notice-dismiss wt_gc_ema_dismiss">
                                <span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'wt-gift-cards-woocommerce'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
                
            <?php
            define('WBTE_EMA_ANALYTICS_BANNER',true);
            $output = ob_get_clean();
            
            if (empty(trim($output))) {
                return;
            }
            ?>
            <script type="text/javascript">
                // Wait for DOM to be fully loaded and give extra time for dynamic content
                setTimeout(function() {
                    var ema_output = document.createElement('div');
                    ema_output.innerHTML = <?php echo wp_json_encode(wp_kses_post($output)); ?>;
                    
                    // Add margin to the banner
                    var banner = ema_output.querySelector('.wt_gc_gift_card_ema_banner');
                    if (banner) {
                        banner.style.margin = '15px 40px 5px 40px';
                    }
                    
                    // Find the header element
                    var header = document.querySelector('.woocommerce-layout__header');
                    if (header && header.parentNode) {
                        // Insert after the header
                        header.parentNode.insertBefore(ema_output, header.nextSibling);
                    } 
                }, 1000); // 1 second delay
            </script>
            <?php
        }
    }


    /**
     * Initialize the BFCM banner
     * 
     * @since 1.2.7
     */
    add_action('admin_init', array('Wbte_Ema_Banner', 'get_instance'));
    
}

