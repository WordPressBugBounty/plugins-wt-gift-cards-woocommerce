<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
?>
<div class="wt_order_credit_coupons">
	<div class="wt-gc-store-credits">
		<?php
		foreach ( $order_items as $wbte_order_item_id => $wbte_order_item ) {
			$wbte_coupons_generated = $wbte_order_item->get_meta( 'wt_credit_coupon_generated' );

			if ( empty( $wbte_coupons_generated ) || ! is_array( $wbte_coupons_generated ) ) {
				continue;
			}

			$wbte_coupon_template_details = $wbte_order_item->get_meta( 'wt_credit_coupon_template_details' );
			$wbte_coupon_template_details = ( ! empty( $wbte_coupon_template_details ) && is_array( $wbte_coupon_template_details ) ? $wbte_coupon_template_details : array() );

			foreach ( $wbte_coupons_generated as $wbte_generated_coupon ) {
				$wbte_coupon_id  = $wbte_generated_coupon['coupon_id'];
				$wbte_coupon_obj = new WC_Coupon( $wbte_coupon_id );

				if ( ! $wbte_coupon_obj ) {
					continue;
				}

				$wbte_coupon_template_data     = array();
				$wbte_wt_store_credit_template = '';
				if ( isset( $wbte_coupon_template_details[ $wbte_coupon_id ] ) && is_array( $wbte_coupon_template_details[ $wbte_coupon_id ] ) ) {
					$wbte_coupon_template_data     = $wbte_coupon_template_details[ $wbte_coupon_id ];
					$wbte_wt_store_credit_template = ( isset( $wbte_coupon_template_data['wt_smart_coupon_template_image'] ) ? $wbte_coupon_template_data['wt_smart_coupon_template_image'] : '' );
				}

				$wbte_status_text      = __( 'Unknown', 'wt-gift-cards-woocommerce' );
				$wbte_send_button_text = '';
				$wbte_send_action_type = 'send';

				if ( self::is_email_action_choosed( $wbte_coupon_template_data ) ) {
					if ( $this->is_generated_coupon_activated( $wbte_coupon_id ) ) {
						$wbte_status_text       = __( 'Sent', 'wt-gift-cards-woocommerce' );
						$wbte_send_button_text = __( 'Resend', 'wt-gift-cards-woocommerce' );
						$wbte_send_action_type = 'resend';

					} elseif ( ! in_array( $order->get_status(), self::get_order_status_for_gift_card_email( $order ), true ) ) {
						$wbte_status_text       = __( 'Not activated, awaiting order status update', 'wt-gift-cards-woocommerce' );
						$wbte_send_button_text = __( 'Force send and activate', 'wt-gift-cards-woocommerce' );
						$wbte_send_action_type = 'force_send';

					} else {
						$wbte_status_text       = __( 'Unknown', 'wt-gift-cards-woocommerce' );
						$wbte_send_button_text = __( 'Send', 'wt-gift-cards-woocommerce' );
						$wbte_send_action_type = 'send';
					}
				}

				$wbte_coupon_edit_url = add_query_arg(
					array(
						'post'   => $wbte_coupon_id,
						'action' => 'edit',
					),
					admin_url( 'post.php' )
				);

				?>
				<div class="wt-gc-store-credit-item" data-order_item_id="<?php echo esc_attr( $wbte_order_item->get_id() ); ?>"> 
					<div class="wt-gc-store-credit-item-meta-block">
						
						<span class="wt-gc-store-credit-single-item">
							<b><?php esc_html_e( 'Coupon amount: ', 'wt-gift-cards-woocommerce' ); ?> </b> 
							<?php 
								$wbte_price_args = array(
									'coupon'   => $wbte_coupon_obj,
									'currency' => $order->get_currency(),
									'order'    => $order,
									'product'  => null,
								);
								echo wp_kses_post( Wbte_Woocommerce_Gift_Cards_Free_Common::get_giftcard_price( $wbte_price_args ) );
							?> 
						</span>

						<span class="wt-gc-store-credit-single-item"><b><?php esc_html_e( 'Coupon code: ', 'wt-gift-cards-woocommerce' ); ?> </b> <a href="<?php echo esc_url( $wbte_coupon_edit_url ); ?>"><?php echo esc_html( strtoupper( $wbte_coupon_obj->get_code() ) ); ?></a> </span>

						<?php
						if ( isset( $wbte_coupon_template_details[ $wbte_coupon_id ] ) && is_array( $wbte_coupon_template_details[ $wbte_coupon_id ] ) ) {
							$wbte_alternate_keys       = self::alternate_meta_keys();
							$wbte_store_credit_details = $wbte_coupon_template_details[ $wbte_coupon_id ];

							foreach ( self::get_customizable_giftcard_fields() as $wbte_field => $wbte_field_title ) {
								if ( isset( $wbte_alternate_keys[ $wbte_field ] ) && '' !== trim( $wbte_store_credit_details[ $wbte_alternate_keys[ $wbte_field ] ] ) ) {
									?>
									<span class="wt-gc-store-credit-single-item"><b><?php echo esc_html( $wbte_field_title ); ?>: </b> <?php echo esc_html( $wbte_store_credit_details[ $wbte_alternate_keys[ $wbte_field ] ] ); ?> </span>
									<?php
								}
							}
						}

						$wbte_expiry      = Wbte_Woocommerce_Gift_Cards_Free_Common::get_store_credit_expiry( $wbte_coupon_obj );
						$wbte_expiry_date = ! is_null( $wbte_expiry ) ? $wbte_expiry->date( 'd M Y' ) : '';

						if ( $wbte_expiry_date ) {
							?>
							<span class="wt-gc-store-credit-single-item"><b><?php esc_html_e( 'Expiry: ', 'wt-gift-cards-woocommerce' ); ?> </b> <?php echo esc_html( $wbte_expiry_date ); ?> </span>
							<?php
						}
						?>
							<span class="wt-gc-store-credit-single-item"><b><?php esc_html_e( 'Status: ', 'wt-gift-cards-woocommerce' ); ?> </b><?php echo esc_html( $wbte_status_text ); ?> </span>
						<?php
						/**
						 *  Last sent date
						 */
						$wbte_last_send_date_gmt = get_post_meta( $wbte_coupon_id, '_wt_sc_send_date_gmt', true );

						if ( $wbte_last_send_date_gmt ) {
							$wbte_last_send_date = get_date_from_gmt( $wbte_last_send_date_gmt, wc_date_format() . ' ' . wc_time_format() );
							if ( $wbte_last_send_date ) {
								?>
								<span><b><?php esc_html_e( 'Last sent: ', 'wt-gift-cards-woocommerce' ); ?> </b><?php echo esc_html( $wbte_last_send_date ); ?> </span>
								<?php
							}
						}


						if ( '' !== $wbte_wt_store_credit_template ) {
							$wbte_template_data = self::get_gift_card_template( $wbte_wt_store_credit_template );
							if ( isset( $wbte_template_data['image_url'] ) && '' !== $wbte_template_data['image_url'] ) {
								?>
									<br />
									<span class="wt-gc-store-credit-single-item"><img src="<?php echo esc_attr( $wbte_template_data['image_url'] ); ?>" width="200"></span>
									<br />
								<?php
							}
						}
						?>
					</div>
					<?php
					if ( self::is_email_action_choosed( $wbte_coupon_template_data ) ) {
						?>
						<div class="wt-send-coupon">
							<button order-id="<?php echo esc_attr( $order_id ); ?>" coupon-id="<?php echo esc_attr( $wbte_coupon_id ); ?>" class="btn wt-btn-resend-store-credit button-primary button-large" type="button" data-resend-text="<?php esc_attr_e( 'Resend', 'wt-gift-cards-woocommerce' ); ?>" data-action-type="<?php echo esc_attr( $wbte_send_action_type ); ?>"><?php echo esc_html( $wbte_send_button_text ); ?></button>
						</div>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
		?>
	</div>
</div>
<style type="text/css">
.wt-gc-store-credits { display:flex; flex-wrap:wrap; gap:2%; width:100%; padding-top:15px; }
.wt-gc-store-credit-item{ flex:1 1 300px; }
.wt-gc-store-credit-item-meta-block .wt-gc-store-credit-single-item{ display:inline-block; width:100%; padding:5px 0px; }
</style>
<script type="text/javascript">
jQuery('document').ready(function()
{
	jQuery(document).on('click', '.wt-btn-resend-store-credit', function(e){
		
		if(!confirm(wt_gc_params.msgs.are_you_sure))
		{
			return false;
		}

		e.preventDefault();
		var elm = jQuery(this);
		var metabox_elm = jQuery('#wt-gc-coupons-in-order');

		var data = {
			'action'        : 'wt_resend_store_credit_coupon',
			'_wpnonce'      : wt_gc_params.nonce,
			'_wt_order_id'  : elm.attr('order-id'),
			'_wt_coupon_id' : elm.attr('coupon-id'),
			'action_type'   : elm.attr('data-action-type'),
		};
		
		var html_bck=elm.html();
		elm.html(wt_gc_params.msgs.please_wait).prop('disabled', true);
		wt_gc_block_node(metabox_elm);
		
		jQuery.ajax({
			type: "POST",
			url: wt_gc_params.ajax_url,
			data: data,
			dataType: 'json',
			success:function(data)
			{
				wt_gc_unblock_node(metabox_elm);
				elm.html(html_bck).prop('disabled', false);

				if(data.status)
				{
					wt_gc_notify_msg.success(data.msg);
					elm.html(elm.attr('data-resend-text'));

					/** Reload the page via ajax to show updated details */
					wt_gc_block_node(metabox_elm);
					jQuery.get('', function(data){
						
						wt_gc_unblock_node(metabox_elm);
						let temp_elm = jQuery('<div>').html(data);
						let order_coupon_temp_elm = temp_elm.find('#wt-gc-coupons-in-order .wt_order_credit_coupons');
						let order_notes_temp_elm = temp_elm.find('#woocommerce-order-notes .inside .order_notes');
						
						if(order_coupon_temp_elm.length)
						{
							metabox_elm.find('.wt_order_credit_coupons').html(order_coupon_temp_elm.html());
						}

						if(order_notes_temp_elm.length)
						{
							jQuery('#woocommerce-order-notes .inside .order_notes').html(order_notes_temp_elm.html());
						}
					});

				}else
				{
					wt_gc_notify_msg.error(data.msg);
				}
			},
			error:function()
			{
				wt_gc_unblock_node(metabox_elm);
				elm.html(html_bck).prop('disabled', false);
				wt_gc_notify_msg.error(wt_gc_params.msgs.error, false);
			}
		});
	});
});
</script>
