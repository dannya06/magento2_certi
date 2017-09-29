define([
    'ko',
    'Magento_Ui/js/form/element/abstract'
], function (ko, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            statusOptions: {},
            value: 'draft',
            template: 'Aheadworks_Blog/ui/form/element/label-status'
        },
        initObservable: function() {
            this._super();
            this.statusLabel = ko.computed(function() {
                return this.statusOptions[this.value()];
            }, this);
            return this;
        }
    });
});
