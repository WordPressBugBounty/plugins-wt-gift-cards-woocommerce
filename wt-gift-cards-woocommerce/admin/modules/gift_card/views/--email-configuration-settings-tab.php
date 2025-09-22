<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 *  @since 1.1.0
 */

if(current_user_can('install_plugins') && current_user_can('update_plugins'))
{
    $placeholder_arr = array('<a>', '</a>', '<a href="'.esc_url($mpdf_wp_url).'" target="_blank">', '</a>');

    if(!$is_mpdf_active && !$is_mpdf_exists)
    {
        /* translators: 1: Opening link tag, 2: Closing link tag, 3: Opening link tag, 4: Closing link tag */
        $enable_mpdf_msg = __('Requires mPDF library to add PDF support. %1$s Click here %2$s to install the %3$s mPDF add-on by WebToffee %4$s (free).', 'wt-gift-cards-woocommerce');
        $placeholder_arr[0] = '<a href="' . esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $mpdf_slug), 'install-plugin_' . $mpdf_slug)) . '">';

    }elseif($is_mpdf_active && !$is_required_mpdf_version_installed)
    {
		/* translators: 1: mPDF version, 2: Opening link tag, 3: Closing link tag, 4: Opening link tag, 5: Closing link tag */
        $enable_mpdf_msg = __('Requires mPDF version %1$s or greater to add PDF support. %2$s Click here %3$s to update the %4$s mPDF add-on by WebToffee %5$s (free).', 'wt-gift-cards-woocommerce');
        $placeholder_arr[0] = '<a href="' . esc_url(wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $mpdf_slug), 'upgrade-plugin_' . $mpdf_slug)) . '">';
        array_unshift($placeholder_arr , $mpdf_required_version);

    }elseif(!$is_mpdf_active && $is_mpdf_exists)
    {
		/* translators: 1: Opening link tag, 2: Closing link tag, 3: Opening link tag, 4: Closing link tag */
        $enable_mpdf_msg = __('Requires mPDF library to add PDF support. %1$s Click here %2$s to activate the %3$s mPDF add-on by WebToffee %4$s(free).', 'wt-gift-cards-woocommerce');
        $placeholder_arr[0] = '<a href="' . esc_url(wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin=' . urlencode($mpdf_path) . '&plugin_status=all&paged=1&s'), 'activate-plugin_' . $mpdf_path)) . '">';
    }else
    {
        $enable_mpdf_msg = '';
        $placeholder_arr = array();
    }
}else
{
	/* translators: 1: Opening link tag, 2: Closing link tag */
    $enable_mpdf_msg = __('Requires mPDF library to add PDF support. Please install the %1$s mPDF add-on by WebToffee %2$s(free).', 'wt-gift-cards-woocommerce');
    $placeholder_arr = array('<a href="'.esc_url($mpdf_wp_url).'" target="_blank">', '</a>');
}

$attr = (!$is_mpdf_active || !$is_required_mpdf_version_installed ? 'disabled = "disabled"' : '');

?>
<table class="wt-gc-form-table wt-gc-product-page-tab-form-table">
	<?php
		Wbte_Woocommerce_Gift_Cards_Free_Admin::generate_form_field(
			array(
				array(
					'type'          => 'field_group_head', //field type
					'head'          => __('Configure gift card email', 'wt-gift-cards-woocommerce'),
					'group_id'      => 'product_page_titles', //field group id
					'show_on_default' => 1,
				),
				array(
					'label'       => __( 'Configure gift card email', 'wt-gift-cards-woocommerce' ),
					'non_field'   => true,
					'option_name' => 'configure_gift_card_email_link',
					'type'        => 'plaintext',
					'text'        => '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=wbte_woocommerce_gift_cards_free_email' ) ) . '" class="button button-secondary" target="_blank">' . __( 'Configure email', 'wt-gift-cards-woocommerce' ) . '</a>',
					'help_text' => __('Redirects to WooCommerce > Settings > Emails. Can configure the gift cards related email from there', 'wt-gift-cards-woocommerce'),

				),
				array(
					'label'         =>  __('Attach Gift Card as PDF', 'wt-gift-cards-woocommerce'),
					'option_name'   =>  "attach_as_pdf",
					'type'          =>  "checkbox",
					'checkbox_label'  =>  __('Enable', 'wt-gift-cards-woocommerce'),
					'field_vl'      =>  'yes',
					'attr'          =>  $attr,
					'after_form_field' => ($enable_mpdf_msg ? '<div class="wt_gc_msgs wt_gc_msg_wrn" style="margin-top:5px;">'.vsprintf($enable_mpdf_msg, $placeholder_arr).'</div>' : ''),
				),
			)
		, $this->module_id);
	?>
</table>