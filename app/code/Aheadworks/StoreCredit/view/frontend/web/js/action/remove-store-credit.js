/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/*global define,alert*/
define(
    [
        'jquery',
        'Aheadworks_StoreCredit/js/model/resource-url-manager',
        'Aheadworks_StoreCredit/js/model/payment/store-credit-messages',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/get-payment-information',
        'mage/storage',
        'mage/translate'
    ],
    function (
            $, 
            urlManager,
            messageContainer,
            quote, 
            totals,
            errorProcessor,
            getPaymentInformationAction, 
            storage, 
            $t
    ) {
        'use strict';
        return function (isApplied, isLoading) {
            var quoteId = quote.getQuoteId(),
                url = urlManager.getRemoveStoreCreditUrl(quoteId), 
                message = $t('Store Credit were successfully removed.');
            
            messageContainer.clear();

            return storage.delete(
                url,
                false
            ).done(
                function () {
                    var deferred = $.Deferred();
                    totals.isLoading(true);
                    getPaymentInformationAction(deferred);
                    $.when(deferred).done(function () {
                        isApplied();
                        totals.isLoading(false);
                    });
                    messageContainer.addSuccessMessage({
                        'message': message
                    });
                }
            ).fail(
                function (response) {
                    totals.isLoading(false);
                    errorProcessor.process(response, messageContainer);
                }
            ).always(
                function () {
                    isLoading(false);
                }
            );
        };
    }
);
