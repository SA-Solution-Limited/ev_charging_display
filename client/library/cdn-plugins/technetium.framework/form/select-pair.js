/* Technetium PHP Framework
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Select-Pair.js - v0.1.8
   
   Dependencies:
   jQuery - http://jquery.com/
   TaffyDB - http://taffydb.com/
*/

(function() {
	'use strict';
	
	if (!window.jQuery) {
		console.error('jQuery is required for select-pair.js.');
		return;
	}
	if (!window.TAFFY) {
		console.error('TaffyDB is required for select-pair.js.');
		return;
	}
	
	window.SelectPair = window.SelectPair || function(opts) {
		var defaults = {
			parentName: null, // accepts string or array
			childName: null,
			formName: null,
			method: 'get',
			url: null,
			data: {}, // accepts data objects or callback function, use only when url is defined
			dataFormatter: null, // optional function to further format parameters
			dataSource: null, // array of preloaded data, use only when url is not defined
			fieldNames: {
				id: null, // accepts property key or callback function
				name: null // accepts property key or callback function
			},
			filter: {},
			autoSelect: true, // auto select if only 1 result is returned; only applies to single selection dropdowns
			autoTrigger: true, // trigger change event after init
			queryOnEmpty: false // whether to perform ajax query if select value is empty
		};
		opts = $.extend(true, {}, defaults, opts);
		
		if (opts.url == null && opts.dataSource == null) {
			console.error('No data source is defined for "' + opts.childName + '".');
			return;
		}
		
		opts.formSelector = opts.formName ? 'form[name="' + opts.formName + '"]' : null;
		
		if (opts.parentName.constructor == String) {
			opts.parentName = [opts.parentName];
		}
		var $parent = $('[name="' + opts.parentName.join('"],[name="') + '"]', opts.formSelector);
		var $child  = $('[name="' + opts.childName + '"]', opts.formSelector);
		
		if ($parent.length == 0 || $child.length == 0) {
			return(false);
		}
		
		$parent.on('change', function() {
			$child.children('option[value!=""]').remove();
			
			var renderOptions = function(data) {
				if (data.TAFFY === true) {
					var db = data;
				} else {
					var db = TAFFY(data);
				}
				db(opts.filter).each(function(row, idx) {
					var id = typeof(opts.fieldNames.id) == 'function' ? opts.fieldNames.id(row) : row[opts.fieldNames.id];
					var text = typeof(opts.fieldNames.name) == 'function' ? opts.fieldNames.name(row) : row[opts.fieldNames.name];
					$(document.createElement('option')).val(id).text(text).appendTo($child);
				});
				if ($child.data('default')) {
					$child.val($child.data('default')).removeAttr('data-default').removeData('default');
				}
				if ($child.is(':not([multiple])') && opts.autoSelect && $child.children('option[value!=""]').length == 1) {
					$child.children('option[value!=""]').prop({selected: true});
				}
				$child.trigger('render.options').trigger('change');
			};
			
			var userdata = {};
			$.each(opts.parentName, function(idx, name) {
				userdata[name] = $('[name="' + name + '"]').val();
			});
			if (!opts.queryOnEmpty) {
				var isEmpty = Object.values(userdata).filter(function(v) {
					return(v == null || v == '' || (v.constructor == Array && v.length == 0));
				}).length > 0;
				if (isEmpty) {
					$child.trigger('change');
					return;
				}
			}

			if (opts.url) {
				var basedata = $.extend({}, typeof(opts.data) == 'function' ? opts.data() : opts.data);
				var data = $.extend({}, basedata, userdata);
				if (typeof(opts.dataFormatter) == 'function') {
					data = opts.dataFormatter(data);
				}
				$.ajax({
					url: opts.url,
					type: opts.method,
					data: data
				}).always(function() {
					$child.children('option[value!=""]').remove();
				}).done(function(response) {
					if (!response.success || !response.data) return;
					renderOptions(response.data);
				});
			} else {
				$child.children('option[value!=""]').remove();
				renderOptions(opts.dataSource);
			}
		});
		if (opts.autoTrigger) {
			$parent.eq(0).trigger('change');
		}
	};
}());
