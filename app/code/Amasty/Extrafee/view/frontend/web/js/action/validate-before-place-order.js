define([
    'Amasty_Extrafee/js/action/validate-checkout'
], function (validateCheckout) {
    'use strict';

    return {
        /**
         * @returns {boolean}
         */
        validate: function () {
            return validateCheckout();
        }
    };
});
