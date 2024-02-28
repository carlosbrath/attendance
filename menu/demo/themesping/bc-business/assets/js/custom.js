$(document).ready(function() {
	"use strict";
	//===============Mobile nav Function============
	var menu = $('#menu');
	var navigation = $('.navigation');
	menu.on('click', function() {
		if ($(window).width() <= 991) {
			navigation.slideToggle('normal');
		}
		return false;
	});
	//===============Submenu Function============
	var navigationLink = $('.navigation>ul> li>a');
	var navigationLi = $('.navigation>ul> li');
	var navigationUl = $('.navigation>ul> li>ul');
	navigationLink.on('click', function() {
		if ($(window).width() <= 991) {
			navigationLi.removeClass('on');
			navigationUl.slideUp('normal');
			if ($(this).next().next('ul').is(':hidden') == true) {
				$(this).parent('li').addClass('on');
				$(this).next().next('ul').slideDown('normal');
			}
		}
		//return false;
	});
	/*----------------------------------------------
	 -----------Counter Function  --------------------
	 -------------------------------------------------*/
	var counter = $('.counter');
	if (counter.length) {
		counter.appear(function() {
			counter.each(function() {
				var e = $(this),
				    a = e.attr("data-count");
				$({
					countNum : e.text()
				}).animate({
					countNum : a
				}, {
					duration : 8e3,
					easing : "linear",
					step : function() {
						e.text(Math.floor(this.countNum));
					},
					complete : function() {
						e.text(this.countNum);
					}
				});
			});
		});
	}

	/*accordion*/
	var accordion_select = $('.accordion');
	if (accordion_select) {
		accordion_select.each(function() {
			$(this).accordion({
				"transitionSpeed" : 400,
				transitionEasing : 'ease-in-out'
			});
		});
	}
	/*MatchHeight*/
	var matchHeigh = $('.matchHeigh');
	if (matchHeigh.length) {
		if (matchHeigh) {
			matchHeigh.matchHeight();
		}
	}

	/*----------------------------------------------
	 -----------Masonry filter Function  --------------------
	 -------------------------------------------------*/
	var container_filter = $(".container-filter");
	container_filter.on("click", ".categories", function() {
		var a = $(this).attr("data-filter");
		container_masonry.isotope({
			filter : a
		});

	});
	/*----------------------------------------------
	 -----------Masonry filter Active Function  --------------------
	 -------------------------------------------------*/
	container_filter.each(function(e, a) {
		var i = $(a);
		i.on("click", ".categories", function() {
			i.find(".active").removeClass("active"), $(this).addClass("active");
		});
	});

	/*----------------------------------------------
	 -----------Masonry Grid view Function  --------------------
	 -------------------------------------------------*/
	var container_grid = $(".container-grid");
	container_grid.imagesLoaded(function() {
		container_grid.isotope({
			itemSelector : ".nf-item",
			layoutMode : "fitRows"
		});
	});

	/*----------------------------------------------
	 -----------Masonry Grid Filter Function  --------------------
	 -------------------------------------------------*/
	container_filter.on("click", ".categories", function() {
		var e = $(this).attr("data-filter");
		container_grid.isotope({
			filter : e
		});
	});

	/*----------------------------------------------
	 -----------Masonry Function  --------------------
	 -------------------------------------------------*/
	var container_masonry = $(".container-masonry");
	container_masonry.imagesLoaded(function() {
		container_masonry.isotope({
			itemSelector : '.nf-item',
			transitionDuration : '1s',
			percentPosition : true,
			masonry : {
				columnWidth : '.grid-sizer'
			}
		});
	});

	/*----------------------------------------------
	 -----------Masonry filter Function  --------------------
	 -------------------------------------------------*/
	var container_filter = $(".container-filter");
	container_filter.on("click", ".categories", function() {
		var a = $(this).attr("data-filter");
		container_masonry.isotope({
			filter : a
		});

	});
	/*----------------------------------------------
	 -----------isotope Function  --------------------
	 -------------------------------------------------*/
	var isotop_grid = $('#isotope');
	if (isotop_grid.length) {
		// init Isotope
		var $grid = isotop_grid.isotope({
			itemSelector : 'li	',
			percentPosition : true,
			layoutMode : 'fitRows',
			fitRows : {
				gutter : 0
			}
		});
	}
	/*----------------------------------------------
	 -----------Light Function  --------------------
	 -------------------------------------------------*/
	var fLight = $(".fancylight");
	if (fLight.length) {
		fLight.fancybox({
			openEffect : 'elastic',
			closeEffect : 'elastic',
			helpers : {
				media : {}
			}
		});
	}

	(function($) {
		"use strict";
		if ($("a[rel^='prettyPhoto'], a[data-rel^='prettyPhoto']").length != 0) {
			$("a[rel^='prettyPhoto'], a[data-rel^='prettyPhoto']").prettyPhoto({
				hook : 'data-rel',
				theme : "dark_square",
				social_tools : false,
				deeplinking : false
			});
		}
	})(jQuery);

	/*----------------------------------------------
	 ----------- parallax Function  --------------------
	 -------------------------------------------------*/

	var parallaxs = $(".parallax");
	parallaxs.parallax("50%", 0.02);

	/*----------------------------------------------
	 ----------- Slider Function  --------------------
	 -------------------------------------------------*/
	var project = $("#project");
	project.owlCarousel({
		loop : true,
		nav : true,
		dots : false,
		responsive : {
			0 : {
				items : 1
			},
			767 : {
				items : 2
			},
			992 : {
				items : 2
			},
			1200 : {
				items : 4
			}
		},
		navText : ["<i class='ion-ios-arrow-back'></i>", "<i class='ion-ios-arrow-forward'></i>"]

	});
	var team_carousel = $(".team-carousel");
	if (team_carousel.length) {
		team_carousel.owlCarousel({
			loop : true,
			margin : 30,
			nav : true,
			dots : true,
			center : false,
			navText : ["<i class='icon-arrow-left'></i>", "<i class='icon-arrow-right'></i>"],
			responsive : {
				0 : {
					items : 1
				},
				767 : {
					items : 2
				},
				992 : {
					items : 3
				},
				1200 : {
					items : 4
				}
			}
		});
	}
	var blog_carousel = $('#blog-carousel');
	if (blog_carousel.length) {
		blog_carousel.owlCarousel({
			loop : true,
			margin : 10,
			nav : true,
			dots : false,
			center : false,
			navText : ["<i class='ion-ios-arrow-back'></i>", "<i class='ion-ios-arrow-forward'></i>"],
			responsive : {
				0 : {
					items : 1
				},
				767 : {
					items : 1
				},
				992 : {
					items : 2
				},
				1200 : {
					items : 2
				}
			}
		});
	}
	var testimonial = $('#testimonial');
	if (testimonial.length) {
		testimonial.owlCarousel({
			loop : true,
			margin : 10,
			nav : true,
			dots : false,
			center : false,
			navText : ["<i class='ion-ios-arrow-back'></i>", "<i class='ion-ios-arrow-forward'></i>"],
			responsive : {
				0 : {
					items : 1
				},
				767 : {
					items : 1
				},
				992 : {
					items : 2
				},
				1200 : {
					items : 2
				}
			}
		});
	}
	var client_carousel = $(".client-carousel");
	if (client_carousel.length) {
		client_carousel.owlCarousel({
			loop : true,
			margin : 10,
			nav : true,
			dots : true,
			center : true,
			navText : ["<i class='ion-ios-arrow-back'></i>", "<i class='ion-ios-arrow-forward'></i>"],
			responsive : {
				0 : {
					items : 1
				},
				767 : {
					items : 3
				},
				992 : {
					items : 5
				},
				1200 : {
					items : 5
				}
			}
		});
	}

	//	Releted Project slider
	var relatedProject = $("#related-project");
	relatedProject.owlCarousel({
		loop : true,
		nav : true,
		dots : false,
		margin : 30,
		responsive : {
			0 : {
				items : 1
			},
			767 : {
				items : 2
			},
			992 : {
				items : 2
			},
			1200 : {
				items : 3
			}
		},
		navText : ["<i class='ion-ios-arrow-back'></i>", "<i class='ion-ios-arrow-forward'></i>"]

	});

	var btt = $('#back-to-top');
	$(window).on('scroll', function() {
		if ($(this).scrollTop() > 50) {
			btt.fadeIn();
		} else {
			btt.fadeOut();
		}
	});
	// scroll body to 0px on click
	btt.on('click', function() {
		btt.tooltip('hide');
		$('body,html').animate({
			scrollTop : 0
		}, 800);
		return false;
	});

	//window load
	$(window).on('load', function() {
		var preloader = $('#preloader');
		preloader.delay().fadeOut();

	});

});

