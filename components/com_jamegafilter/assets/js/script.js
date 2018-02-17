function initScript() {
	// accordion menu filter critirea
	jQuery('dt.filter-options-title').off().unbind().click(function() {
		// do not use collapse with horizontal.
		if (jQuery('.ja-mg-sidebar').hasClass('sb-horizontal')) return false;
		//collapsed
		if (jQuery(this).hasClass('collapsed')) {
			jQuery(this).removeClass('collapsed');
			jQuery(this).next().slideDown();
		} else {
			jQuery(this).addClass('collapsed');
			jQuery(this).next().slideUp();
		}
		// save to cookie
		var arrTab = new Array();
		jQuery('dt.filter-options-title').each(function(i){
			arrTab[i] = jQuery(this).hasClass('collapsed');
		});
		jQuery.cookie(jQuery('.ja-mg-sidebar').data('mgfilter'), arrTab);
	});
	// change layout product list.
	jQuery('.jamg-layout-chooser>span').off().unbind().click(function() {
		jQuery('.jamg-layout-chooser>span').removeClass('active');
		jQuery('.jamg-layout-chooser>span[data-layout="'+jQuery(this).attr('data-layout')+'"]').addClass('active');
		jQuery.cookie("jamg-layout", jQuery(this).attr('data-layout'));
		jQuery('.ja-products-wrapper.products.wrapper')
			.removeClass('grid products-grid list products-list')
			.addClass(jQuery(this).attr('data-layout')+' products-'+jQuery(this).attr('data-layout'));

		jQuery.event.trigger('jamg-layout-change');
	});

	// trigger change layout cookie
	if (jQuery.cookie("jamg-layout") != undefined) {
		jQuery('.jamg-layout-chooser>span').removeClass('active');
		jQuery('.jamg-layout-chooser>span[data-layout="'+jQuery.cookie("jamg-layout")+'"]').addClass('active');
		jQuery('.ja-products-wrapper.products.wrapper')
			.removeClass('grid products-grid list products-list')
			.addClass(jQuery.cookie("jamg-layout")+' products-'+jQuery.cookie("jamg-layout"));
	} else {
		// default value.
		jQuery('.jamg-layout-chooser>span[data-layout="grid"]').addClass('active');
	}

	// trigger collapse critirie
	if (jQuery.cookie(jQuery('.ja-mg-sidebar').data('mgfilter')) != undefined) {
		// do not use collapse with horizontal.
		if (jQuery('.ja-mg-sidebar').hasClass('sb-horizontal')) return false;
		var arrTab = jQuery.cookie(jQuery('.ja-mg-sidebar').data('mgfilter')).split(',');
		jQuery('dt.filter-options-title').each(function(i){
			if (arrTab[i] == "true") {
				jQuery(this).addClass('collapsed');
				jQuery(this).next().slideUp();
			}
		});
	}
}