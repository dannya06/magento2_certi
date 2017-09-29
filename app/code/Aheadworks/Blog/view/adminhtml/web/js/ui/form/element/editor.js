define([
    'ko',
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'mage/translate',
    'mage/adminhtml/events',
    'mage/adminhtml/wysiwyg/tiny_mce/setup',
    'mage/adminhtml/wysiwyg/widget'
], function (ko, $, Abstract) {
    'use strict';

    ko.bindingHandlers.awBlogEditor = {
        init: function (element, valueAccessor, allBindings, viewModel) {
            if (valueAccessor() && viewModel.wysiwygConfig.enabled) {
                var wysiwyg = new tinyMceWysiwygSetup(element.id, viewModel.wysiwygConfig);
                wysiwyg.setup('exact');
            }
        }
    };

    return Abstract.extend({
        defaults: {
            cols: 15,
            rows: 2,
            style: '',
            template: 'Aheadworks_Blog/ui/form/element/editor',
            wysiwygConfig: {
                enabled: true
            }
        }
    });
});
