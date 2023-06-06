/* Technetium PHP Framework
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Buttons.js - v1.0.1
   
   Dependencies:
   jQuery - http://jquery.com/
*/

var Buttons = function($) {
	
	var handleRedirect = function() {
		var els = '[data-href]:not(.disabled):not([disabled])'
		$('body').on('click', els, function(e) {
			e.preventDefault();
			var href = $(this).data('href');
			var target = $(this).data('target');
			if (typeof(href) != 'undefined') {
				if (typeof(target) != 'undefined' && target != '_self') {
					var w = window.open(href, target);
					w.focus();
				} else {
					window.location.assign(href);
				}
			}
		});
	};
	
	var handleTrigger = function() {
		var els = '[data-trigger]:not(.disabled):not([disabled])';
		$('body').on('click', els, function(e) {
			e.preventDefault();
			var fn = $(this).data('trigger');
			if (typeof(fn) != 'undefined' && typeof(eval(fn)) == 'function') eval(fn+'(this)');
		});
	};
	
	var handleBackToTop = function() {
		var els = '.back-to-top, [rel=back-to-top]';
		$('body').on('click', els, function(e) {
			e.preventDefault();
			$('html, body').animate({
				scrollTop: 0
			}, 400);
		});
		if (jQuery().waypoint) {
			if ($('html').scrollTop() < 100) {
				$(els).hide();
			}
			$('body').waypoint(function(d) {
				$(els).fadeTo(400, d == 'up' ? 0 : 1);
			}, {offset: -100});
		}
	};
	
	return({
		init: function() {
			handleRedirect();
			handleTrigger();
			handleBackToTop();
		}
	});
}(jQuery);
