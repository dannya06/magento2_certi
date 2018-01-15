/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Aheadworks_Rma/ui/form/element/label_url'
        },

        /**
         * Retrieve label for field
         *
         * @returns {String}
         */
        getLabel: function() {
            return this.source.get(this.dataScope + '_label');
        },

        /**
         * Retrieve url for field
         *
         * @returns {String}
         */
        getUrl: function() {
            return this.source.get(this.dataScope + '_url');
        },

        /**
         * Retrieve data after element
         *
         * @returns {String}
         */
        getAfter: function() {
            return this.source.get(this.dataScope + '_after');
        }
    });
});
