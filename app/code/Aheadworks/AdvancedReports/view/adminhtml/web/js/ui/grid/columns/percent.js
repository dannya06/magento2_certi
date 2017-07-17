/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'mage/utils/strings',
    'Aheadworks_AdvancedReports/js/ui/grid/columns/number'
], function (stringUtils, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_AdvancedReports/ui/grid/cells/percent'
        },

        /**
         * Meant to preprocess data associated with a current columns' field
         *
         * @param {Object} row
         * @param {String} index
         * @returns {String}
         */
        getLabel: function (row, index) {
            var number = this._super(row, index);

            return String(number) + '%';
        },

        /**
         * Return not formatted value of a current columns' field
         *
         * @param {Object} row
         * @returns {String}
         */
        getValue: function (row) {
            var number = row[this.index];
            if (stringUtils.isEmpty(number)) {
                number = '0';
            }
            return number;
        },

        /**
         * Retrieve width for percent bar
         *
         * @returns {Integer}
         */
        getPercentBarWidth: function (row) {
            var width = Math.round(this.getValue(row) / 2);

            if (width > 50) {
                width = 50;
            }
            return width;
        }
    });
});