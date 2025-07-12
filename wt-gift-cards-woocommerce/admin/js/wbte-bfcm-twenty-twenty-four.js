(function ($) {
    'use strict';
    $(function () {
        var wbte_bfcm_twenty_twenty_four_banner = {
            init: function () { 
                var data_obj = {
                    _wpnonce: wbte_bfcm_twenty_twenty_four_banner_js_params.nonce,
                    action: wbte_bfcm_twenty_twenty_four_banner_js_params.action,
                    wbte_bfcm_twenty_twenty_four_banner_action_type: '',
                };

                jQuery(document).on('click', '.wbte-bfcm-banner-2024 .wbte-bfcm-banner-body-button', function (e) { 
                    e.preventDefault(); 
                    var elm = $(this);
                    window.open(wbte_bfcm_twenty_twenty_four_banner_js_params.cta_link, '_blank'); 
                    elm.parents('.wbte-bfcm-banner-2024').hide();
                    data_obj['wbte_bfcm_twenty_twenty_four_banner_action_type'] = 3; // Clicked the button.
                    
                    $.ajax({
                        url: wbte_bfcm_twenty_twenty_four_banner_js_params.ajax_url,
                        data: data_obj,
                        type: 'POST'
                    });
                }).on('click', '.wbte-bfcm-banner-2024 .notice-dismiss', function(e) {
                    e.preventDefault();
                    data_obj['wbte_bfcm_twenty_twenty_four_banner_action_type'] = 2; // Closed by user
                    
                    $.ajax({
                        url: wbte_bfcm_twenty_twenty_four_banner_js_params.ajax_url,
                        data: data_obj,
                        type: 'POST',
                    });
                });
            }
        };
        wbte_bfcm_twenty_twenty_four_banner.init();
    });
})(jQuery);