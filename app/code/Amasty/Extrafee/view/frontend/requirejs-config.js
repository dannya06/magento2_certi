var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/resource-url-manager': {
                'Amasty_Extrafee/js/model/resource-url-manager-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Amasty_Extrafee/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/view/payment/default': {
                'Amasty_Extrafee/js/view/payment/default-mixin': true
            }
        }
    }
};
