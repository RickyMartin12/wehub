+(function($) {

	// Portfolio QuickView
	$(function(){
		$('.wpb_portfolio').magnificPopup({
			type:'inline',
			midClick: true,
			gallery:{
				enabled:true
			},
			delegate: 'a.wpb_fp_preview',
			removalDelay: 500, //delay removal by X to allow out-animation
			callbacks: {
			    beforeOpen: function() {
			       this.st.mainClass = this.st.el.attr('data-effect');
			    }
			},
		  	closeOnContentClick: true,
		});
	});


	// Portfolio slider

	$(function(){
		$(".wpb_fp_slider").each(function() {

			var t = $(this),
		        auto 			= t.data("autoplay") ? !0 : !1,
		        rtl 			= t.data("direction") ? !0 : !1,
		        items 			= t.data("items") ? parseInt(t.data("items")) : '',
		        tablet 			= t.data("tablet") ? parseInt(t.data("tablet")) : '',
		        mobile 			= t.data("mobile") ? parseInt(t.data("mobile")) : '',
		        margin 			= t.data("margin") ? parseInt(t.data("margin")) : '',
		        nav 			= t.data("navigation") ? !0 : !1,
		        pag 			= t.data("pagination") ? !0 : !1,
		        loop 			= t.data("loop") ? !0 : !1,
		        navTextLeft 	= t.data("direction") ? 'right' : 'left',
		        navTextRight 	= t.data("direction") ? 'left' : 'right';

			$(this).owlCarousel({
				nav: nav,
            	navText : ['<i class="wpbfpicons-arrow-'+navTextLeft+'" aria-hidden="true"></i>','<i class="wpbfpicons-arrow-'+navTextRight+'" aria-hidden="true"></i>'],
				dots: pag,
            	loop: loop,
            	margin: margin,
				autoplay: auto,
				autoHeight: true,
				rtl: rtl,
				items : items,
				responsiveClass:true,
				responsive:{
			        0:{
			            items:mobile,
			        },
			        600:{
			            items:tablet,
			        },
			        1000:{
			            items:items,
			        }
			    }
			});
		});
	});

})(jQuery);