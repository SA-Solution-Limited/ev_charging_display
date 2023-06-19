/* Technetium PHP Framework
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   API-Helper.js - v1.0.1.3
   
   Dependencies:
   jQuery - http://jquery.com/
   Toastr - https://codeseven.github.io/toastr/
   Util.js - http://cdn.cruzium.info/technetium.framework/latest/util/util.js
*/

'use strict';

window.ApiHelper = (function() {
	
	var settings = {
		dataFormat: 'form', // form|json
		crossDomain: false,
		messageHandler: null
	};
	
	var init = function(opts) {
		settings = $.extend(true, settings, opts);
	};
	
	var state = {
		isReady: function() {
			return(state.ready);
		},
		enable: function() {
			state.ready = true;
			$.event.trigger({type: 'enable.apihelper'});
		},
		disable: function() {
			state.ready = false;
			$.event.trigger({type: 'disable.apihelper'});
		},
		ready: true
	};
	
	var ajax = {
		setup: function(options, type) {
			if (!type) type = 'extend';
			switch (type) {
				case 'extend':
					ajax.settings = $.extend(true, ajax.settings, options);
					break;
				case 'replace':
					ajax.settings = options;
					break;
			}
		},
		get: function(url, data, fnDone, fnFail, fnHandling) {
			return(ajax.submit('get', url, data, fnDone, fnFail, fnHandling));
		},
		post: function(url, data, fnDone, fnFail, fnHandling) {
			return(ajax.submit('post', url, data, fnDone, fnFail, fnHandling));
		},
		Delete: function(url, data, fnDone, fnFail, fnHandling) {
			return(ajax.submit('delete', url, data, fnDone, fnFail, fnHandling));
		},
		submit: function(method, url, data, fnDone, fnFail, fnHandling) {
			if (!state.ready) {
				var promise = $.when(function() {
					var defer = $.Deferred();
					$(document).one('enable.apihelper', function() {
						ajax.submit(method, url, data, fnDone, fnFail, fnHandling).done(function(response, status, xhr) {
							defer.resolve(response, status, xhr);
						}).fail(function(xhr, status, error) {
							defer.reject(xhr, status, error);
						});
					});
					return(defer.promise());
				}());
				return(promise);
			}
			if (!fnHandling) {
				fnHandling = 'replace';
			}
			fnDone = callback.build(callback.defaults.success, fnDone, fnHandling);
			fnFail = callback.build(callback.defaults.error, fnFail, fnHandling);
			return($.ajax(ajax.helper.buildSettings(method, url, data)).done(fnDone).fail(fnFail));
		},
		helper: {
			buildSettings: function(method, url, data) {
				var opts = {
					url: url,
					type: method,
					data: data
				};
				if (method != 'get' && settings.dataFormat == 'json') {
					opts.data = JSON.stringify(opts.data);
					opts.contentType = 'application/json';
				}
				if (settings.crossDomain) {
					opts.crossDomain = true;
					opts.xhrFields = {
						withCredentials: true
					};
				}
				return($.extend(true, {}, ajax.settings, opts));
			}
		},
		settings: {}
	};
	
	var callback = {
		build: function(fnBase, fnCustom, handling) {
			if (typeof(fnCustom) == 'function') {
				switch (handling) {
					case 'replace':
						return(fnCustom);
					case 'prepend':
						return(function(arg1, arg2, arg3) {
							fnCustom(arg1, arg2, arg3);
							fnBase(arg1, arg2, arg3);
						});
					case 'append':
					default:
						return(function(arg1, arg2, arg3) {
							fnBase(arg1, arg2, arg3);
							fnCustom(arg1, arg2, arg3);
						});
				}
			} else {
				return(fnBase);
			}
		},
		defaults: {
			success: function(response, status, jqXHR) {
				if (response.success) {
					if (response.message && !response.redirect) {
						callback.messageHandler('success', response.message);
					}
				} else {
					if (response.message) {
						callback.messageHandler('error', response.message);
					}
				}
				if (response.redirect) {
					Util.redirect(response.redirect, response.redirectType);
				}
			},
			error: function(xhr, status, error) {
				if (xhr.responseJSON) {
					if (Util.hasNestedProperties(xhr, 'responseJSON.message')) {
						callback.messageHandler('error', xhr.responseJSON.message);
					}
					if (Util.hasNestedProperties(xhr, 'responseJSON.redirect')) {
						Util.redirect(response.redirect, response.redirectType);
					}
				} else if (xhr.responseText) {
					callback.messageHandler('error', xhr.responseText);
				}
			}
		},
		messageHandler: function(type, message) {
			// possible values for "type": success, error
			type == 'error' && console.error(message);
			typeof(settings.messageHandler) == 'function' && settings.messageHandler(type, message);
		}
	};
	
	return({
		init: init,
		getStatus: state.isReady,
		enable: state.enable,
		disable: state.disable,
		setup: ajax.setup,
		get: ajax.get,
		post: ajax.post,
		delete: ajax.Delete
	});
})();
