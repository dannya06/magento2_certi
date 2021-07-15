/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Icube_Logistix/js/model/shipping-rates-validator',
        'Icube_Logistix/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        logistixShippingRatesValidator,
        logistixShippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('logistix', logistixShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('logistix', logistixShippingRatesValidationRules);
        return Component;
    }
);