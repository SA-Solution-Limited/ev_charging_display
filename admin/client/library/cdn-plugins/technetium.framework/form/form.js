/* Technetium PHP Framework
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Form.js - v1.6.7
   
   Mandatory Dependencies:
   jQuery - https://jquery.com
   Util.js - https://cdn.cruzium.info/technetium.framework/latest/util/util.js
   
   Optional Dependencies:
   Font Awesome - https://fontawesome.com/
   Toastr - https://codeseven.github.io/toastr/
   SelectPair - https://cdn.cruzium.info/technetium.framework/latest/form/select-pair.js
*/

(function($) {
	'use strict';
	
	var fontAwesome4Ready = function() {
		var $span = $(document.createElement('span')).addClass('fa').hide().appendTo('body');
		var ready = $span.css('font-family').match(/FontAwesome/);
		$span.remove();
		return(!!ready);
	}();
	
	var fontAwesome5Ready = function() {
		var $span = $(document.createElement('span')).addClass('fas').hide().appendTo('body');
		var ready = $span.css('font-family').match(/Font Awesome 5/);
		$span.remove();
		return(!!ready);
	}();
	
	window.Form = function(name, opts) {
		
		var $ = jQuery;
		var name  = name;
		var $form = null;
		var $submit = null;
		
		var _d = {
			ajax: false,
			ajaxOpts: null,       // options to pass directly to jquery ajax settings
			ajaxFormat: 'form',   // format of ajax data, "form" or "json"
			dataReformat: null,   // function to re-format data before submit
			autoSubmit: true,     // whether to bind submit event automatically
			autoReset: true,      // whether to reset form after success submission
			displayMessage: true, // whether to display success message (ajax only)
			messageType: 'text',  // jquery function to insert message, "text" or "html"
			disableScroll: false, // disable auto-scrolling to invalid field
			scrollOffset: 0,      // accepts interger or callback function
			scrollElement: null,
			toastr: false         // toastr config object or true to enable
		};
		
		var init = function() {
			var error = {};
			
			if (!name) {
				error = {
					error: true,
					message: 'No form name defined'
				};
			}
			
			$form = $(document.forms[name]);
			if ($form.length == 0) {
				error = {
					error: true,
					message: 'Form element not found'
				};
			}
			
			$.extend(_d, opts);
			_d.scrollElement = _d.scrollElement || 'html';
			message.init();
			
			$form.attr('novalidate', true).on('click', ':submit', function(e) {
				$submit = $(this);
			});
			if (_d.autoSubmit) {
				$form.on('submit', function(e) {
					return(api.submit());
				});
			}
			
			if (_d.toastr && _d.toastr.constructor != Object) {
				_d.toastr = {};
			}
			
			return($.extend({
				$elem: $form
			}, api, error));
		};
		
		var data = {
			query: function(url, method, data, autoBind) {
				if ($.inArray(method.toLowerCase(), ['get', 'post']) == -1) {
					method = 'get';
				}
				if (autoBind == null) {
					autoBind = true;
				}
				
				$.ajax({
					url: url,
					type: method,
					data: data,
					success: function(response, status, jqXHR) {
						if (response.success) {
							data.obj = response.data;
							if (!!autoBind) {
								data.bind(data.obj);
							}
						} else if (response.message) {
							console.error(response.message);
							window.toastr && toastr.error(response.message, null, _d.toastr || {});
						}
					},
					error: function(response, status, jqXHR) {
						if (response.responseJSON && response.responseJSON.message) {
							var msg = response.responseJSON.message;
						} else if (response.responseText) {
							var msg = response.responseText;
						} else {
							var msg = 'Unexpected error.';
						}
						console.error(msg);
						window.toastr && toastr.error(response.message, null, _d.toastr || {});
					}
				});
			},
			bind: function(data) {
				Util.bindData(data);
			},
			get: function() {
				return(data.obj);
			},
			obj: null
		};
		
		var validate = {
			execute: function() {
				api.lock();
				$form.find('.has-error').removeClass('has-error');
				$form.find('.input-error').text('').hide();
				
				var hasError = false;
				for (var type in validate.validator) {
					var result = validate.validator[type]();
					for (var i in result) {
						validate.helper.highlightInput(i);
						validate.helper.displayMessage(i, message[validate.message[type]], result[i]);
						hasError = true;
					}
				}
				
				if (hasError && !_d.disableScroll) {
					$(_d.scrollElement).animate({
						scrollTop: validate.helper.getTargetScrollTop($form.find('.has-error:first'))
					});
				}
				
				api.unlock();
				return(!hasError);
			},
			validator: {
				required: function() {
					var array = {};
					$form.find('[required]:visible:not(:disabled)').filter(':not(select):not(:checkbox):not(:radio):not(:file)').each(function() {
						var name = $(this).attr('name');
						if ($(this).val() == '' || $(this).val() == null) array[name] = {};
					});
					return(array);
				},
				required_option: function() {
					var array = {};
					$form.find('[required]:visible:not(:disabled)').filter('select, :checkbox, :radio').each(function() {
						var name = $(this).attr('name');
						if ($(this).is(':checkbox, :radio')) {
							if ($form.find('[name="'+name+'"]:checked').length == 0) array[name] = {};
						} else {
							if ($(this).val() == '' || $(this).val() == null) array[name] = {};
						}
					});
					return(array);
				},
				required_file: function() {
					var array = {};
					$form.find('[required]:visible:not(:disabled)').filter(':file').each(function() {
						var name = $(this).attr('name');
						if ($(this).val() == '' || $(this).val() == null) array[name] = {};
					});
					return(array);
				},
				email: function() {
					var array = {};
					$form.find('[type=email]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						var regexp = new RegExp('^[\\w!#$%&\'*+/=?^_`{|}~-]+(?:\\.[\\w!#$%&\'*+/=?^_`{|}~-]+)*@(?:[\\w](?:[\\w-]*[\\w])?\\.)+[\\w](?:[\\w-]*[\\w])?$', 'i');
						if (!$(this).val().match(regexp)) array[name] = {};
					});
					return(array);
				},
				tel: function() {
					var array = {};
					$form.find('[type=tel]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						if (!$(this).val().match(/^(\(\d+\)|\+\d+|\d*)[\d ]+\d+$/i)) array[name] = {};
					});
					return(array);
				},
				number: function() {
					var array = {};
					$form.find('[type=number]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						if (isNaN($(this).val())) array[name] = {};
					});
					return(array);
				},
				number_min: function() {
					var array = {};
					$form.find('[type=number][min]:not([max]):visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						if (parseFloat($(this).val()) < parseFloat($(this).attr('min'))) {
							array[name] = {
								min: parseFloat($(this).attr('min'))
							};
						}
					});
					return(array);
				},
				number_max: function() {
					var array = {};
					$form.find('[type=number][max]:not([min]):visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						if (parseFloat($(this).val()) > parseFloat($(this).attr('max'))) {
							array[name] = {
								max: parseFloat($(this).attr('max'))
							};
						}
					});
					return(array);
				},
				number_range: function() {
					var array = {};
					$form.find('[type=number][min][max]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						if (parseFloat($(this).val()) < parseFloat($(this).attr('min')) || parseFloat($(this).val()) > parseFloat($(this).attr('max'))) {
							array[name] = {
								min: parseFloat($(this).attr('min')),
								max: parseFloat($(this).attr('max'))
							};
						}
					});
					return(array);
				},
				integer: function() {
					var array = {};
					$form.find('[type=number][step="1"]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						if ($(this).val() % $(this).attr('step') != 0) array[name] = {};
					});
					return(array);
				},
				number_step: function() {
					var array = {};
					$form.find('[type=number][step]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						if ($(this).val() % $(this).attr('step') != 0) {
							array[name] = {
								step: $(this).attr('step')
							};
						}
					});
					return(array);
				},
				maxlength: function() {
					var array = {};
					$form.find('[maxlength]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '' || !$(this).attr('maxlength')) return;
						var name = $(this).attr('name');
						if ($(this).val().toString().length > $(this).attr('maxlength')) array[name] = {length: $(this).attr('maxlength')};
					});
					return(array);
				},
				pattern: function() {
					var array = {};
					$form.find('[pattern]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						var regexp = new RegExp($(this).attr('pattern'));
						if (!$(this).val().match(regexp)) array[name] = {};
					});
					return(array);
				},
				multiselect: function() {
					var array = {};
					$form.find('[name$="[]"][data-multiselect]:visible:not(:disabled)').each(function() {
						var rule = $(this).data('multiselect');
						if (!rule) return;
						rule = (rule + '').split(',');
						if (!rule[0]) rule[0] = 0;
						if (!rule[1]) rule[1] = rule[0];
						if ($(this).is(':checkbox')) {
							var name = $(this).attr('name');
							var selected = $form.find('[name="'+name+'"]:checked').length;
						} else {
							var selected = $(this).val().length;
						}
						if (selected < rule[0] || selected > rule[1]) array[name] = {};
					});
					return(array);
				},
				ajax: function() {
					var array = {};
					$form.find('[ajax-validate]:visible:not(:disabled)').each(function() {
						if ($(this).val() == '') return;
						var name = $(this).attr('name');
						var data = {};
						data[name] = $(this).val();
						var isValid = false;
						$.ajax({
							url: $(this).attr('ajax-validate'),
							type: 'POST',
							data: opts.ajaxFormat == 'json' ? JSON.stringify(data) : data,
							contentType: opts.ajaxFormat == 'json' ? 'application/json' : 'application/x-www-form-urlencoded',
							async: false,
							success: function(response) {
								isValid = response.success;
								if (!isValid) {
									validate.helper.highlightInput(name);
									validate.helper.displayMessage(name, response.message ? response.message : message[validate.message.ajax]);
								}
							},
							error: function(response) {
								isValid = false;
								validate.helper.highlightInput(name);
								validate.helper.displayMessage(name, Util.getNestedProperties(response, 'responseJSON.message', message[validate.message.ajax]));
							}
						});
						if (!isValid) array[name + ':ajax' + new Date().getTime()] = {};
					});
					return(array);
				},
				recaptcha: function() {
					var $input = $form.find('[name=g-recaptcha-response]:not(:disabled)');
					if ($input.length == 0) {
						return({});
					} else if ($input.val() == '' || $input.val() == null) {
						return({'g-recaptcha-response':{}});
					}
					return({});
				},
				terms: function() {
					var array = {};
					$form.find(':checkbox[name^="agreeTerms"]:visible:not(:disabled)').each(function() {
						var name = $(this).attr('name');
						if (!$(this).is(':checked')) array[name] = {};
					});
					return(array);
				}
			},
			message: {
				required:        'FIELD_REQUIRED',
				required_option: 'FIELD_REQUIRED_OPTION',
				required_file:   'FIELD_REQUIRED_FILE',
				email:           'FIELD_INVALID_EMAIL',
				tel:             'FIELD_INVALID_TEL',
				number:          'FIELD_INVALID_NUMBER',
				number_range:    'FIELD_INVALID_NUMBER_RANGE',
				integer:         'FIELD_INVALID_INTEGER',
				number_step:     'FIELD_INVALID_NUMBER_STEP',
				maxlength:       'FIELD_INVALID_MAXLENGTH',
				pattern:         'FIELD_INVALID_PATTERN',
				multiselect:     'FIELD_INVALID_SELECTION',
				ajax:            'FIELD_DUPLICATED',
				recaptcha:       'FIELD_RECAPTCHA',
				terms:           'FIELD_AGREE_TERMS'
			},
			helper: {
				highlightInput: function(name) {
					var $els = $form.find('[name="'+name+'"]');
					$els.addClass('has-error');
				},
				displayMessage: function(name, message, param) {
					var $els = $form.find('[name="'+name+'"]').parents('.input-field').eq(0);
					if ($els.find('.input-error').length == 0) {
						$els.append('<div class="input-error"></div>');
					}
					
					$els = $els.find('.input-error');
					if ($els.text() == '') {
						for (var i in param) {
							message = message.replace(new RegExp('\{\{' + i + '\}\}', 'gi'), param[i]);
						}
						if (_d.messageType.toLowerCase() == 'html') {
							$els.html(message);
						} else {
							$els.text(message);
						}
					}
					$els.show();
				},
				getTargetScrollTop: function($els) {
					var offset = Util.isFunction(_d.scrollOffset) ? _d.scrollOffset() : _d.scrollOffset;
					if ($(_d.scrollElement).is('html')) {
						return($els.offset().top - offset);
					} else {
						return($els.offset().top - $(_d.scrollElement).offset().top + $(_d.scrollElement).scrollTop() - offset);
					}
				}
			}
		};
		
		var ajax = {
			enable: function() {
				_d.ajax = true;
			},
			disable: function() {
				_d.ajax = false;
			},
			setup: function(ajaxSuccess, ajaxError, type) {
				switch (type) {
					case 'replace':
						if (typeof(ajaxSuccess) == 'function') ajax.success = ajaxSuccess;
						if (typeof(ajaxError) == 'function') ajax.error = ajaxError;
						break;
					case 'prepend':
						if (typeof(ajaxSuccess) == 'function') {
							var successCallback = ajax.success;
							ajax.success = function(response) {
								if (ajaxSuccess(response) === false) return;
								successCallback(response);
							};
						}
						if (typeof(ajaxError) == 'function') {
							var errorCallback = ajax.error;
							ajax.error = function(xhr, status, error) {
								if (ajaxError(xhr, status, error) === false) return;
								errorCallback(xhr, status, error);
							};
						}
						break;
					case 'append':
					default:
						if (typeof(ajaxSuccess) == 'function') {
							var successCallback = ajax.success;
							ajax.success = function(response) {
								successCallback(response);
								if (ajaxSuccess(response) === false) return;
							};
						}
						if (typeof(ajaxError) == 'function') {
							var errorCallback = ajax.error;
							ajax.error = function(xhr, status, error) {
								errorCallback(xhr, status, error);
								if (ajaxError(xhr, status, error) === false) return;
							};
						}
				}
			},
			collect: function() {
				if ($form.attr('enctype') == 'multipart/form-data' && $form.find(':file').length > 0 && typeof(FormData) != 'undefined') {
					// use formdata only when there is file upload
					var data = new FormData($form[0]);
					// remove empty file
					$form.find(':file').each(function() {
						if ($(this).val() != '') return;
						var name = $(this).attr('name');
						data.delete(name);
					});
					if ($submit && $submit.attr('name')) {
						data.append($submit.attr('name'), $submit.val());
					}
				} else {
					// fallback; multipart upload is not supported
					var data = $form.serializeObject();
					// look for checkbox which should be returned with boolean
					var $checkbox = $form.find(':checkbox');
					$checkbox.each(function() {
						var name = $(this).attr('name');
						if (name.match(/\[\]$/) || $checkbox.filter('[name="' + name + '"]').length > 1) {
							return;
						}
						if (name.match(/\[(.+?)\]/)) {
							var setPointer = function(base, key) {
								if (base[key] === undefined) {
									base[key] = {};
								}
								return(base[key]);
							};
							var pointer = setPointer(data, name.replace(/\[.+\]/, ''));
							var matches = name.match(/\[(.+?)\]/g).map(function(key) {
								return(key.replace(/\[(.+?)\]/, '$1'));
							});
							while (matches.length > 1) {
								pointer = setPointer(pointer, matches.shift());
							}
							pointer[matches[0]] = $(this).is(':checked');
						} else {
							data[name] = $(this).is(':checked');
						}
					});
					// add button's action
					if ($submit && $submit.attr('name')) {
						data[$submit.attr('name')] = $submit.val();
					}
				}
				$submit = null;
				return(data);
			},
			success: function(response, status, jqXHR) {
				if (response.success) {
					if (response.redirect) {
						if (response.message) window.alert(response.message);
						return(Util.redirect(response.redirect, response.redirectType));
					}
					!!_d.autoReset && api.reset();
					alert.show('success', response.message ? response.message : message.FORM_SUCCESS, true);
					$form.trigger('ajaxsubmit.success', response);
				} else {
					ajax.error({responseJSON: response});
				}
				api.unlock();
			},
			error: function(response, status, jqXHR) {
				var msg = message.FORM_ERROR;
				if (response.responseJSON && response.responseJSON.message) {
					msg = response.responseJSON.message;
				} else if (response.responseText) {
					msg = response.responseText;
				}
				alert.show('error', msg, true);
				
				/* google recaptcha */
				if ($form.find('[name=g-recaptcha-response]').length) {
					grecaptcha.reset();
				}
				
				$form.trigger('ajaxsubmit.error', response);
				api.unlock();
			}
		};
		
		var alert = {
			show: function(type, msg, focus) {
				if (type == 'success' && !_d.displayMessage) {
					return;
				}
				if (window.toastr && _d.toastr) {
					switch (type) {
						case 'success':
							console.info(msg);
							toastr.success(msg, null, _d.toastr);
						break;
						case 'error':
							console.error(msg);
							toastr.error(msg, null, _d.toastr);
						break;
					}
				} else {
					var fn = _d.messageType.toLowerCase() == 'html' ? 'html' : 'text';
					alert.getObject(type, focus)[fn](msg).show();
				}
			},
			hide: function() {
				if (window.toastr && _d.toastr) {
					toastr.clear();
				} else {
					alert.getObject().hide();
				}
			},
			getObject: function(type, focus) {
				var $els = $form.find('.alert');
				if ($els.length == 0) {
					$els = $(document.createElement('div')).addClass('alert').prependTo($form);
				}
				var cssClassMapper = {
					success: 'alert-success',
					error: 'alert-danger alert-error'
				};
				$els.removeClass(Object.values(cssClassMapper).join(' '))
				if (type) {
					$els.addClass(cssClassMapper[type]);
				}
				if (focus && !_d.disableScroll) {
					$(_d.scrollElement).animate({
						scrollTop: validate.helper.getTargetScrollTop($form)
					});
				}
				return($els);
			}
		};
		
		var message = {
			init: function() {
				for (var i in FormMessage) {
					message[i] = FormMessage[i];
				}
			},
			set: function(key, value) {
				message[key] = value;
			}
		};
		
		var api = {
			getObject: function() {
				return($form);
			},
			queryData: data.query,
			bindData: data.bind,
			getData: data.get,
			ajax: {
				enable: ajax.enable,
				disable: ajax.disable,
				setup: ajax.setup,
				collect: ajax.collect
			},
			validate: validate.execute,
			submit: function() {
				alert.hide();
				if (!validate.execute()) return(false);
				if (_d.ajax) {
					api.lock();
					
					var data = ajax.collect();
					if (typeof(_d.dataReformat) == 'function') {
						data = _d.dataReformat(data)
					}
					
					var opts = $.extend({
						url: $form.attr('action'),
						type: $form.attr('method'),
						dataType: 'json',
					}, _d.ajaxOpts, {
						success: ajax.success,
						error: ajax.error
					});
					
					if (_d.ajaxFormat != 'form' && _d.ajaxFormat != 'json') {
						_d.ajaxFormat = 'form';
					}
					
					if (_d.ajaxFormat == 'form') {
						opts.data = data;
						if (data instanceof FormData) {
							opts.contentType = false;
							opts.processData = false;
						}
					} else {
						opts.data = JSON.stringify(data);
						opts.contentType = 'application/json';
					}
					
					// oauth 2.0 support
					/*var setBearerToken = function(token) {
						opts.beforeSend = function(xhr) {
							xhr.setRequestHeader('Authorization', 'bearer ' + token);
						};
					};
					if (data instanceof FormData && data.get('bearer_token')) {
						setBearerToken(data.get('bearer_token'));
						data.remove('bearer_token');
					} else if (data.bearer_token) {
						setBearerToken(data.bearer_token);
						data.bearer_token = null;
					}*/
					
					$form.trigger('ajaxbeforesubmit');
					return($.ajax(opts) && false);
				}
				return(true);
			},
			reset: function() {
				$form[0].reset();
				$form.find('.alert, .input-error').hide();
				$form.find('.has-error').removeClass('has-error');
				
				/* google recaptcha */
				if ($form.find('[name=g-recaptcha-response]').length) {
					grecaptcha.reset();
				}
			},
			lock: function(hideSpinner) {
				var $btn = helper.getSubmitButtons().blur().prop('disabled', true);
				if (hideSpinner) return;
				$btn.each(function() {
					if (!$(this).is('button') || $(this).find('.fa-spin').length) return;
					if (fontAwesome5Ready) {
						$(this).append(' <i class="fas fa-spin fa-circle-notch"></i>');
					} else if (fontAwesome4Ready) {
						$(this).append(' <i class="fa fa-spin fa-circle-o-notch"></i>');
					}
				});
			},
			unlock: function() {
				helper.getSubmitButtons().prop('disabled', false).each(function() {
					$(this).is('button') && $(this).find('.fa-spin').remove();
				});
			},
			alert: alert,
			message: {
				set: message.set
			}
		};
		
		var helper = {
			getSubmitButtons: function() {
				var $btn = $form.find(':submit');
				var formId = $form.attr('id');
				if (formId) {
					$btn = $.merge($btn, $(':submit[form="' + formId + '"]'));
				}
				return($btn);
			}
		};
		
		return(init());
	};
	
	window.FormMessage = {
		FORM_SUCCESS: 'Success.',
		FORM_ERROR: 'Failure.',
		FIELD_REQUIRED: 'Please fill in this field.',
		FIELD_REQUIRED_OPTION: 'Please choose an option.',
		FIELD_REQUIRED_FILE: 'Please select a file to upload.',
		FIELD_INVALID_EMAIL: 'Please enter a valid email.',
		FIELD_INVALID_TEL: 'Please enter a valid phone number.',
		FIELD_INVALID_NUMBER: 'Please enter a valid number.',
		FIELD_INVALID_NUMBER_MIN: 'Please enter a number larger than or equal to {{min}}.',
		FIELD_INVALID_NUMBER_MAX: 'Please enter a number smaller than or equal to {{max}}.',
		FIELD_INVALID_NUMBER_RANGE: 'Please enter a number in correct range ({{min}} - {{max}}).',
		FIELD_INVALID_INTEGER: 'Please enter an integer.',
		FIELD_INVALID_NUMBER_STEP: 'Please enter a number with multiple of {{step}}.',
		FIELD_INVALID_SELECTION: 'Please select correct number of options.',
		FIELD_INVALID_MAXLENGTH: 'Please reduce the length of this field to a maximum of {{length}} characters.',
		FIELD_INVALID_PATTERN: 'Please check the validity of this field.',
		FIELD_DUPLICATED: 'The data has been used by other user.',
		FIELD_RECAPTCHA: 'Please solve the reCAPTCHA.',
		FIELD_AGREE_TERMS: 'Please read and agree terms and conditions.'
	};
	
	/* credit to @maÄek on stackflow "Convert forms to JSON LIKE A BOSS" source https://goo.gl/6AvqF2 */
	$.fn.serializeObject = function(){
		var self = this,
			json = {},
			push_counters = {},
			patterns = {
				"validate": /^[a-zA-Z_][a-zA-Z0-9_\-.:]*(?:\[(?:\d*|[a-zA-Z0-9_\-.]+)\])*$/,
				"key":      /[a-zA-Z0-9_\-.:]+|(?=\[\])/g,
				"push":     /^$/,
				"fixed":    /^\d+$/,
				"named":    /^[a-zA-Z0-9_\-.:]+$/
			};
		this.build = function(base, key, value){
			base[key] = value;
			return base;
		};
		this.push_counter = function(key){
			if(push_counters[key] === undefined){
				push_counters[key] = 0;
			}
			return push_counters[key]++;
		};
		$.each($(this).serializeArray(), function(){
			// skip invalid keys
			if(!patterns.validate.test(this.name)){
				return;
			}
			var k,
				keys = this.name.match(patterns.key),
				merge = this.value,
				reverse_key = this.name;
			while((k = keys.pop()) !== undefined){
				// adjust reverse_key
				reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');
				// push
				if(k.match(patterns.push)){
					merge = self.build([], self.push_counter(reverse_key), merge);
				}
				// fixed
				else if(k.match(patterns.fixed)){
					merge = self.build([], k, merge);
				}
				// named
				else if(k.match(patterns.named)){
					merge = self.build({}, k, merge);
				}
			}
			json = $.extend(true, json, merge);
		});
		return json;
	};
	
})(jQuery);
