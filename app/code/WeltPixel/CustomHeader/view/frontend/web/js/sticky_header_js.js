define(['jquery', 'domReady!'], function ($) {
    stickyHeader = {
        stickyHeader: function () {
            var config = {
                pageWrapper:        $('.page-wrapper'),
                headerSection:      $('.page-wrapper > .page-header'),
                headerContent:      $('.header.content'),
                headerLogo:         $('.header.content').find('.logo'),
                panelWrapper:       $('.panel.wrapper'),
                navSection:         $('.sections.nav-sections'),
                headerMultiStore:   $('.header-multistore'),
                switcherMultiStore: $('.multistore-switcher'),
                globalPromo:        $('.page-wrapper .page-header').find('.header-global-promo'),
                switcherCurrency:   $('.panel.wrapper').find('.switcher-currency'),
                greetWelcome:       $('.panel.wrapper').find('.greet.welcome'),
                headerPlaceholder:  '<div class="header-placeholder"></div>',
                stickyMobile:       window.stickyMobileEnabled,
                design:             $('.nav-toggle').is(':visible') ? 'mobile' : 'desktop',
                headerElHeight:     0
            };

            /** abort if header-content was not found */
            if (config.headerContent.length == 0) {
                return;
            }

            var that = this;

            /** insert header-placeholder and move header elements */
            config.pageWrapper.prepend(config.headerPlaceholder);
            config.headerPlaceholder = $('.header-placeholder');

            if (that.getHeaderVersion(config.headerSection) != 'v3') {
                that.appendElements(config.headerSection, config.navSection, config);
            } else {
                that.appendElements(config.headerSection, null, config);
                config.headerContent.find('.compare.wrapper').after(config.navSection);
            }

            /** adjust header-placeholder height if global-promo-message is active */
            var checkHeight = setInterval(function() {
                if (config.globalPromo.height() && checkHeight < 3000) {
                    var globalPromoHeight = config.globalPromo.find('.global-notification-wrapper').height() + 10;
                    config.headerElHeight += globalPromoHeight;
                    config.headerPlaceholder.css('height', config.headerElHeight + 'px');

                    clearInterval(checkHeight);
                } else {
                    clearInterval(checkHeight);
                }
            }, 100);
            $('.close-global-notification').on('click', function() {
                var globalPromoHeight = config.globalPromo.find('.global-notification-wrapper').height() + 10;
                if (globalPromoHeight) {
                    config.headerElHeight -= globalPromoHeight;
                    config.headerPlaceholder.css('height', config.headerElHeight + 'px');
                }
            });

            $(window).on('scroll resize', function () {
                /** if design has changed force reset settings */
                var oldDesign = config.design;
                config.design = $('.nav-toggle').is(':visible') ? 'mobile' : 'desktop';
                if (oldDesign != config.design) {
                    that.resetSettings(that, config);
                }

                if (config.design == 'desktop') {
                    config.headerSection.removeClass('sticky-header-mobile');
                    switch (that.getHeaderVersion(config.headerSection))
                    {
                        case 'v1':
                            if (that.doSticky(config)) {
                                if (that.notStickyYet(config)) {
                                    that.moveElementsOnSticky(config.headerSection, config.navSection, 'out', config);
                                    config.headerLogo.after(config.navSection);
                                    that.showHideElements('hide', [
                                        config.globalPromo,
                                        config.headerMultiStore,
                                        config.switcherMultiStore,
                                        config.panelWrapper
                                    ]);
                                }
                            } else {
                                that.moveElementsOnSticky(config.headerSection, config.navSection, 'in', config);
                                config.headerSection.after(config.navSection);
                                that.showHideElements('show', [
                                    config.globalPromo,
                                    config.headerMultiStore,
                                    config.switcherMultiStore,
                                    config.panelWrapper
                                ]);
                            }
                            break;
                        case 'v2':
                            if (that.doSticky(config)) {
                                if (that.notStickyYet(config)) {
                                    that.moveElementsOnSticky(config.headerSection, config.navSection, 'out', config);
                                    config.headerLogo.after(config.navSection);
                                    that.showHideElements('hide', [
                                        config.globalPromo,
                                        config.headerMultiStore,
                                        config.switcherMultiStore
                                    ]);
                                }
                            } else {
                                that.moveElementsOnSticky(config.headerSection, config.navSection, 'in', config);
                                config.headerSection.after(config.navSection);
                                that.showHideElements('show', [
                                    config.globalPromo,
                                    config.headerMultiStore,
                                    config.switcherMultiStore
                                ]);
                            }
                            break;
                        case 'v3':
                            if (that.doSticky(config)) {
                                if (that.notStickyYet(config)) {
                                    that.moveElementsOnSticky(config.headerSection, null, 'out', config);
                                    that.showHideElements('hide', [
                                        config.globalPromo,
                                        config.headerMultiStore,
                                        config.switcherMultiStore,
                                        config.panelWrapper
                                    ]);
                                }
                            } else {
                                that.moveElementsOnSticky(config.headerSection, null, 'in', config);
                                that.showHideElements('show', [
                                    config.globalPromo,
                                    config.headerMultiStore,
                                    config.switcherMultiStore,
                                    config.panelWrapper
                                ]);
                            }
                            break;
                        case 'v4':
                            var panelWrapperHeight = $('.panel.wrapper').height();
                            if ($(window).scrollTop() > panelWrapperHeight) {
                                if (that.notStickyYet(config)) {
                                    that.moveElementsOnSticky(config.headerSection, config.navSection, 'out', config);
                                    config.navSection.addClass('sticky-header');
                                    that.showHideElements('hide', [
                                        config.globalPromo,
                                        config.greetWelcome,
                                        config.switcherCurrency,
                                        config.headerMultiStore,
                                        config.switcherMultiStore
                                    ]);
                                }
                            } else {
                                that.moveElementsOnSticky(config.headerSection, config.navSection, 'in', config);
                                config.navSection.removeClass('sticky-header');
                                that.showHideElements('show', [
                                    config.globalPromo,
                                    config.greetWelcome,
                                    config.switcherCurrency,
                                    config.headerMultiStore,
                                    config.switcherMultiStore
                                ]);
                            }
                            break;
                        default:
                            // nothing to do here
                            break;
                    }
                    that.fixFullWidthMenus(config);
                } else {
                    config.headerSection.removeClass('sticky-header');
                    config.navSection.removeClass('sticky-header sticky-header-nav');

                    if (that.getHeaderVersion(config.headerSection) != 'v3')
                        config.headerSection.after(config.navSection);

                    if (config.stickyMobile == 1) {
                        if (that.doSticky(config)) {
                            config.headerSection.addClass('sticky-header-mobile');

                            if (that.getHeaderVersion(config.headerSection) != 'v4') {
                                that.showHideElements('hide', [
                                    config.panelWrapper
                                ]);
                            }

                            that.showHideElements('hide', [
                                config.globalPromo,
                                config.headerMultiStore
                            ]);
                        } else {
                            config.headerSection.removeClass('sticky-header-mobile');
                            that.showHideElements('show', [
                                config.globalPromo,
                                config.headerMultiStore,
                                config.panelWrapper
                            ]);
                        }
                    }
                }
            });
        },
        resetSettings: function (that, config) {
            config.headerElHeight = 0;
            if (that.getHeaderVersion(config.headerSection) != 'v3') {
                that.appendElements(config.headerSection, config.navSection, config);
            } else {
                that.appendElements(config.headerSection, null, config);
                config.headerContent.find('.compare.wrapper').after(config.navSection);
            }
        },
        appendElements: function (a, b, config) {
            if (a) {
                a.appendTo(config.headerPlaceholder);
                if (a.length && a.is(':visible'))
                    config.headerElHeight += a.outerHeight();
            }
            if (b) {
                b.appendTo(config.headerPlaceholder);
                if (config.design != 'mobile' && b.length && b.is(':visible'))
                    config.headerElHeight += b.outerHeight();
            }
            config.headerPlaceholder.css('height', config.headerElHeight + 'px');
        },
        notStickyYet: function (config) {
            return !config.headerSection.hasClass('sticky-header');
        },
        doSticky: function (config) {
            return $(window).scrollTop() > config.headerContent.position().top;
        },
        moveElementsOnSticky: function (a, b, direction, config) {
            if (direction == 'out') {
                if (b) {
                    b.prependTo($('.page-wrapper')).before(config.headerPlaceholder);
                    b.addClass('sticky-header-nav');
                }
                if (a) {
                    a.prependTo($('.page-wrapper')).before(config.headerPlaceholder);
                    a.addClass('sticky-header');
                }
            } else {
                if (a) {
                    a.appendTo(config.headerPlaceholder);
                    a.removeClass('sticky-header');
                }
                if (b) {
                    b.appendTo(config.headerPlaceholder);
                    b.removeClass('sticky-header-nav');
                }
            }
        },
        showHideElements: function (action, els) {
            for (var i = 0; i < els.length; i++) {
                if (action == 'show') {
                    els[i].slideDown('fast');
                } else {
                    els[i].hide();
                }
            }
        },
        getHeaderVersion: function (headerSection) {
            if (headerSection.hasClass('page-header-v1')) {
                return 'v1';
            } else if (headerSection.hasClass('page-header-v2')) {
                return 'v2';
            } else if (headerSection.hasClass('page-header-v3')) {
                return 'v3';
            } else if (headerSection.hasClass('page-header-v4')) {
                return 'v4';
            }
        },
        fixFullWidthMenus: function (config) {
            var headerW = parseInt(config.headerContent.width());
            config.navSection.find('.level0.submenu.fullwidth').each(function() {
                $(this).css('width', headerW + 'px');
            });
        }
    };
    return stickyHeader;
});
