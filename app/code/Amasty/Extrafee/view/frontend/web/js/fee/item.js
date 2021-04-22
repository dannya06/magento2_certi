define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/quote',
    'Amasty_Extrafee/js/action/select-fee',
    'Magento_Ui/js/lib/validation/validator',
    'Amasty_Extrafee/js/model/tax-utils'
], function ($, AbstractField, priceUtils, quote, selectFeeAction, validator, taxUtils) {
    'use strict';

    return AbstractField.extend({
        defaults: {
            template: 'Amasty_Extrafee/fee/item',
            templatesChildComponents: {
                radio: 'Amasty_Extrafee/fee/item/radio',
                checkbox: 'Amasty_Extrafee/fee/item/checkbox',
                dropdown: 'Amasty_Extrafee/fee/item/dropdown'
            },
            listens: {
                value: 'setFee'
            },
            frontendType: 'dropdown',
            feeId: null,
            options: [],
            value: []
        },
        translation: {
            error: $.mage.__('Please select at least one option for %1.')
        },
        taxUtils: taxUtils,

        /**
         * @returns {Item} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
                    'options'
                ]);

            return this;
        },

        /**
         * @returns {Object} Validate information.
         */
        validate: function () {
            var value = this.value(),
                result = validator(this.validation, value, this.validationParams),
                message = '',
                isValid = this.disabled() || !this.visible() || result.passed;

            if (this.required() && !value) {
                isValid = false;
            }

            if (!isValid) {
                message = this.translation.error.replace('%1', this.label);
            }

            this.error(message);
            this.error.valueHasMutated();
            this.bubble('error', message);

            if (this.source && !isValid) {
                this.source.set('params.invalid', true);
            }

            return {
                valid: isValid,
                target: this
            };
        },

        /**
         * @param {String|Array} optionId
         * @returns {void}
         */
        setFee: function (optionId) {
            var optionsIds = Array.isArray(optionId) ? optionId : [ optionId ];

            selectFeeAction.selectFee(this.feeId, optionsIds);
        },

        /**
         * @returns {Item} Chainable.
         */
        initConfig: function () {
            this._super();

            if (Object.keys(this.templatesChildComponents).indexOf(this.frontendType) !== -1) {
                this.elementTmpl = this.templatesChildComponents[this.frontendType];
            }

            return this;
        },

        /**
         * @param {Object} item
         * @returns {string}
         */
        optionsText: function (item) {
            return item.label + ' ' + taxUtils.getPrice(item);
        },

        /**
         * @param {Object} item
         * @returns {String}
         */
        optionsValue: function (item) {
            return item.index + '';
        },

        /**
         * @param {Number} price
         * @returns {String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },

        /**
         * @returns {void}
         */
        onUpdate: function () {
            this.bubble('update', this.hasChanged());

            if (this.error()) {
                this.validate();
            }
        },

        /**
         * @returns {Array}
         */
        getOptions: function () {
            var self = this;

            if (typeof self.options.first() !== 'undefined' && typeof self.options.first() === 'string') {
                self.options.each(function (value, i) {
                    if (typeof self.options()[i] === 'string') {
                        self.options()[i] = JSON.parse(value);
                    }
                });
            }

            return this.options;
        }
    });
});
