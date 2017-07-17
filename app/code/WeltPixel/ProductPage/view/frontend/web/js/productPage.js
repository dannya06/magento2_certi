var productPage = {
	init: function () {
	},

	load: function () {
		this.action();
		this.mageSticky();
	},

	ajaxComplete: function () {
		this.mageSticky();
		// this.swatchOptionMageSticky();
	},

	resize: function () {
		this.action();
	},

	mageSticky: function () {
		jQuery('.product-info-main.product_v2.cart-summary').mage('sticky', {
			container: '.product-top-main.product_v2'
		});
		jQuery('.product-info-main.product_v4.cart-summary').mage('sticky', {
			container: '.product-top-main.product_v4'
		});
	},

	action: function () {
		var media = jQuery('.product.media.product_v2'),
			media_v4 = jQuery('.product.media.product_v4'),
			swipeOff = jQuery('.swipe_desktop_off #swipeOff');

		if(jQuery(window).width() > 768) {
			media.addClass('v2');
			media_v4.addClass('v4');
		} else {
			media.removeClass('v2');
			media_v4.removeClass('v4');
		}

		if(jQuery(window).width() > 1024) {
			swipeOff.addClass('active');
		} else {
			swipeOff.removeClass('active');
		}
	}
};

require(['jquery', 'productPage', 'mage/mage', 'mage/ie-class-fixer', 'mage/gallery/gallery'],
	function ($) {
		$(document).ready(function () {
			productPage.init();
		});

		$(window).load(function () {
			productPage.load();
		});

		$(document).ajaxComplete(function () {
			productPage.ajaxComplete();
		});

		var reinitTimer;
		$(window).on('resize', function () {
			clearTimeout(reinitTimer);
			reinitTimer = setTimeout(productPage.resize(), 300);
		});
	}
);