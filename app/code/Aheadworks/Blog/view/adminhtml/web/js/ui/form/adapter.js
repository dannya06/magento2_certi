define([
    'jquery',
    'underscore'
], function($, _) {
    'use strict';

    var buttons = {
        'reset': '#reset',
        'save': "#save",
        'schedule': "#schedule",
        'publish': "#publish",
        'saveAsDraft': "#save_as_draft",
        'saveAndContinue': '#save_and_continue'
    };

    function initListener(callback, action){
        var selector = buttons[action];
        var element = $(selector)[0];
        if (element) {
            if (element.onclick){
                element.onclick = null;
            }
            $(element).off().on('click', callback);
        }
    }

    return {
        on: function(handlers){
            _.each(handlers, initListener);
        }
    }
});