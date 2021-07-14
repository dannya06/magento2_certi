define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/quote'
], function ($, priceUtils, quote) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,

        // Excluding price with tax type
        EXCLUDING_PRICE_TYPE = 1,

        // Including price with tax type
        INCLUDING_PRICE_TYPE = 2,

        // Including and excluding price with tax type
        BOTH_PRICE_TYPE = 3;

    return {
        displayPriceMode: {
            total: checkoutConfig.amasty.extrafee.displayPriceModeTotal,
            block: checkoutConfig.amasty.extrafee.displayPriceModeBlock,
        },
        translation: {
            bothPriceType: $.mage.__('%1 (Excl. Tax: %2)')
        },

        /**
         * @param {Object} item
         * @return {String}
         */
        getPrice: function (item) {
            var price,
                type = 'block';

            switch (this.displayPriceMode[type]) {
                case BOTH_PRICE_TYPE:
                    price = this.translation.bothPriceType
                        .replace('%1', this.getFormattedPrice(item['value_incl_tax']))
                        .replace('%2', this.getFormattedPrice(item['value_excl_tax']));

                    break;
                case INCLUDING_PRICE_TYPE:
                    price = this.getFormattedPrice(item['value_incl_tax']);

                    break;
                case EXCLUDING_PRICE_TYPE:
                    price = this.getFormattedPrice(item['value_excl_tax']);

                    break;
                default:
                    price = this.getFormattedPrice(item.price);
            }

            return price;
        },

        /**
         * @param {Number} price
         * @return {String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },

        /**
         * @param {String} type
         * @return {Boolean}
         */
        isBothPricesDisplayed: function (type) {
            return this.displayPriceMode[type] === BOTH_PRICE_TYPE;
        },

        /**
         * @param {String} type
         * @return {Boolean}
         */
        isIncludingTaxDisplayed: function (type) {
            return this.displayPriceMode[type] === INCLUDING_PRICE_TYPE;
        },

        /**
         * @param {String} type
         * @return {Boolean}
         */
        isExcludingDisplayed: function (type) {
            return this.displayPriceMode[type] === EXCLUDING_PRICE_TYPE;
        }
    };
});
