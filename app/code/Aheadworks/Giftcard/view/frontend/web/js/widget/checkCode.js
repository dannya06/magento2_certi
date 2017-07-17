/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery'
], function($){
    'use strict';

    $.widget('mage.awGiftCardCheckCode', {
        options: {
            resultSelector: '#aw_giftcard__code_info',
            url: '',
            formSelector: ''
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this.element.on('click', $.proxy(this.onClick, this));
        },

        /**
         * Click on element
         * @private
         */
        onClick: function() {
            var self = this,
                data = '';

            $(this.options.resultSelector).html('');
            if (this.options.formSelector) {
                if (!this.validate()) {
                    return;
                }
                data = $(this.options.formSelector).serializeArray()
            }

            $.ajax({
                url: this.options.url,
                data: data,
                method: 'post',
                context: this,
                showLoader: true
            }).success(function(response) {
                if (response) {
                    $(self.options.resultSelector).html(response);
                }
            });
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            return $(this.options.formSelector).validation() && $(this.options.formSelector).validation('isValid');
        }
    });

    return $.mage.awGiftCardCheckCode;
});
