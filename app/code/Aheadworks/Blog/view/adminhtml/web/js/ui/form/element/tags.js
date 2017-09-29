define([
    'ko',
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'jquerytokenize'
], function (ko, $, Abstract) {
    'use strict';

    function updateValue(viewModel, value) {
        viewModel.value(value);
    }

    ko.bindingHandlers.awBlogTags = {
        init: function (element, valueAccessor, allBindings, viewModel) {
            if (valueAccessor()) {
                $('#' + element.id).tokenize({
                    onAddToken: function (value, text, tokenize) {
                        updateValue(viewModel, tokenize.select.val());
                    },
                    onRemoveToken: function (value, tokenize) {
                        updateValue(viewModel, tokenize.select.val());
                    }
                });
            }
        }
    };

    return Abstract.extend({
        defaults: {
            template: 'Aheadworks_Blog/ui/form/element/tags'
        }
    });
});
