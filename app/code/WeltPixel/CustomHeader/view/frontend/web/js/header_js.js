var Header = {

	headerLinks: function () {
		var headerLinks_1         = jQuery('.header.panel >.header.links'),
			headerRightMiniCart   = jQuery('.header_right > .minicart-wrapper');


		if ((jQuery('body').hasClass('wp-device-l')) || jQuery('body').hasClass('wp-device-xl') ) {
			if (!headerLinks_1.hasClass('moved-header')) {
				headerLinks_1.clone().insertBefore(headerRightMiniCart);
				headerLinks_1.addClass('moved-header');
			}
		}
	},

	resizeActions: function () {
		this.headerLinks();
	},

	action: function () {
		this.resizeActions();
	}

};

require(['jquery'],
	function ($) {
		$(document).ready(function () {
			Header.action();
		});

		$(window).load(function () {
			Header.action();
		});

		var reinitTimer;
		$(window).on('resize', function () {
			clearTimeout(reinitTimer);
			reinitTimer = setTimeout(function() {Header.action();}, 100);
		});
	}
);