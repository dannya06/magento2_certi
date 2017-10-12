var FullPageScroll = {
	action: function (countBlocks) {
		var pageHeader = jQuery('.page-header'),
			header = jQuery('header'),
			headerCnt = jQuery('header .header.content'),
			headerPanel = jQuery('header .panel.header'),
			headerH = '',
			search = jQuery('header .block-search'),
			footer = jQuery('footer'),
			nav = jQuery('.nav-sections'),
			breadcrumbs = jQuery('.breadcrumbs'),
			headerOH = header.outerHeight(), // this var is uses in default module js
			headerCntOH = headerCnt.outerHeight(),
			headerPanelOH = headerPanel.outerHeight(),
			searchH = '',
			footerOH = footer.outerHeight(),
			navOH = nav.outerHeight(),
			breadcrumbsOH = breadcrumbs.outerHeight(),
			body = jQuery('body'),
			ww = jQuery(window).width(),
			multiStore = jQuery('.header-multistore .multistore-desktop'),
			globalPromo = jQuery('.header-global-promo'),
			round = 0;

		header.addClass('fps active');
		body.addClass('fullpagescroll');

		jQuery(window).resize(function(){
			ww = jQuery(window).width();
			headerOH = header.outerHeight();
			headerCntOH = headerCnt.outerHeight() + 4;
			headerPanelOH = headerPanel.outerHeight();
			footerOH = footer.outerHeight();
			navOH = nav.outerHeight();
			breadcrumbsOH = breadcrumbs.outerHeight();
			multiStoreOH = multiStore.outerHeight();
			globalPromoOH = globalPromo.outerHeight();


            if (pageHeader.hasClass('page-header-v1')) {
                headerH = headerPanelOH + headerCntOH;
            } else if (pageHeader.hasClass('page-header-v2')) {
                round = 0;
                headerH = headerCntOH - round,
                    searchH = search.outerHeight();
            } else if (pageHeader.hasClass('page-header-v3')) {
                round = 10;
				console.log(navOH);
                headerH = headerPanelOH + headerCntOH - navOH + round;
            } else if (pageHeader.hasClass('page-header-v4')) {
                round = 0; //8
                headerH = headerPanelOH + headerCntOH - round;
            }

			headerH = headerH + multiStoreOH + globalPromoOH;

            footer.css('margin-bottom', -footerOH);
            breadcrumbs.css('top', headerH + navOH).addClass('fps active');

			if(ww > 767 && header.hasClass('active')){
				if (pageHeader.hasClass('page-header-v3')) {
					nav.css('top', 0).addClass('fps');
				} else {
					nav.css('top', headerH).addClass('fps');
				}
			} else {
				nav.css('top', 0).removeClass('fps');
			}
		});

		jQuery(document).ready(function() {
			setTimeout(function(){ jQuery(window).trigger('resize'); }, 1000);
		});

		jQuery('#fullpage').fullpage({
			verticalCentered: true,
			onLeave: function (index, nextIndex, direction) {
				if (
					index == 1 &&
					nextIndex == 2 &&
					direction == 'down'
				) {
					if (ww > 767) {
						jQuery('.fps').removeClass('active');
						nav.css('top', 0);
						if(headerH != ''){
							header.css('margin-top', -headerH);
							//nav.css('margin-top', -(headerH + navOH));
							breadcrumbs.css('margin-top', -(headerH + navOH + breadcrumbsOH + 20));
						}
						if(searchH != ''){
							search.css('margin-top', -searchH);
						}
					} else {
						jQuery('.fps').removeClass('active');
						header.css('margin-top', -headerH);
					}
				}
				if (index == 2 && nextIndex == 1 && direction == 'up') {
					jQuery('.fps').addClass('active');
				}
				if (index == countBlocks && nextIndex == (countBlocks + 1) && direction == 'down') {
					jQuery('footer').addClass('active');
				}
				if (index == (countBlocks + 1) && nextIndex == countBlocks && direction == 'up') {
					jQuery('footer').removeClass('active');
				}
				jQuery(window).trigger('resize');
			}
		});
	}
};