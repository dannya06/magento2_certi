define(function () {
    'use strict';

    return function (resourceUrlManager) {
        /**
         * @param {Object} quote
         * @return {String|*}
         */
        resourceUrlManager.getUrlForTotalsEstimationForFee = function (quote) {
            var params = this.getCheckoutMethod() === 'guest'
                    ? {
                        cartId: quote.getQuoteId()
                    } : {},
                urls = {
                    'guest': '/amasty_extrafee/guest-carts/:cartId/totals-information',
                    'customer': '/amasty_extrafee/carts/mine/totals-information'
                };

            return this.getUrl(urls, params);
        };

        /**
         * @param {Object} quote
         * @return {String|*}
         */
        resourceUrlManager.getUrlForFetchFees = function (quote) {
            var params = this.getCheckoutMethod() === 'guest'
                    ? {
                        cartId: quote.getQuoteId()
                    } : {},
                urls = {
                    'guest': '/amasty_extrafee/guest-carts/:cartId/fees-information',
                    'customer': '/amasty_extrafee/carts/mine/fees-information'
                };

            return this.getUrl(urls, params);
        };

        return resourceUrlManager;
    };
});
