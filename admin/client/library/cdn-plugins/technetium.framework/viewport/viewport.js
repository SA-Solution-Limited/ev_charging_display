/* Technetium PHP Framework version 2.8
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Viewport.js - v1.1.2.2
*/

var Viewport = function($) {
	
	var views = {}, events = {}, changeCount = {}, currentView = null;
	
	var init = function(config) {
		if (!config || !config.views) return(false);
		
		for (var i in config.views) {
			views[i] = config.views[i];
			events[i] = {
				onload: function() {},
				onfirstchange: function() {},
				onchange: function() {},
				onresize: function() {}
			};
			changeCount[i] = 0;
		}
		if (config.defaultView) {
			currentView = config.defaultView;
		} else {
			for (var i in config.views) {
				currentView = config.defaultView;
				break;
			}
		}
		
		$(document).one('page.ready ready.page', function(e) {
			check('ready.page');
		});
		$(window).on('resize', function(e) {
			check('resize');
			events[get()].onresize();
		});
	};
	
	var check = function(e) {
		var view = get();
		switch (e) {
			case 'ready.page':
				currentView = view;
				events[view].onload();
			break;
			case 'resize':
				if (currentView != view) {
					if (changeCount[view] == 0) {
						events[view].onfirstchange(currentView, view);
						$.event.trigger({type: 'firstchange'});
					}
					events[view].onchange(currentView, view);
					changeCount[view]++;
					currentView = view;
				}
			break;
		}
	};
	
	var addEvent = function(view, type, fn) {
		if (!view || !type || !fn) return;
		view = view.replace(/ /g, '|').split('|');
		type = type.replace(/ /g, '|').split('|');
		for (var i = 0; i < view.length; i++) {
			if (!events[view[i]]) {
				console.warn('Unknown view "' + view[i] + '".');
				continue;
			}
			for (var j = 0; j < type.length; j++) {
				if (!events[view[i]][type[j]]) {
					console.warn('Unknown event "' + type[j] + '".');
					continue;
				}
				(function() {
					var old = events[view[i]][type[j]];
					events[view[i]][type[j]] = function() {
						old.apply(this, arguments);
						fn.apply(this, arguments);
					};
				}());
			}
		}
	};
	
	var get = function() {
		var width = window.innerWidth || $(window).width();
		for (var i in views) {
			if (width >= views[i]) {
				return(i);
			}
		}
		return(false);
	};
	
	var is = function(view) {
		if (view === null) return(false);
		if (view.constructor == String) {
			view = view.replace(/ /g, '|').split('|');
		}
		return(view.indexOf(get()) > -1);
	};
	
	return({
		init: init,
		check: check,
		addEvent: addEvent,
		get: get,
		is: is
	});
	
}(jQuery);
