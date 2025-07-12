(function($) {
    'use strict';

    $(document).ready(function() {
		/**
		 * Gift card balance check form actions
		 */
		if($('.wbte_gc_check_balance_form').length > 0){
			$('.wbte_gc_check_balance_form').on('submit', function(e) {

				e.preventDefault();
				var form = $(this);
				var resultDiv = $('.wbte_gc_balance_result');
				var data = {
					'action'		  : 'wbte_gc_check_balance',
					'_wpnonce'		 	  : wt_gc_params.nonce,
					'coupon_code'	  : form.find('.wbte_gc_coupon_code').val(),
					'restricted_email': form.find('.wbte_gc_restricted_email').val()
				}
				$.ajax({
					url: wt_gc_params.ajax_url,
					type: 'POST',
					dataType: 'json',
					data: data,
					beforeSend: function() {
						form.find('button[type="submit"]').prop('disabled', true);
						resultDiv.html('').removeClass('wbte_gc_balance_success wbte_gc_balance_error');
					},
					success: function(response) {
						console.log(response)
						
						if (response.success) {
							resultDiv.html(response.data.message).addClass('wbte_gc_balance_success');
						} else {
							resultDiv.html(response.data.message).addClass('wbte_gc_balance_error');
						}
					},
					error: function() {
						resultDiv.html(wt_gc_params.ajax_error).addClass('wbte_gc_balance_error');
					},
					complete: function() {
						form.find('button[type="submit"]').prop('disabled', false);
					}
				});
			});
		}
    });
})(jQuery);