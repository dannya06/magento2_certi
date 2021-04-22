/* global AdminOrder, payment, order  */

/**
 * Extend admin order form
 *
 * @global {Object} AdminOrder - Constructor admin order create
 * @global {Object} order - Instance admin order create
 * @global {Object} payment
 * @returns {Object}
 */
define([
    'jquery',
    'Magento_Sales/order/create/scripts'
], function ($) {
    'use strict';


    return {
        editFormSelector: '#edit_form',
        feeItemSelector: '[data-amexfee-js="items"]',
        extraFeeBlock: '[data-amexfee-js="block"]',

        /**
         * @returns {Object}
         */
        initFeeOrderCreate: function () {
            /**
             * Override method for update Fees after a shipping method set
             * @param {Object} method
             * @returns {void}
             */
            AdminOrder.prototype.setShippingMethod = function (method) {
                var data = {};

                data['order[shipping_method]'] = method;
                this.loadArea([
                    'shipping_method',
                    'items',
                    'totals',
                    'billing_method'], true, data);
            };

            /**
             * Override method for update Fees after a payment method switch
             * @param {Object} method
             * @returns {void}
             */
            payment.switchMethod = function (method) {
                var data = {},
                    editForm = $(this.editFormSelector);

                editForm
                    .off('submitOrder')
                    .on('submitOrder', function () {
                        $(this).trigger('realOrder');
                    });
                editForm.trigger('changePaymentMethod', [ method ]);
                this.setPaymentMethod(method);
                data['order[payment_method]'] = method;
                this.loadArea([
                    'card_validation',
                    'items'], true, data);
            }.bind(order);

            return this;
        },

        /**
         * @returns {void}
         */
        updateExtraFee: function () {
            var self = this,
                fees = {},
                data = {},
                extraFeeSelected = self.extraFeeBlock + ' :input:checked,'
                    + '' + self.extraFeeBlock + ' select option:selected';

            $(self.feeItemSelector).each(function () {
                fees[$(this).data('feeId')] = [];
            });
            $(extraFeeSelected).each(function () {
                var elem = $(this),
                    feeId,
                    value = elem.val();

                if (value) {
                    feeId = elem.closest(self.feeItemSelector).data('feeId');
                    fees[feeId].push(parseInt(value, 10));
                }
            });

            if (!$.isEmptyObject(fees)) {
                data.am_extra_fees = JSON.stringify(fees);
                order.loadArea([
                    'items',
                    'shipping_method',
                    'totals',
                    'billing_method'], true, data);
            }
        }
    };
});
