<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
$wbte_settings_button_title = isset( $wbte_settings_button_title ) && '' !== $wbte_settings_button_title ? $wbte_settings_button_title : __( 'Save settings', 'wt-gift-cards-woocommerce' );

/**
*   left and right HTML for settings footer
*/
$wbte_settings_footer_left  = isset( $wbte_settings_footer_left ) ? $wbte_settings_footer_left : '';
$wbte_settings_footer_right = isset( $wbte_settings_footer_right ) ? $wbte_settings_footer_right : '';
?>
<div style="clear: both;"></div>
<div class="wt-gc-plugin-toolbar bottom">
	<div class="left">
		<?php echo wp_kses_post( $wbte_settings_footer_left ); ?>
	</div>
	<div class="right">
		<input type="submit" name="wt_gc_update_admin_settings_form" value="<?php echo esc_attr( $wbte_settings_button_title ); ?>" class="button button-primary" style="float:right;"/>
		<?php echo wp_kses_post( $wbte_settings_footer_right ); ?>
		<span class="spinner" style="margin-top:11px;"></span>
	</div>
</div>