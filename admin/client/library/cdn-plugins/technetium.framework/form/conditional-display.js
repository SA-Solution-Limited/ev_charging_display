/* Technetium PHP Framework
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Conditional-Display.js - v0.1.2.1
   
   Dependencies:
   jQuery - http://jquery.com/
*/

(function() {
	'use strict';
	
	if (!window.jQuery) {
		console.error('jQuery is required for conditional-display.js.');
		return;
	}
	
	window.ConditionalDisplay = window.ConditionalDisplay || function(fieldName, conditions, opts) {
		// mixed fieldNames: string or array of field name(s)
		// mixed conditions: object (AND case) or array of objects (OR case) stating display conditions
		
		var _t = this;
		_t.fieldNames = fieldName.constructor == Array ? fieldName : [fieldName];
		_t.conditions = conditions;
		
		opts = $.extend(true, {
			toggleElement: '.input-field'
		}, opts);
		
		var helper = {
			getInput: function(name) {
				return($('[name="' + name + '"]'));
			},
			getInputValue: function(name) {
				var $input = helper.getInput(name);
				if ($input.is(':radio')) {
					return($input.filter(':checked').val());
				} else if ($input.is(':checkbox')) {
					return($input.filter(':checked').map(function(i, elem) {
						return(elem.value);
					}).get());
				} else {
					return($input.val());
				}
			},
			loop: function(obj, callback) {
				var loopObject = function(obj, callback) {
					for (var i in obj) {
						callback(i, obj[i]);
					}
				};
				var loopArray = function(array, callback) {
					array.forEach(function(elem, i) {
						if (elem.constructor == Object) {
							loopObject(elem, callback);
						} else if (elem.constructor == Array) {
							loopArray(elem, callback);
						}
					});
				};
				
				if (obj.constructor == Object) {
					loopObject(obj, callback);
				} else if (obj.constructor == Array) {
					loopArray(obj, callback);
				}
			},
			compare: function(cond) {
				var compareObject = function(cond) {
					var matched = true;
					for (var i in cond) {
						var val = helper.getInputValue(i);
						if (cond[i].constructor == Array) {
							if (val !== undefined && val !== null && val.constructor == Array) {
								matched = matched && cond[i].filter(function(elem1) {
									return(val.findIndex(function(elem2) {
										return(elem1 == elem2);
									}) > -1);
								}).length > 0;
							} else {
								matched = matched && cond[i].findIndex(function(elem) {
									return(elem == val);
								}) > -1;
							}
						} else {
							if (val !== undefined && val !== null && val.constructor == Array) {
								matched = matched && val.findIndex(function(elem) {
									return(elem == cond[i]);
								}) > -1;
							} else {
								matched = matched && cond[i] == val;
							}
						}
					}
					return(matched);
				};
				var compareArray = function(cond) {
					return(cond.map(function(cond) {
						if (cond.constructor == Object) {
							return(compareObject(cond));
						} else if (conditions.constructor == Array) {
							return(compareArray(cond));
						} else {
							return(false);
						}
					}).filter(function(elem) {
						return(!!elem);
					}).length);
				};
				
				if (cond.constructor == Object) { // AND case
					return(compareObject(cond));
				} else if (cond.constructor == Array) { // OR case
					return(compareArray(cond));
				} else { // unknown
					return(false);
				}
			}
		};
		
		helper.loop(_t.conditions, function(key, value) {
			helper.getInput(key).on('change', function() {
				var result = helper.compare(_t.conditions);
				_t.fieldNames.forEach(function(name) {
					var $input = helper.getInput(name);
					if (result) {
						$input.prop({disabled: false}).closest(opts.toggleElement).show();
					} else {
						$input.prop({disabled: true}).closest(opts.toggleElement).hide();
					}
					$input.trigger('change.condition');
				});
			}).trigger('change');
		});
	};
	
})();
