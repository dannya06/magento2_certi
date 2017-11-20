define([
    "jquery",
    "amasty_slick",
    "jquery/ui",
    "uiRegistry"
], function ($) {

    $.widget('mage.ampromoPopup', {
        options: {
            autoOpen: false,
            slickSettings: {},
            sourceUrl: '',
            uenc: ''
        },

        isSlickInitialized: false,

        _create: function () {
            $(this.element).mousedown($.proxy(function (event) {
                if ($(event.target).data('role') == 'ampromo-overlay') {
                    event.stopPropagation();
                    this.hide();
                }
            }, this));

            $('[data-role=ampromo-popup-hide]').click($.proxy(this.hide, this));

            if (this.options.autoOpen) {
                this.show();
            }

            var widget = this;
            $(document).on('customer-data-reload', function (event, sections) {
                var inArray = $.inArray('cart', sections);
                if (inArray !== -1) {
                        widget.reload();
                    }
                }
            );
        },

        hide: function () {
            $(this.element).fadeOut();
        },

        show: function () {
            if (!this.isSlickInitialized) {
                this.init();
            }

            $(this.element).fadeIn();
        },

        init: function () {
            // Hack for "slick" library
            $(this.element).show();
            $('[data-role=ampromo-gallery]').slick(this.options.slickSettings);
            $(this.element).hide();

            this.isSlickInitialized = true;

            $('.ampromo_items_form').mage('validation');
        },
        
        reload: function () {
            this.isSlickInitialized = false;

            var widget = this;

            $.ajax({
                url: this.options.sourceUrl,
                method: 'GET',
                data: {uenc: this.options.uenc},
                success: function (response) {
                    $('[data-role="ampromo-items-container"]').html(response);
                    widget.init();

                    var itemsCount = +widget.element.find('[data-role="ampromo-gallery"]').data('count');
                    var event = new $.Event('reloaded');
                    widget.element.trigger(event, [itemsCount]);
                }
            });
        }
    });

    return $.mage.ampromoPopup;
});
