/* Technetium PHP Framework version 2.8
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Responsive-UI.js - v1.1.1
   
   Dependencies:
   jQuery - http://jquery.com/
 */

'use strict';

var ResponsiveUI = function() {

	/* inspired by https://developer.mozilla.org/en-US/docs/Web/CSS/env */
	var setSafeArea = function() {
		$(window).on('resize', function() {
			$('html').css({
				'--safe-area-vw': $(window).width() + 'px',
				'--safe-area-vh': $(window).height() + 'px',
			});
		}).trigger('resize');
	};
	
	var hamburgerMenu = function() {
		var api = {
			init: function() {
				$('.hamburger').on('click', function() {
					api.toggle();
				});
			},
			toggle: function() {
				$('body').hasClass('mm-opened') ? api.close() : api.open();
			},
			open: function() {
				$('body').addClass('mm-opened');
			},
			close: function() {
				$('body').removeClass('mm-opened');
			}
		};
		return(api);
	}();
	
	var responsiveContent = function() {
		var conatiner = {
			'table': 'table-responsive',
			'iframe[src*=youtube]': 'video-responsive'
		};
		return({
			init: function(els) {
				els = els || 'article';
				for (var i in conatiner) {
					$(els).find(i).each(function() {
						var $elem = $(this);
						if ($elem.parent().hasClass(conatiner[i])) return;
						var $conatiner = $(document.createElement('div')).addClass(conatiner[i]).insertAfter($elem);
						$conatiner.append($elem);
					});
				}
			}
		})
	}();
	
	var disablePinchZoom = function() {
		document.addEventListener('touchstart', function(e) {
			if (e.touches.length > 1) {
				e.preventDefault();
			}
		}, {passive: false});
	};
	
	return({
		setSafeArea: setSafeArea,
		hamburgerMenu: hamburgerMenu,
		responsiveContent: responsiveContent,
		disablePinchZoom: disablePinchZoom
	});
}();
