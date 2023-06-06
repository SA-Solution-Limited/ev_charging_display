/* Technetium PHP Framework version 2.8
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Loader.js - v2.1.1
   
   Dependencies:
   jQuery - http://jquery.com/
 */

var Loader = function() {
	
	var config = {
		beforeunload: false
	};
	
	var ready = false;
	var event = {
		pending: [],
		subscribed: []
	};
	
	var init = function() {
		if (ready) return;
		if (config.beforeunload) {
			$(window).on('beforeunload', function(e) {
				trigger();
			});
		}
		$('#ajax-loader').length == 0 && $('<div id="ajax-loader"><div></div></div>').appendTo('body');
		for (var i = 0; i < arguments.length; i++) {
			listen(arguments[i]);
		}
		event.pending.length == 0 && close();
		ready = 1;
	};
	
	var trigger = function(callback) {
		!ready && init();
		$('body').addClass('loader-open');
		$('#ajax-loader').stop().clearQueue().fadeTo(400, 1, callback);
	};
	
	var close = function(callback) {
		if (event.pending.length) return;
		$('body').removeClass('loader-open');
		var $loader = $('#ajax-loader');
		$loader.stop().clearQueue().fadeTo(400, 0, function() {
			$loader.hide();
			if (typeof(callback) == 'function') callback();
		});
	};
	
	var listen = function() {
		for (var i = 0; i < arguments.length; i++) {
			var e = arguments[i];
			event.pending.push(e);
			if (event.subscribed.indexOf(e) == -1) {
				event.subscribed.push(e);
				$(document).on(e, function() {
					unlisten(e);
				});
			}
		}
		trigger();
	};
	
	var unlisten = function() {
		for (var i = 0; i < arguments.length; i++) {
			var e = arguments[i];
			var idx = event.pending.indexOf(e);
			if (idx == -1) return;
			event.pending.splice(idx, 1);
			event.pending.length == 0 && close();
		}
	};
	
	var unlistenAll = function() {
		event.pending = [];
		close();
	};
	
	var configure = function(key, value) {
		config[key] = value;
	};
	
	return({
		init: init,
		trigger: trigger,
		close: close,
		listen: listen,
		unlisten: unlisten,
		unlistenAll: unlistenAll,
		configure: configure
	});
	
}(); 
