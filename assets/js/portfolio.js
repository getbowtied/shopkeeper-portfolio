jQuery( function ($) {

	"use strict";

	//portfolio isotope - adjust wrapper width, return portfolio_grid
    function isotope_initWrapper () {

		var window_width = $(window).width();

		if ( window_width > 1680 ) {
			$portfolio_grid = 5;

			if( $('.items_per_row_4').length ) {
				$portfolio_grid = 4;
			}

		} else if ( window_width <= 480 ) {
			$portfolio_grid = 1;
		} else if ( window_width <= 768 ) {
			$portfolio_grid = 2;
		} else if ( window_width <= 1280 ) {
			$portfolio_grid = 3;
		} else {
			$portfolio_grid = 4;

			if( $('.items_per_row_3').length ) {
				$portfolio_grid = 3;
			}
		}

        $portfolio_wrapper_width = $('.portfolio-isotope-container').width();

        if ( $portfolio_wrapper_width % $portfolio_grid > 0 ) {
            $portfolio_wrapper_width = $portfolio_wrapper_width + ( $portfolio_grid - $portfolio_wrapper_width%$portfolio_grid);
        };

        $('.portfolio-isotope').css('width',$portfolio_wrapper_width);

        return $portfolio_grid;
    }

	function after_isotope_init() {
		setTimeout( function() {
			$(".portfolio-box").removeClass('hidden');
		}, 200);
	}

	function isotope_init() {
		var imgLoad = imagesLoaded($('.portfolio-isotope'));

		imgLoad.on('done',function(){

			$portfolio_wrapper_inner = $('.portfolio-isotope').isotope({
				"itemSelector": ".portfolio-box",
				"masonry": { "columnWidth": ".portfolio-grid-sizer" }
			});

			after_isotope_init();
		})

		imgLoad.on('fail',function(){

			portfolio_wrapper_inner = $('.portfolio-isotope').isotope({
				"itemSelector": ".portfolio-box",
				"masonry": { "columnWidth": ".portfolio-grid-sizer" }
			});

			after_isotope_init();
		});
	}

	//portfolio isotope - hover effect
	$('.hover-effect-text').each(function(){
		$(this).css( 'bottom', -$(this).outerHeight() ).attr( 'data-height', $(this).outerHeight() );
	})

	$('.hover-effect-link').on({
		mouseenter: function() {
			if ( !$(this).find('.hover-effect-text').is(':empty') ) {

				var portfolio_cat_height = $(this).find('.hover-effect-text').outerHeight();

				$(this).find('.hover-effect-title').css( 'bottom', portfolio_cat_height );
				$(this).find('.hover-effect-text').css( 'bottom', 0 );
			}
		},
		mouseleave: function() {
			if ( !$(this).find('.hover-effect-text').is(':empty') ) {

				var portfolio_cat_height = $(this).find('.hover-effect-text').attr('data-height');

				$(this).find('.hover-effect-title').css( 'bottom', 28 );
				$(this).find('.hover-effect-text').css( 'bottom', -portfolio_cat_height );
			}
		}
	});

    //portfolio isotope
    if ( $('.portfolio-isotope-container').length ) {

		var $portfolio_wrapper_inner,
            $portfolio_wrapper_width,
            $portfolio_grid,
            $filterValue;

        $filterValue = $('.filters-group .is-checked').attr('data-filter');

        $portfolio_grid =  isotope_initWrapper();
        isotope_initWrapper();

        isotope_init();

        // filter items on button click
        $('.filters-group').on( 'click', '.filter-item', function() {
            $filterValue = $(this).attr('data-filter');
            $(this).parents('.portfolio-filters').siblings('.portfolio-isotope').isotope({ filter: $filterValue });
		});
    }

    $(window).on( 'resize', function(){

    	//portfolio isotope
        if ( $('.portfolio-isotope-container').length ) {

            var $portfolio_grid_on_resize;

            isotope_initWrapper();
            $portfolio_grid_on_resize = isotope_initWrapper();

            if ( $portfolio_grid != $portfolio_grid_on_resize ) {

                $('.filters-group .filter-item').each(function(){
                    if ( $(this).attr('data-filter') == $filterValue ){
                            $(this).trigger('click');
                    }
                })

                $portfolio_grid = $portfolio_grid_on_resize;

				resizeIsotopeEnd();
            }
        }
    });
});
