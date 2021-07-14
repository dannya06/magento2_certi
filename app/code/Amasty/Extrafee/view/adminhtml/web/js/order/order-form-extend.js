/* global productConfigure, order  */

/**
 * Init Order form
 *
 * @returns {Function}
 */
define([
    'jquery',
    'Amasty_Extrafee/js/order/create/extrafee',
    'domReady!'
], function ($, amExtraFee) {
    'use strict';

    var applyFeesSelector = '[data-amexfee-js="apply-fees"]';

    return function (config) {
        var amExtraFeeAction = amExtraFee.initFeeOrderCreate();

        order.sidebarHide();
        $(document).on('click', applyFeesSelector, amExtraFeeAction.updateExtraFee.bind(amExtraFeeAction));

        if (productConfigure) {
            productConfigure.addListType('product_to_add', {
                urlFetch: config.configureProductToAdd
            });
            productConfigure.addListType('quote_items', {
                urlFetch: config.configureQuoteItems
            });
        }
    };
});
