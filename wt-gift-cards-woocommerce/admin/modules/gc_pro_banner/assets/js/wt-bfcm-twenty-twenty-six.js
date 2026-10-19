(function ($) {
    'use strict';

    $(function () {
        const wt_bfcm_twenty_twenty_six_banner = {
            init: function () { 
                const data_obj = {
                    _wpnonce: wbte_gc_bfcm_2026_banner_js_params.nonce,
                    action: wbte_gc_bfcm_2026_banner_js_params.action,
                    wbte_gc_bfcm_2026_banner_action_type: '',
                };

                /** The View plugins button opens the link normally; only the close button hides the banner permanently. */
                $(document).on('click', '.wbte-gc-bfcm-banner-2026 .notice-dismiss', function(e) {
                    e.preventDefault();
                    data_obj['wbte_gc_bfcm_2026_banner_action_type'] = 2; /** Closed by user. */
                    
                    $.ajax({
                        url: wbte_gc_bfcm_2026_banner_js_params.ajax_url,
                        data: data_obj,
                        type: 'POST',
                    });
                });
            }
        };
        wt_bfcm_twenty_twenty_six_banner.init();
    });

})(jQuery);