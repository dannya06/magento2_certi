define([
    'Magento_Ui/js/form/form',
    'Amasty_Extrafee/js/model/fees'
], function (Component, feesService) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return Component.extend({
        isLoading: feesService.isLoading,
        defaults: {
            template: 'Amasty_Extrafee/fee/block',
            modules: {
                fieldset: '${ $.name }.amasty-extrafee-fieldsets'
            }
        },

        /**
         * @returns {Boolean}
         */
        visible: function () {
            var elements,
                param = 'enabledOn' + this.blockType,
                enabledTarget = checkoutConfig.amasty.extrafee[param];

            if (!this.fieldset()) {
                return false;
            }

            elements = this.fieldset().elems.filter('visible');

            return enabledTarget && elements.length > 0;
        }
    });
});
