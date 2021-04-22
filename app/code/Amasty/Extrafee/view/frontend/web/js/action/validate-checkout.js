define([
    'jquery',
    'uiRegistry'
], function ($, registry) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        selectorExtraFeeBlock = '[data-amexfee-js="block"]';

    return function (source) {
        var provider = source || registry.get('checkoutProvider');

        if (checkoutConfig.amasty.extrafee.enabledOnCheckout) {
            provider.set('params.invalid', false);
            provider.trigger('amastyExtrafee.data.validate');
        }

        if (provider.get('params.invalid')) {
            $('html, body').animate({
                scrollTop: $(selectorExtraFeeBlock).offset().top
            }, 300);

            return false;
        }

        return true;
    };
});
