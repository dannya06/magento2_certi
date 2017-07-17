/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'mage/utils/strings',
    'Magento_Ui/js/grid/columns/column'
], function (stringUtils, Column) {
    'use strict';

    return Column.extend({
        /**
         * Meant to preprocess data associated with a current columns' field
         *
         * @param {Object} row
         * @param {String} index
         * @returns {String}
         */
        getLabel: function (row, index) {
            if (typeof index != 'undefined') {
                var number = row[index];
            } else {
                var number = this._super(row);
            }

            if (stringUtils.isEmpty(number)) {
                return '0';
            } else if (Math.floor(number) == number) {
                return String(number * 1);
            }

            return String(Number(number * 1).toFixed(2));
        }
    });
});
