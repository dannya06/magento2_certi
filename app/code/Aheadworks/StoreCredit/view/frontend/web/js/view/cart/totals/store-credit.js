/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/*global define*/
define(
    [
        'Aheadworks_StoreCredit/js/view/summary/store-credit'
    ],
    function (Component) {
        "use strict";

        var awStoreCreditRemoveUrl  = window.checkoutConfig.payment.awStoreCredit.removeUrl;

        return Component.extend({
            defaults: {
                template: 'Aheadworks_StoreCredit/cart/totals/store-credit'
            },
            
            /**
             * @override
             *
             * @returns {boolean}
             */
            isDisplayed: function () {
                return this.getPureValue() != 0;
            },

            /**
             * Retrieve url for remove Store Credit
             *
             * @returns {String}
             */
            getRemoveUrl: function () {
                return awStoreCreditRemoveUrl;
            },
        });
    }
);