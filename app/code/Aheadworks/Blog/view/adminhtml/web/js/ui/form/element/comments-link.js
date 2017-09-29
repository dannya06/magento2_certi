define([
    'ko',
    'Magento_Ui/js/form/element/abstract'
], function (ko, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            url: null,
            linkLabel: 'Go To Comments',
            template: 'Aheadworks_Blog/ui/form/element/comments-link'
        },
        initialize: function () {
            this._super()
                .initVisibility();
            return this;
        },
        initVisibility: function () {
            if (!this.url) {
                this.visible(false);
            }
            return this;
        }
    });
});
