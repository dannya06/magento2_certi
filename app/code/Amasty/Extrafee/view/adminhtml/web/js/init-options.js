/**
 * Init Options Panel
 */
define([
    'jquery',
    'uiRegistry'
], function ($, registry) {
    'use strict';

    var panelId = 'manage-options-panel',
        initOptions = {
            selectors: {
                editForm: '#edit_form',
                optionCount: '#option-count-check',
                validationInput: '[data-amxexfee-js="required-options"]',
                priceTypesSelect: '[data-item-select-id="%s"]',
                optionsContainer: '[data-amexfee-js="options-container"]',
                options: '[data-amexfee-js="options"]',
                priceTypes: '[data-amexfee-js="price-types"]'
            },

            /**
             * Init options for fee item
             *
             * @return {void}
             */
            initialize: function () {
                this.initSelectors();
                this.renderOptions();
                this.initEventHandlers();
            },

            /**
             * @return {void}
             */
            initSelectors: function () {
                this.optionsContainer = $(this.selectors.optionsContainer);
                this.editForm = $(this.selectors.editForm);
                this.validationInput = $(this.selectors.validationInput);
                this.optionCount = $(this.selectors.optionCount);
            },

            /**
             * @return {void}
             */
            renderOptions: function () {
                var self = this;

                this.optionsContainer.trigger('render');
                $(self.selectors.priceTypes).each(function () {
                    var input = $(this),
                        id = input.data('itemId'),
                        selectorSelectElement = self.selectors.priceTypesSelect.replace('%s', id);

                    $(selectorSelectElement).val(input.val());
                });
                this.optionsContainer.sortable({
                    cancel: 'select, input, button'
                });
            },

            /**
             * @return {void}
             */
            initEventHandlers: function () {
                this.editForm.on('beforeSubmit', this.validate.bind(this));
            },

            /**
             * @return {void}
             */
            validate: function () {
                this.validationInput.val(this.isValid());
            },

            /**
             * @return {String}
             */
            isValid: function () {
                return this.optionCount.val();
            }
        };

    registry.get(panelId, initOptions.initialize.bind(initOptions));

    return initOptions;
});
