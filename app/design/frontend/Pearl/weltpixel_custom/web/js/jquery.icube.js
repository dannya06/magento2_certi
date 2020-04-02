/*
    Author  : I.CUBE, inc.
    main jQuery widget of Fpmini
*/

define([
  'jquery',
  'jquery/ui',
  'matchMedia',
  'responsive',
  'mage/translate'
], function($, ui, mediaCheck, _responsive){
    'use strict';

    $.widget('icube.icube', {
        
        _create: function() {
            this.initAllPages();
            this.initHomePage();
            this.initCategoryPage();
            this.initProductPage();
            this.initSearchPage();
            this.initShoppingCartPage();
            this.initCmsPage();
            this.initCheckoutPage();
            this.initStoreLocator();
            this.initCustomerAccountLogin();
            this.initCustomerAccountPage();
            this.initAccountOrder();
        },

		initAllPages: function() {
            
            mediaCheck({
                media: '(min-width: 768px)',
                // Switch to Desktop Version
                entry: function () {
                    (function () {
                        $('.trigger.icon-search').click(function(){
                            $(this).addClass('hide');
                            $('.block-search .block-content').removeClass('hide');
                        });
                        
                    })();
                    
                },
                // Switch to Mobile Version
                exit: function () {
                    /* The function that toggles page elements from mobile to desktop mode is called here*/
                    (function () {
                        
                    })();
                }
            });

            
        },

        initHomePage: function() {
            
            if ($('body.cms-index-index').length) {
	            
	            
            }
            
        },

        initCategoryPage: function() {

            if ($('body.catalog-category-view').length) {
                
            }

            if ($('body.catalogsearch-result-index').length) {
            }

           
        },

        initProductPage: function() {

            if ($('body.catalog-product-view').length) {
                
                
                
            }
        },

        initSearchPage: function() {

            if ($('body.catalogsearch-result-index').length) {
            }
        },

        initCategorySearchPage: function() {

            if ($('body.catalog-category-view').length || $('body.catalogsearch-result-index').length) {
            }
        },

        initShoppingCartPage: function() {

            if ($('body.checkout-cart-index').length) {
                
            }
        },

        initCheckoutPage: function() {

            if ($('body.checkout-index-index').length) {  
               
            }

            if ($('body.onestepcheckout-index-index').length) {
            }
        },

        initRegisterPage: function() {

            if ($('body.customer-account-create').length) {
            }
        },
        
        initInvoicePage: function() {
            if ($('body.sales-order-invoice').length) {
            }
        },

        initCmsPage: function() {

            
        },
        initContactPage: function() {
            if ($('body.contact-index-index').length) {   
            }
        },
        initStoreLocator: function() {
            if ($('body.cms-store-locator').length) {
               
            }
        },
        initMpsellerbuyercommunicationCustomerView: function() {
            if ($('body.mpsellerbuyercommunication-customer-view').length) {
               
            }
        },
        initCustomerAccountLogin: function() {
            if ($('body.customer-account-login').length) {
               
            }
            
        },
        initCustomerAccountPage: function(){
            if( $('body.account').length ) {
            }

            if ($('body.account.customer-account-index').length ||
                $('body.account.sales-order-history').length ||
                $('body.account.mpsellerbuyercommunication-customer-history').length ||
                $('body.account.wishlist-index-index').length ||
                $('body.account.review-customer-index').length ||
                $('body.account.pdc-customerdesign-index').length ) {
                    
            }
        },
        initAccountOrder: function(){
            if( $('body.account.sales-order-view').length ) {
                    if ( $('.block').hasClass('block-gosend-details-view') ) {
                      $('.block.block-order-details-view').addClass('gosend');
                    };
            }
        }
    });

    return $.icube.main;

});