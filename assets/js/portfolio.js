jQuery( function ($) {

	"use strict";

	$(document).on( 'click', '.portfolio-filters .list_categories .filter-item', function() {
		var category = $(this).attr('data-filter');

		$('.portfolio-items-grid .portfolio-box').removeClass('hidden');

		if( category.length > 0 && '*' !== category ) {
			$('.portfolio-items-grid .portfolio-box').removeClass('hidden');
			$('.portfolio-items-grid .portfolio-box:not(.'+category+')').addClass('hidden');
		}
	} );
});
