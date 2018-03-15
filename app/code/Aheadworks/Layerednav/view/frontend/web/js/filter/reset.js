/**
* Copyright 2018 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    './../url',
    './../updater',
    './request/bridge',
], function ($, url, updater, requestBridge) {
    'use strict';

    $.widget('mage.awLayeredNavFilterReset', {
        options: {
            params: [],
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function () {
            this._on({
                /**
                 * Calls callback when event is triggered
                 * @param {Event} event
                 */
                'click': function (event) {
                    event.stopPropagation();
                    var resetUrl = url.getResetUrl(this.options.params);

                    requestBridge.submit(resetUrl).then(
                        /**
                         * Called after request finishes
                         */
                        function () {
                            updater.updateAndScrollUpToTop(resetUrl, requestBridge.getResult());
                        }
                    );
                },
            });
        },
    });

    return $.mage.awLayeredNavFilterReset;
});
