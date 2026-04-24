<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wbte_Woocommerce_Gift_Cards_Free
 * @subpackage Wbte_Woocommerce_Gift_Cards_Free/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

?>
<div class="wrap">
	<h2 class="wp-heading-inline"> <?php esc_html_e( 'WooCommerce Gift Cards', 'wt-gift-cards-woocommerce' ); ?> </h2>
	
	<?php
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy wt_gc hook for extenders.
	do_action( 'wt_gc_plugin_before_settings_tab' );
	?>
	<div class="nav-tab-wrapper wp-clearfix wt-gc-tab-head">
		<?php
		$wbte_tab_head_arr = array(
			'wt-gc-general' => __( 'General settings', 'wt-gift-cards-woocommerce' ),
		);
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['debug'] ) ) {
			$wbte_tab_head_arr['wt-gc-debug'] = __( 'Debug', 'wt-gift-cards-woocommerce' );
		}

		self::generate_settings_tabhead( $wbte_tab_head_arr );

		?>
	</div>

	<div class="wt-gc-tab-container">       
		<?php
		$wbte_setting_views_a = array(
			'wt-gc-general' => '-admin-settings-general.php',
		);

		$wbte_setting_views_b = array(
			'wt-gc-help' => 'admin-settings-help.php',
		);
		?>

		<form method="post" class="wt_gc_settings_form">
			<input type="hidden" value="main" class="wt_gc_settings_base" />
			<?php

			// Set nonce:
			if ( function_exists( 'wp_nonce_field' ) ) {
				wp_nonce_field( WBTE_GC_FREE_PLUGIN_NAME );
			}

			?>

			<?php
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['debug'] ) ) {
				$wbte_setting_views_b['wt-gc-debug'] = 'admin-settings-debug.php';
			}

			$wbte_tab_key = self::get_tab_key();

			self::insert_tab_content_file( $wbte_setting_views_a, $wbte_tab_key );

			// settings tabs
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy wt_gc hook for extenders.
			do_action( 'wt_gc_plugin_settings_form', array( 'tab_key' => $wbte_tab_key ) );
			?>
		</form>

		<?php
		self::insert_tab_content_file( $wbte_setting_views_b, $wbte_tab_key );
		// modules to hook outside settings form
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy wt_gc hook for extenders.
		do_action( 'wt_gc_plugin_out_settings_form', array( 'tab_key' => $wbte_tab_key ) );
		?>
	</div>

	<div class="wt-gc-tab-right-container">
		<div class="wt_gc_upgrade_pro">
			<?php 
			if(  method_exists( 'Wbte_Woocommerce_Gift_Cards_Free_Admin', 'is_bfcm_season' ) && Wbte_Woocommerce_Gift_Cards_Free_Admin::is_bfcm_season() ) {
			?>
				<div class="wt_gc_bfcm_offer">
					<span style="margin: 0px 13px;"><img src="<?php echo esc_url( WBTE_GC_FREE_URL . 'admin/images/bfcm-doc-settings-coupon.svg' ); ?>" style="width: 280px;"></span>
				</div>
			<?php
			}
			?>
			<div class="wt_gc_upgrade_pro_main">
				<!-- <span style="font-size:41px; padding-top:20px;">🎉</span> -->
				<span style="font-size:41px; padding-top:20px;"><img src="<?php echo esc_url( WBTE_GC_FREE_URL . 'admin/images/coupon-image.svg' ); ?>" style="width: 46px;"></span>
				<div class="wt_gc_upgrade_pro_main_hd"><?php esc_html_e( 'Boost Your Sales and Customer Loyalty With Premium Gift Card Features!', 'wt-gift-cards-woocommerce' ); ?></div>
			</div>
			<div class="wt_gc_upgrade_pro_content">
				<ul class="pro_feature_list">
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Create an unlimited number of gift cards', 'wt-gift-cards-woocommerce' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Create physical gift cards', 'wt-gift-cards-woocommerce' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( "'Gift this product' option", 'wt-gift-cards-woocommerce' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Let users schedule gift card delivery', 'wt-gift-cards-woocommerce' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Print gift cards', 'wt-gift-cards-woocommerce' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Allow users to upload custom images for gift cards', 'wt-gift-cards-woocommerce' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Recommend products on gift certificates', 'wt-gift-cards-woocommerce' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Accept gift cards as payment method', 'wt-gift-cards-woocommerce' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Apply store credit on shipping, tax, and other fees', 'wt-gift-cards-woocommerce' ); ?></li>
				</ul>
			</div> 
			<div class="wt_gc_upgrade_pro_lower_green">
				<div class="wt_gc_extras">
					<h3> <?php esc_html_e( 'Try with confidence', 'wt-gift-cards-woocommerce' ); ?> </h3>
					<div class="wt_gc_extras_content" style="border-bottom: none; border-radius: 5px 5px 0px 0px;">
						<img src="<?php echo esc_url(WBTE_GC_FREE_URL . 'admin/images/30day-money-back.svg')?>">
						<h3  style="color: #606060;"><?php esc_html_e('100% No Risk Money Back Guarantee', 'wt-gift-cards-woocommerce'); ?></h3>
					</div>
					<div class="wt_gc_extras_content" style="border-radius: 0px 0px 5px 5px;">
						<img src="<?php echo esc_url(WBTE_GC_FREE_URL . 'admin/images/satisfaction-rating.svg')?>">
						<h3  style="color: #606060;"><?php esc_html_e('Excellent Support with 99% Satisfaction Rating', 'wt-gift-cards-woocommerce'); ?></h3>
					</div>
				</div>
				<div class="wt_gc_upgrade_pro_button">
					<a class="button button-secondary" href="<?php echo esc_url( 'https://www.webtoffee.com/product/woocommerce-gift-cards/?utm_source=free_plugin_sidebar&utm_medium=Gift_card_basic&utm_campaign=WooCommerce_Gift_Cards&utm_content=' . WBTE_GC_FREE_VERSION ); ?>" target="_blank"><?php esc_html_e( 'Check Out Premium', 'wt-gift-cards-woocommerce' ); ?> <span class="dashicons dashicons-arrow-right-alt" style="line-height:58px;font-size:14px;"></span> </a>
				</div>
			</div>
		</div> 

		<?php 
		/**
		 * Check if newsletter banner should be hidden
		 * @since 1.2.9
		 */

		$wbte_newsletter_banner_hidden = get_option( 'wt_newsletter_banner_hidden', false );

		if ( ! $wbte_newsletter_banner_hidden ) :
			?>
			<div class="wt_gc_newsletter_subscription_widget" style="position:relative;width: 100%;">
				<div class="wt_gc_newsletter_subscription_box">
					<div class="wt_newsletter_header">
						<div class="wt_newsletter_icon">
							<svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
								<mask id="mask0_11201_2391" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="38" height="38">
									<path d="M38 0H0V38H38V0Z" fill="white"/>
								</mask>
								<g mask="url(#mask0_11201_2391)">
									<path d="M36.4832 16.3331L21.6513 1.50126C21.1882 1.03814 20.5113 1.10939 20.0482 1.57251L1.54695 19.8125C1.08382 20.2756 1.06007 21.2137 1.5232 21.6769L16.3076 36.4612C16.7707 36.9244 17.5307 36.9362 17.9938 36.4731L36.4713 17.9956C36.9345 17.5325 36.9463 16.7962 36.4832 16.3331Z" fill="#FFC44D"/>
									<path d="M28.501 32.0684H36.8135M34.4385 27.3184H36.8135M7.12598 21.381H20.1885C20.819 21.381 21.376 20.862 21.376 20.1935V8.31843M16.626 26.1309V29.6934M29.6885 16.6309H26.126M36.4806 16.3329C36.9449 16.7972 36.9307 17.5346 36.4664 17.9978L17.9936 36.4717C17.5293 36.936 16.7729 36.9313 16.3097 36.4669L1.52181 21.679C1.0575 21.2147 1.086 20.2766 1.55031 19.8134L20.0456 1.57699C20.5099 1.11386 21.1821 1.0343 21.6464 1.49861L36.4806 16.3329Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
							</svg>
						</div>
						<div class="wt_newsletter_title">
							<h3><?php esc_html_e('Subscribe to our newsletter for exclusive offers & updates', 'wt-gift-cards-woocommerce'); ?></h3>
						</div>
					</div>
						
					<div id="mc_embed_shell">
						<div id="mc_embed_signup">
							<form action="https://list-manage.us5.list-manage.com/subscribe/post?u=10e843cdec17dd1d2e769ead6&amp;id=d9d25110b9&amp;f_id=0020b8edf0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
								<div class="mc-field-group wbte-gc-newsletter-email">
									<input type="email" name="EMAIL" class="required email" id="mce-EMAIL" required="" value="" placeholder="<?php esc_attr_e('Enter your email address', 'wt-gift-cards-woocommerce'); ?>">
								</div>
								<div class="consent-checkbox wbte-newsletter-consent-checkbox">
									<input type="checkbox" id="consent-checkbox" name="CONSENT" class="required checkbox" required>
									<label for="consent-checkbox">
									<?php 
										printf(
											// translators: %1$s: Privacy Policy link, %2$s: a tag closing.
											esc_html__( 'I consent to receive newsletters and exclusive offers from WebToffee and agree to the %1$s Privacy Policy %2$s.', 'wt-gift-cards-woocommerce' ),
											'<a href="https://www.webtoffee.com/privacy-policy/" target="_blank">',
											'</a>'
										);
										?>
									</label>
								</div>
								<div hidden="">
									<input type="hidden" name="tags" value="4546286">
								</div>
								<div id="mce-responses" class="clear">
									<div class="response" id="mce-error-response" style="display: none;"></div>
									<div class="response" id="mce-success-response" style="display: none;"></div>
								</div>
								
								<div aria-hidden="true" style="position: absolute; left: -5000px;">
									<input type="text" name="b_10e843cdec17dd1d2e769ead6_d9d25110b9" tabindex="-1" value="">
								</div>
								<button type="submit" id="mc-embedded-subscribe" class="button wbte-newsletter-subscribe-button">
									<span class="button-text"><?php esc_html_e('Subscribe', 'wt-gift-cards-woocommerce'); ?></span>
									<span class="button-spinner"></span>
								</button>
							</form>
						</div>
						<?php // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript, PluginCheck.CodeAnalysis.Offloading.OffloadedContent -- MailChimp embedded newsletter (remote mc-validate.js). ?>
						<script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script><script type="text/javascript">(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='ADDRESS';ftypes[3]='address';fnames[4]='PHONE';ftypes[4]='phone';fnames[5]='BIRTHDAY';ftypes[5]='birthday';fnames[6]='MMERGE6';ftypes[6]='text';fnames[7]='IS_BOARD';ftypes[7]='text';fnames[8]='IS_CONF';ftypes[8]='text';fnames[9]='IS_CONT';ftypes[9]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
						<?php // phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript, PluginCheck.CodeAnalysis.Offloading.OffloadedContent ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy wt_gc hook for extenders.
	do_action( 'wt_gc_plugin_after_settings_tab' );
	?>
</div>