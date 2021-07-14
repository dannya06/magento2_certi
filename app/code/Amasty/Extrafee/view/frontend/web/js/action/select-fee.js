define([
    'underscore',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/totals',
    'Amasty_Extrafee/js/model/fees',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/action/set-shipping-information',
    'Amasty_Extrafee/js/model/utils'
], function (
    _,
    resourceUrlManager,
    quote,
    storage,
    totalsService,
    feesService,
    errorProcessor,
    setShippingInformationAction,
    utils
) {
    'use strict';

    var EXTRA_FEE_SEGMENT_NAME = 'amasty_extrafee';

    return {
        /**
         * Select Fee
         *
         * @param {Number} feeId
         * @param {Array} optionsIds
         * @returns {void}
         */
        selectFee: function (feeId, optionsIds) {
            var payload;

            totalsService.isLoading(true);
            payload = this.collectPayload(feeId, optionsIds);
            feesService.rejectFeesLoading(true);
            this.setFee(payload);
        },

        /**
         * @param {Number} feeId
         * @param {Array} optionsIds
         * @returns {Object}
         */
        collectPayload: function (feeId, optionsIds) {
            var payload = {
                information: {
                    fee_id: feeId,
                    options_ids: optionsIds
                },
                addressInformation: {
                    address: _.pick(quote.shippingAddress(), utils.requiredFields)
                }
            };

            payload = utils.setShippingMethodInformation(payload);

            return payload;
        },

        /**
         * @param {Object} payload
         * @returns {void}
         */
        setFee: function (payload) {
            var serviceUrl = resourceUrlManager.getUrlForTotalsEstimationForFee(quote);

            storage.post(
                serviceUrl,
                JSON.stringify(payload),
                false
            ).done(
                this.requestHandler.bind(this)
            ).always(
                function () {
                    totalsService.isLoading(false);
                    feesService.rejectFeesLoading(false);
                }
            );
        },

        /**
         * @param {Object} result
         * @returns {void}
         */
        requestHandler: function (result) {
            if (this.isTotalsOnlyExtraFee(result)) {
                setShippingInformationAction();
            } else {
                quote.setTotals(result);
            }
        },

        /**
         * @param {Object} result
         * @returns {boolean}
         */
        isTotalsOnlyExtraFee: function (result) {
            var amastyExtrafeeTotal = 0,
                grandTotal,
                feeSegment;

            if (!result || !this.hasProperties(result, ['grand_total', 'total_segments'])) {
                return false;
            }

            grandTotal = result.grand_total;
            feeSegment = result.total_segments.filter(function (segment) {
                return segment.code === EXTRA_FEE_SEGMENT_NAME;
            });

            if (!_.isEmpty(feeSegment)) {
                amastyExtrafeeTotal = feeSegment[0].value;
            }

            return (grandTotal - amastyExtrafeeTotal) === 0;
        },

        /**
         * Has properties in object
         *
         * @param {Object} object
         * @param {Array} properties
         * @returns {boolean}
         */
        hasProperties: function (object, properties) {
            return properties.every(function (prop) {
                return _.has(object, prop);
            });
        }
    };
});
