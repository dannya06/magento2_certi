define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals',
    'Amasty_Extrafee/js/model/tax-utils'
], function ($, Component, quote, totals, taxUtils) {
    'use strict';

    var NAME_SEGMENT = 'amasty_extrafee';

    return Component.extend({
        defaults: {
            template: 'Amasty_Extrafee/checkout/summary/totals/fee',
            items: [],
            selectorsToggleClass: '-show',
            baseToggleClass: '-expanded'
        },
        taxUtils: taxUtils,
        totals: quote.getTotals(),

        /**
         * @returns {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'items'
                ]);
            totals.totals.subscribe(this.setItems, this);

            return this;
        },

        /**
         * @returns {void}
         */
        setItems: function () {
            var extraFeeDetails = this.getDetailsSegment(),
                items = JSON.parse(extraFeeDetails.items);

            this.items(items);
        },

        /**
         * @returns {Object}
         */
        getDetailsSegment: function () {
            var segmentExtraFee = totals.getSegment(NAME_SEGMENT);

            if (!segmentExtraFee) {
                return {
                    items: null
                };
            }

            var extensionAttributes = segmentExtraFee['extension_attributes'];

            return extensionAttributes['tax_amasty_extrafee_details'];
        },

        /**
         * Get formatted price
         *
         * @param {String} type
         * @return {String}
         */
        getValue: function (type) {
            var price = 0;

            if (!this.totals()) {
                return '';
            }

            price = this.getDetailsSegment()[type];

            return this.getFormattedPrice(price);
        },

        /**
         * @returns {Boolean}
         */
        isDisplayed: function () {
            var amastySegmentExtraFee = totals.getSegment(NAME_SEGMENT);

            if (this.totals() && amastySegmentExtraFee !== null && amastySegmentExtraFee.value > 0) {
                return true;
            }

            return false;
        },

        /**
         * @param {String} feeTaxType
         * @returns {Boolean}
         */
        isFeesExpanded: function (feeTaxType) {
            return $('.-' + feeTaxType).hasClass(this.baseToggleClass);
        }
    });
});
