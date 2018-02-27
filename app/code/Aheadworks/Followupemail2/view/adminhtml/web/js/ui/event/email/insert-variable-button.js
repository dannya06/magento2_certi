/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/form/components/button',
    'Magento_Variable/variables'
], function (Button) {
    'use strict';

    return Button.extend({
        defaults: {
            contentSelector: null,
        },

        /**
         * Open insert variable window
         */
        insertVariable: function () {
            if (this.source.data.variables) {
                Variables.resetData();
                Variables.init(this.contentSelector);
                Variables.openVariableChooser(this.source.data.variables);
            }
        },

        /**
         * Hide element
         *
         * @returns {Abstract} Chainable
         */
        hide: function () {
            this.visible(false);

            return this;
        },

        /**
         * Show element
         *
         * @returns {Abstract} Chainable
         */
        show: function () {
            this.visible(true);

            return this;
        },
    });
});
