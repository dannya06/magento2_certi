/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (Component) {
    'use strict';

    return Component.extend({
        /**
         * Show element
         *
         * @returns {Object} Chainable
         */
        show: function () {
            this.visible(true);

            return this;
        },

        /**
         * Hide element
         *
         * @returns {Object} Chainable
         */
        hide: function () {
            this.visible(false);

            return this;
        }
    });
});
