define([
    'Magento_SalesRule/js/view/summary/discount',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'mage/url',
    'Magento_Catalog/js/price-utils'
], function (Component, $, quote, $t, url, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Icube_Cashback/cashback-message',
            message: '',
            isCashback: false,
            isAjaxRunning: false
        },

        initObservable: function () {
            this._super();
            this.observe(['isCashback', 'message']);

            return this;
        },

        initialize: function () {
            this._super();

            quote.totals.subscribe(this.refreshCashbackMessage.bind(this));

            $(document).on(
                'click',
                '#shipping-method-buttons-container button[data-role="opc-continue"]',
                this.hideCashbackMessage.bind(this)
            );
        },

        hideCashbackMessage: function () {
            this.isCashback(false);
            this.refreshCashbackMessage.bind(this);
        },

        refreshCashbackMessage: function (totals) {
            var _this = this;

            if (!_this.isAjaxRunning) {
                _this.isAjaxRunning = true;
                $.ajax({
                    url: url.build('cashback/ajax/checkcashback'),
                    type: "GET",
                    dataType: 'json'
                }).done(function (response) {
                    if (response.is_cashback) {
                        var promo_names = response.data.map((rule) => {
                            return rule.promo_name;
                        });
                        var amount = priceUtils.formatPrice(response.total_cashback, quote.getPriceFormat());
                        _this.message = $.mage.__('Congratulation! You will get cashback <strong>%1</strong> from ' +
                            'promo(s) <strong>%2</strong> into your store credit').replace('%1', amount)
                            .replace('%2', promo_names);
                        _this.isCashback(true);

                        if ($('body.checkout-cart-index').length) {
                            $('.cashback-message-wrapper.cloned').remove();
                            $('.cart-summary .cashback-message-wrapper').clone().addClass('cloned')
                                .insertBefore($('.cart-summary'));
                        }
                    } else {
                        _this.isCashback(false);
                    }
                    _this.isAjaxRunning = false;
                });
            }
        }
    });
});
