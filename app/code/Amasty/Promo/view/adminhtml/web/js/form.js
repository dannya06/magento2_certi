define([
    'jquery',
    'uiRegistry'
], function ($, registry) {
    var fieldset_prefix = 'sales_rule_form.sales_rule_form.actions.';

    var ampromoForm = {
        update: function () {
            this.resetFields();

            var actionFieldset = $('#sales_rule_formrule_actions_fieldset_').parent();

            window.amPromoHide = 0;
            actionFieldset.show();
            if (typeof window.amRulesHide !="undefined" && window.amRulesHide == 1) {
                actionFieldset.hide();
            }
            

            var action = $('[data-index="simple_action"] select').val();

            if (action.match(/^ampromo/)) {
                this.hideFields(['simple_free_shipping', 'apply_to_shipping']);
            }
            this.hideBannersTab();
            switch (action) {
                case 'ampromo_cart':
                    actionFieldset.hide();
                    window.amPromoHide = 1;

                    this.hideFields(['discount_qty', 'discount_step']);
                    this.showFields(['ampromorule[sku]', 'ampromorule[type]']);
                    break;
                case 'ampromo_items':
                    this.showFields(['ampromorule[sku]', 'ampromorule[type]']);
                    this.showBannersTab();
                    break;
                case 'ampromo_product':
                    this.showBannersTab();
                    break;
                case 'ampromo_spent':
                    actionFieldset.hide();
                    window.amPromoHide = 1;

                    this.showFields(['ampromorule[sku]', 'ampromorule[type]']);
                    break;
            }
        },
        showBannersTab: function(){
            jQuery('[data-index=ampromorule_top_banner]').show();
            jQuery('[data-index=ampromorule_after_product_banner]').show();
        },
        hideBannersTab: function(){
            jQuery('[data-index=ampromorule_top_banner]').hide();
            jQuery('[data-index=ampromorule_after_product_banner]').hide();
        },
        resetFields: function () {
            this.showFields([
                'discount_qty', 'discount_step', 'apply_to_shipping', 'simple_free_shipping'
            ]);
            this.hideFields(['ampromorule[sku]', 'ampromorule[type]']);
        },

        hideFields: function (names) {
            return this.toggleFields('hide', names);
        },

        showFields: function (names) {
            return this.toggleFields('show', names);
        },

        addPrefix: function (names) {
            for (var i = 0; i < names.length; i++) {
                names[i] = fieldset_prefix + names[i];
            }

            return names;
        },

        toggleFields: function (method, names) {
            registry.get(this.addPrefix(names), function () {
                for (var i = 0; i < arguments.length; i++) {
                    arguments[i][method]();
                }
            });
        }
    };

    return ampromoForm;
});
