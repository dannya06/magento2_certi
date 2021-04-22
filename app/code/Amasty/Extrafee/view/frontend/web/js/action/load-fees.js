define([
    'underscore',
    'mage/storage',
    'Amasty_Extrafee/js/model/fees',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/totals',
    'Amasty_Extrafee/js/model/utils'
], function (
    _,
    storage,
    feesService,
    resourceUrlManager,
    quote,
    errorProcessor,
    totalsService,
    utils
) {
    'use strict';

    return {
        /**
         * Load fees
         *
         * @returns {void}
         */
        loadFees: function () {
            var payload,
                address;

            if (feesService.rejectFeesLoading()) {
                return;
            }

            feesService.isLoading(true);
            address = this.addressExtend(this.collectAddress());
            payload = this.collectPayload(address);
            this.getFees(payload);
        },

        /**
         * @returns {Object}
         */
        collectAddress: function () {
            var address,
                newAddress;

            newAddress = quote.shippingAddress() ? quote.shippingAddress() : quote.billingAddress();
            address = _.pick(newAddress, utils.requiredFields);

            return address;
        },

        /**
         * Add extension attributes for address
         *
         * @param {Object} address
         * @returns {Object}
         */
        addressExtend: function (address) {
            var paymentMethod = quote.paymentMethod() ? quote.paymentMethod().method : null,
                city = quote.shippingAddress() ? quote.shippingAddress().city : '';

            if (quote.isVirtual() && quote.billingAddress()) {
                city = quote.billingAddress().city;
            }

            address.extension_attributes = {
                advanced_conditions: {
                    custom_attributes: quote.shippingAddress() ? quote.shippingAddress().custom_attributes : [],
                    payment_method: paymentMethod,
                    city: city,
                    shipping_address_line: quote.shippingAddress() ? quote.shippingAddress().street : null,
                    billing_address_country: quote.billingAddress() ? quote.billingAddress().countryId : null,
                    currency: totalsService.totals() ? totalsService.totals().quote_currency_code : null
                }
            };

            return address;
        },

        /**
         * @param {Object} address
         * @returns {Object}
         */
        collectPayload: function (address) {
            var payload = {};

            payload.addressInformation = {
                address: address
            };

            payload = utils.setShippingMethodInformation(payload);

            return payload;
        },

        /**
         * @param {Object} payload
         * @returns {void}
         */
        getFees: function (payload) {
            var serviceUrl = resourceUrlManager.getUrlForFetchFees(quote);

            storage.post(
                serviceUrl,
                JSON.stringify(payload),
                false
            ).done(
                this.requestHandler
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            ).always(
                function () {
                    feesService.isLoading(false);
                }
            );
        },

        /**
         * @param {Object} result
         * @returns {void}
         */
        requestHandler: function (result) {
            if (!_.isUndefined(result.fees)) {
                feesService.fees(result.fees);
            }

            if (!_.isUndefined(result.totals)) {
                quote.setTotals(result.totals);
            }
        }
    };
});
