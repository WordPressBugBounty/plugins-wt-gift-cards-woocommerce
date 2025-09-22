<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$no_icon  = '<span class="dashicons dashicons-dismiss" style="color:#ea1515;"></span>&nbsp;';
$yes_icon = '<span class="dashicons dashicons-yes-alt" style="color:#18c01d;"></span>&nbsp;';

global $wp_version;
if ( version_compare( $wp_version, '5.2.0' ) < 0 ) {
	$yes_icon = '<img src="' . esc_url( plugin_dir_url( __DIR__ ) ) . 'assets/images/tick_icon_green.png" style="float:left;" />&nbsp;';
}

/**
 *  Array format
 *  First   : Feature
 *  Second  : Basic availability. Supports: Boolean, Array(Boolean and String values), String
 *  Pro     : Pro availability. Supports: Boolean, Array(Boolean and String values), String
 */
$comparison_data = array(
	array(
		__( 'Number of gift cards', 'wt-gift-cards-woocommerce' ),
		1,
		__( 'Unlimited', 'wt-gift-cards-woocommerce' ),
	),
	array(
		__( 'Physical gift cards', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Available templates for gift card', 'wt-gift-cards-woocommerce' ),
		4,
		'20+',
	),
	array(
		__( 'Gift card amounts', 'wt-gift-cards-woocommerce' ),
		__( 'Set predefined value', 'wt-gift-cards-woocommerce' ),
		__( 'Set predefined and custom values with min and max control', 'wt-gift-cards-woocommerce' ),
	),
	array(
		__( 'Allow users to schedule gift cards', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Category-wise template listing', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( "'Gift this product' option (Create gift cards worth of a specific product)", 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Redeem gift cards to wallet', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Allow users to use custom images for gift cards', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Manage store credit balances of users', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Allow users to print gift cards', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Upload custom gift card templates', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Refunds to store credit', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Choose templates for sending gift cards from the admin side', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Give discounts on gift card purchases', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Attach gift cards as PDFs to emails', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Set expiry and advanced usage restrictions', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Control gift card form fields', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
	array(
		__( 'Add captions for gift cards', 'wt-gift-cards-woocommerce' ),
		false,
		true,
	),
);

function wbte_gc_free_vs_pro_column_vl( $vl, $yes_icon, $no_icon ) {
	if ( is_array( $vl ) ) {

		foreach ( $vl as $value ) {

			if ( is_bool( $value ) ) {
				echo wp_kses_post( $value ? $yes_icon : $no_icon );
			} else {
				// string only
				echo wp_kses_post( $value );
			}
		}
	} elseif ( is_bool( $vl ) ) {

			echo wp_kses_post( $vl ? $yes_icon : $no_icon );
	} else {
		// string only
		echo wp_kses_post( $vl );
	}
}
?>

<table style="width:100%;  background: #fff url('<?php echo esc_url( WBTE_GC_FREE_URL . 'admin/images/background-freevspro.png' ); ?>') no-repeat bottom right; border-radius:8px; padding:2px; margin:2px 0; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom: 14px; background-size: 100% auto;">
	<tr style="vertical-align:middle;">
		
		<td style="padding:20px;">
			<div style="display:flex; align-items:center; gap:15px; width:56%;">
				<img src="<?php echo esc_url( WBTE_GC_FREE_URL . 'admin/modules/freevspro/assets/images/plugin_icon.png' ); ?>" alt="Plugin Icon" style="width:50px; height:50px;">
				<p style="margin:0; font-size:18px; font-weight:600; color:#111;">
					<?php esc_html_e( 'Upgrade to Premium and Enjoy the Full Potential of WooCommerce Gift Cards', 'wt-gift-cards-woocommerce' ); ?>
				</p>
			</div>
			
			<div style="margin-top:17px; display:flex; gap:25px; font-size:14px; color:#444; font-weight: 600;">
				<span><span style="color:#6abe45; margin-right:6px;" class="dashicons dashicons-yes"></span><?php esc_html_e( '99% Customer Satisfaction', 'wt-gift-cards-woocommerce' ); ?></span>
				<span><span style="color:#6abe45; margin-right:6px;" class="dashicons dashicons-yes"></span><?php esc_html_e( '30 Day money back guarantee', 'wt-gift-cards-woocommerce' ); ?></span>
			</div>
		</td>
		
		<td style="text-align:right; padding:20px; width:40%;">
			<a href="<?php echo esc_url( 'https://www.webtoffee.com/product/woocommerce-gift-cards/?utm_source=free_plugin_free_vs_pro&utm_medium=Gift_card_basic&utm_campaign=WooCommerce_Gift_Cards&utm_content=' . WBTE_GC_FREE_VERSION ); ?>" target="_blank"
				style="display:inline-block; background:#4750cb; font-size:15px; font-weight:500; border-radius:8px; padding:12px 20px; color:#fff; text-decoration:none;">
				<?php esc_html_e( 'Unlock Pro Features', 'wt-gift-cards-woocommerce' ); ?> →
			</a>
		</td>
	</tr>
</table>
<table class="wt_gc_freevs_pro">
	<tr class="wt_gc_freevspro_table_hd_tr">
		<td><?php esc_html_e( 'FEATURES', 'wt-gift-cards-woocommerce' ); ?></td>
		<td class="wt_gc_upsell_text_align_center"><?php esc_html_e( 'FREE', 'wt-gift-cards-woocommerce' ); ?></td>
		<td class="wt_gc_upsell_text_align_center"><?php esc_html_e( 'PREMIUM', 'wt-gift-cards-woocommerce' ); ?>&nbsp;<span><img src="<?php echo esc_url( WBTE_GC_FREE_URL . 'admin/images/pro_crown.svg' ); ?>" style="width:16px;"></span></td>
	</tr>
	<?php
	foreach ( $comparison_data as $val_arr ) {
		?>
		<tr class="wt_gc_freevspro_table_body_tr">
			<td><?php echo wp_kses_post( $val_arr[0] ); ?></td>
			<td class="wt_gc_upsell_text_align_center">
				<?php
				wbte_gc_free_vs_pro_column_vl( $val_arr[1], $yes_icon, $no_icon );
				?>
			</td>
			<td class="wt_gc_upsell_text_align_center">
				<?php
				wbte_gc_free_vs_pro_column_vl( $val_arr[2], $yes_icon, $no_icon );
				?>
			</td>
		</tr>
		<?php
	}
	?>
</table>

