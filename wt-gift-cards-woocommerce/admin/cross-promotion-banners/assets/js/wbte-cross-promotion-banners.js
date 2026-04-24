(function ($) {
    'use strict';

    $(function () {
        var wt_cta_banner = {
            init: function () {
                this.initToggleFeatures();
                this.initDismissButtons();
            },

            initToggleFeatures: function() {
                const toggleBtn = $('.wt-cta-toggle');
                const hiddenFeatures = $('.hidden-feature');
                
                toggleBtn.text(toggleBtn.data('show-text'));
                
                toggleBtn.on('click', function(e) {
                    e.preventDefault();
                    
                    hiddenFeatures.slideToggle(100, function() {
                        if ( $(this).is(':visible') ) {
                            toggleBtn.text(toggleBtn.data('hide-text'));
                        } else {
                            toggleBtn.text(toggleBtn.data('show-text'));
                        }
                    });
                });
            },

            initDismissButtons: function() {
                $('.wt-cta-dismiss').on('click', function(e) {
                    e.preventDefault();
                    const $this    = $(this);
                    const $banner  = $this.closest('.postbox');
                    const bannerId = $banner.attr('id');
                    
                    /** Determine which banner is being dismissed and use appropriate AJAX data */
                    let ajaxData = {};
                    
                    if ('wt_product_import_export_pro' === bannerId) {
                        ajaxData = {
                            action: 'wt_dismiss_product_ie_cta_banner',
                            nonce: typeof wt_product_ie_cta_banner_ajax !== 'undefined' ? wt_product_ie_cta_banner_ajax.nonce : ''
                        };
                    } else if ('wt_pdf_invoice_pro' === bannerId) {
                        ajaxData = {
                            action: 'wt_dismiss_invoice_cta_banner',
                            nonce: typeof wt_invoice_cta_banner_ajax !== 'undefined' ? wt_invoice_cta_banner_ajax.nonce : ''
                        };
                    } else if ('wbte-sc-upgrade-to-pro' === bannerId) {
                        ajaxData = {
                            action: 'wt_dismiss_smart_coupon_cta_banner',
                            nonce: typeof wt_smart_coupon_cta_banner_ajax !== 'undefined' ? wt_smart_coupon_cta_banner_ajax.nonce : ''
                        };
                    }
                    
                    if (ajaxData.action && ajaxData.nonce) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: ajaxData,
                            success: function(response) {
                                if (response.success) {
                                    $banner.hide();
                                }
                            }
                        });
                    }
                });
            }
        };

        wt_cta_banner.init();

        /** Hide hidden features by default */
        $('.hidden-feature').hide();
    });
})(jQuery);