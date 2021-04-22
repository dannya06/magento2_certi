define([
    'underscore',
    'uiLayout',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Amasty_Extrafee/js/model/fees',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Amasty_Extrafee/js/action/load-fees',
    'Amasty_Conditions/js/model/subscriber'
], function (
    _,
    layout,
    Component,
    quote,
    feesService,
    totalsDefaultProvider,
    loaderFees,
    subscriber
) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return Component.extend({
        defaults: {
            listens: {
                elems: 'updateTotals'
            },
            components: {
                dropdown: 'Amasty_Extrafee/js/fee/item/dropdown',
                checkbox: 'Amasty_Extrafee/js/fee/item/checkbox',
                radio: 'Amasty_Extrafee/js/fee/item'
            }
        },

        /**
         * @returns {Fieldset}
         */
        initialize: function () {
            this._super();

            if (!this.blockEnabled()) {
                return this;
            }

            subscriber.isLoading.subscribe(function (isLoading) {
                if (!isLoading) {
                    loaderFees.loadFees();
                }
            });
            feesService.fees.subscribe(this.updateFees.bind(this));

            return this;
        },

        /**
         * @returns {Boolean}
         */
        blockEnabled: function () {
            var blockType = 'enabledOn' + this.blockType,
                enabledTarget = checkoutConfig.amasty.extrafee[blockType];

            return enabledTarget;
        },

        /**
         * Esitamate totals of fees selected
         *
         * @param {Array} elems
         * @returns {void}
         */
        updateTotals: function (elems) {
            if (this.initChildCount === elems.length && this.hasFees()) {
                totalsDefaultProvider.estimateTotals(quote.shippingAddress());
            }
        },

        /**
         * Check that currently loaded fees has selection option
         *
         * @returns {boolean}
         */
        hasFees: function () {
            var fees = this.elems.filter(function (item) {
                return item.value() !== false && item.value() !== null;
            });

            return fees.length > 0;
        },

        /**
         * Update fees after collect totals
         *
         * @param {Object} fees
         * @returns {void}
         */
        updateFees: function (fees) {
            var names = {};

            _.each(fees, function (fee) {
                var name = this.name + '.fee.' + fee.id,
                    elem = this.findChildByName(name);

                if (!elem) {
                    layout([ {
                        parent: this.name,
                        name: 'fee.' + fee.id,
                        component: this.components[fee.frontend_type],
                        options: fee.base_options,
                        label: fee.name,
                        description: fee.description,
                        frontendType: fee.frontend_type,
                        feeId: fee.id,
                        value: fee.current_value
                    } ]);
                } else {
                    elem.options(fee.base_options);
                    elem.visible(true);
                }

                names[name] = 1;
            }.bind(this));

            this.removeUnmatchedFees(names);
        },

        /**
         * Find element by name
         *
         * @param {Object} name
         * @returns {void}
         */
        findChildByName: function (name) {
            return this.elems.findWhere({
                name: name
            });
        },

        /**
         * Remove fees elements
         *
         * @param {String} names
         * @returns {void}
         */
        removeUnmatchedFees: function (names) {
            this.elems.each(function (elem) {
                if (!names[elem.name]) {
                    elem.visible(false);
                }
            });
        }
    });
});
