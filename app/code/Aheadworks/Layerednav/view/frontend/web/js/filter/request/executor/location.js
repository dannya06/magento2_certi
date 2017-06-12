define([
    'jquery',
    './../executor'
], function ($, Executor) {
    'use strict';

    return $.extend(Executor, {
        /**
         * Submit request
         *
         * @param {String} url
         * @returns {Object}
         */
        submit: function (url) {
            window.location.replace(url);
            return $.Deferred().resolve();
        },

        /**
         * Get request result
         *
         * @returns {Object|null}
         */
        getResult: function () {
            return null;
        }
    });
});
