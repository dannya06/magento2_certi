define([
    'Magento_Checkout/js/model/quote'
], function (quote) {
    'use strict';

    return {
        /**
         * Array of required address fields
         */
        requiredFields: ['countryId', 'region', 'regionId', 'postcode', 'city'],

        /**
         * @param {Object} payload
         * @returns {Object}
         */
        setShippingMethodInformation: function (payload) {
            if (quote.shippingMethod() && quote.shippingMethod()['method_code']) {
                payload.addressInformation['shipping_method_code'] = quote.shippingMethod()['method_code'];
                payload.addressInformation['shipping_carrier_code'] = quote.shippingMethod()['carrier_code'];
            }

            return payload;
        }
    };
});
