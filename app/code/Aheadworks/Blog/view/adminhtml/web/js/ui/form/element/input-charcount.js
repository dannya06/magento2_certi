define([
    'ko',
    'Magento_Ui/js/form/element/abstract'
], function (ko, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            warningLevel: 60,
            hintText: 'characters used. Recommended max length is 50-60 characters',
            template: 'Aheadworks_Blog/ui/form/element/input-charcount'
        },
        initObservable: function() {
            this._super();
            this.charCount = ko.computed(function() {
                var value = this.value();
                return value ? value.length : 0;
            }, this);
            this.hint = ko.computed(function() {
                return this.charCount() + " " + this.hintText;
            }, this);
            this.warning = ko.computed(function() {
                return this.charCount() > this.warningLevel;
            }, this);
            return this;
        },
        onKeyUp: function(data, event) {
            this.value(event.target.value);
        }
    });
});
