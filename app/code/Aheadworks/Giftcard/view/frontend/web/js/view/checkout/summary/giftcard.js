/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals',
    'Aheadworks_Giftcard/js/action/remove-giftcard-code',
], function (Component, totals, removeAction) {
    'use strict';

    var giftcardRemoveUrl = window.checkoutConfig.awGiftcard.removeUrl;

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Giftcard/checkout/summary/giftcard'
        },
        isAjaxRemoveLink: true,
        code: 'aw_giftcard',
        totals: totals.totals(),

        /**
         * Is display Gift Card totals
         *
         * @return {boolean}
         */
        isDisplayed: function() {
            return this.isFullMode() && this.totals
                && totals.getSegment(this.code) && totals.getSegment(this.code).value != 0;
        },

        /**
         * Retrieve applied Gift Card codes
         *
         * @returns {Array}
         */
        getGiftcardCodes: function () {
            if (this.totals && totals.getSegment(this.code)) {
                return totals.getSegment(this.code).extension_attributes.aw_giftcard_codes;
            }
            return [];
        },

        /**
         * Retrieve formatted value
         *
         * @param {Number} value
         * @returns {String}
         */
        getValue: function (value) {
            return this.getFormattedPrice(value);
        },

        /**
         * Remove Gift Card by code
         *
         * @param {String} giftcardCode
         */
        removeByCode: function (giftcardCode) {
            if (this.isAjaxRemoveLink) {
                removeAction(giftcardCode)
            } else {
                window.location.href = giftcardRemoveUrl + 'code/' + giftcardCode;
            }
        }
    });
});
