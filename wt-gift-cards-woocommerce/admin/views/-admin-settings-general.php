<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 *  @since 1.0.0
 */
?>
<style type="text/css">
.wt_gc_settings_container{ width:100%; float:left; }
.wt-gc-product-page-tab-form-table tr th:first-child{ width:35%; }
.wt-gc-product-page-tab-form-table tr td:nth-child(2){ width:60%; }
.wt-gc-product-page-tab-form-table tr td:nth-child(3){ width:0%; }
.wt-gc-tab-content{ padding-bottom:0px; }
.wt_gc_coupon_format_field_block{ display:inline-flex; align-items:stretch; gap:8px; }
.wt_gc_coupon_format_field_block input[type="text"]{ width:150px; min-width:90px; margin:0; padding:0.35rem 0.75rem; border:1px solid #d7dade; border-radius:4px; background:#fff; outline:none; box-shadow:none; }
.wt_gc_coupon_format_field_block input[type="text"]:focus{ border-color:#4750cb; box-shadow:none; }
.wt_gc_coupon_format_code_wrap{ display:inline-flex; align-items:center; justify-content:center; padding:4px; border-radius:4px; background:#fff; }
.wt_gc_coupon_format_field_block code{ display:inline-flex; align-items:center; justify-content:center; padding:6px 14px; background:#eeeeee; color:#1d2327; border-radius:3px; font-family:Consolas, Monaco, "Courier New", monospace; font-size:13px; font-weight:600; white-space:nowrap; }
</style>

<div class="wt-gc-tab-content">
	<div class="wt_gc_settings_container">       
		<h3 class="wt-gc-form-settings-group-heading"><?php esc_html_e( 'General', 'wt-gift-cards-woocommerce' ); ?></h3>
		<?php
		require WBTE_GC_FREE_MAIN_PATH . 'admin/views/--general-tab.php';

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook for extenders.
		do_action( 'wt_gs_intl_general_settings_tab_content' );
		?>
	</div>
	<?php
	Wbte_Woocommerce_Gift_Cards_Free_Admin::add_settings_footer( __( 'Save', 'wt-gift-cards-woocommerce' ) );
	?>
</div>