define(['jquery'], function ($) {
    'use strict';

    var rangeSliderMixin = {
        options: {
            fromLabel      : '[data-role=from-label]',
            toLabel        : '[data-role=to-label]',
            sliderBar      : '[data-role=slider-bar]',
            message        : '[data-role=message-box]',
            applyButton    : '[data-role=apply-range]',
            rate           : 1.0000,
            maxLabelOffset : 0,
            messageTemplates : {
                "displayOne": '<span class="msg">1 item</span>',
                "displayCount": '<span class="msg"><%- count %> items</span>',
                "displayEmpty": '<span class="msg-error">No items in the current range.</span>'
            },
        }
    };

    return function (targetWidget) {
        // Example how to extend a widget by mixin object
        $.widget('smileEs.rangeSlider', targetWidget, rangeSliderMixin); // the widget alias should be like for the target widget

        return $.smileEs.rangeSlider; //  the widget by parent alias should be returned
    };
});