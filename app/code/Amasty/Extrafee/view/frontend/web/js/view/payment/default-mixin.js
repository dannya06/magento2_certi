define([
    'Magento_Checkout/js/model/payment/additional-validators',
    'Amasty_Extrafee/js/action/validate-before-place-order'
], function (additionalValidators, validationPlaceOrder ) {
    'use strict';

    return function (paymentDefault) {
        additionalValidators.registerValidator(validationPlaceOrder);

        return paymentDefault;
    };
});
