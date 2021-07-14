define([
    'Amasty_Extrafee/js/action/validate-checkout'
], function (validateCheckout) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (Shipping) {
        var mixin = {
            /**
             * @returns {boolean|*}
             */
            validateShippingInformation: function () {
                var result = this._super(),
                    isValid = validateCheckout(this.source);

                return isValid && result;
            }
        };

        if (checkoutConfig.checkoutBlocksConfig && !window.amasty_checkout_disabled) {
            return Shipping;
        }

        return Shipping.extend(mixin);
    };
});
