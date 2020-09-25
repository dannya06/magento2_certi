define([
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (uiRegistry, select) {
    'use strict';

    return select.extend({

        initialize: function () {
            this._super();
            this.onUpdate(this.value());
            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            var max_cashback = uiRegistry.get('index = max_cashback');
            if(value == 'cashback_fixed' || value == 'cashback_percent') {
                max_cashback.show();
            } else {
                max_cashback.hide();
            }

            return this._super();
        },
    });
});