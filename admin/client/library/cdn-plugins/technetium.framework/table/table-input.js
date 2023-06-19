/* Technetium PHP Framework version 2.8
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Table-Input.js - v1.1.0.1
   Transform a static table to an editable table
   
   Dependencies:
   - jQuery - https://jquery.com/
   - Util.js - https://cdn.cruzium.info/technetium.framework/latest/util/util.min.js
*/

'use strict';

window.TableInput = function(els, opts) {
	
	var defaults = {
		autoDraw: true,
		maxRow: -1,
		sortable: false,
		bindEvents: true,
		escapeHTML: true
	};
	
	var $els = $(els).eq(0);
	var $cursor;
	var buffer = {};
	
	var state = {
		rowCount: 0
	};
	
	var init = function() {
		// merge options
		opts = $.extend({}, defaults, opts);
		
		// write css
		if (!document.getElementById('table-input-css')) {
			Util.writeCSS('table-input', 'tr.template {display:none !important;}');
		}
		
		// disable template inputs
		$els.find('> tbody > tr.template').find('input, select, textarea').prop({disabled: true});
		
		// define cursor
		if ($els.parent().is('.table-responsive')) {
			$cursor = $els.parent('.table-responsive');
		} else {
			$cursor = $els;
		}
		
		// bind events
		if (opts.bindEvents) {
			$cursor.siblings('.table-toolbar').on('click', '.add', function(e) {
				e.preventDefault();
				row.add();
			});
			$cursor.on('click', '.add', function(e) {
				e.preventDefault();
				row.add();
			}).on('click', '.delete', function(e) {
				e.preventDefault();
				row.Delete($(this).parents('tr:eq(0)'));
			});
		}
		
		// jquery ui sortable
		if (opts.sortable || $els.hasClass('sortable')) {
			var $sortable = $els.find('> tbody');
			var uiOpts = {
				cursor: 'move',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				handle: '.drag-handle',
				helper: 'clone',
				items: '> tr:not(.template):not(.empty)',
				tolerance: 'pointer',
			};
			if (opts.sortable.constructor == Object) {
				uiOpts = $.extend(true, uiOpts, opts.sortable);
			} else {
				opts.sortable = true;
			}
			$sortable.sortable(uiOpts);
			$sortable.on('sortupdate', function(e, ui) {
				refresh();
				$els.trigger('tableinput.row.sort');
				if (typeof(callback.obj.rowSorted) == 'function') {
					callback.obj.rowSorted();
				}
			});
			$els.on('tableinput.row.add tableinput.row.render', function(e, $tpl, id, data) {
				$sortable.sortable('refresh');
			});
		}
		
		var api = {
			row: {
				add: row.add,
				render: row.render,
				Delete: row.Delete
			},
			draw: draw,
			reset: reset,
			refresh: refresh,
			callback: {
				set: callback.set
			},
			getCursor: function() {
				return($cursor);
			},
			getTable: function() {
				return($els);
			},
			getSortable: function() {
				return(opts.sortable ? $els.find('> tbody') : $(' '));
			}
		};
		$els.data('tableinput', api);
		$els.trigger('tableinput.load');
		
		return(api);
	};
	
	var row = {
		add: function(tplId) {
			if (opts.maxRow > -1 && state.rowCount >= opts.maxRow) return;
			var id = uid.generate();
			var $tpl = row.clone(tplId);
			$tpl.attr('data-id', id);
			$tpl.find('[name*=_ID_]').each(function() {
				var name = $(this).attr('name').replace(/_ID_/, id);
				$(this).attr({name: name});
			});
			$tpl.find('[data-id]').each(function() {
				helper.bindData($(this), id);
			});
			helper.applyUniform($tpl);
			
			$els.trigger('tableinput.row.add', [$tpl, id, {}]);
			if (typeof(callback.obj.rowAdded) == 'function') {
				callback.obj.rowAdded($tpl, id);
			}
			if (++state.rowCount >= opts.maxRow && opts.maxRow > -1) {
				$cursor.siblings('.table-toolbar .add').addClass('disabled');
			}
			return($tpl);
		},
		render: function(id, data, tplId) {
			if (data === false) return;
			if ($els.find('> tbody > tr[data-id="'+id+'"]').length > 0) {
				var $tpl = $els.find('> tbody > tr[data-id="'+id+'"]');
			} else if (opts.maxRow > -1 && state.rowCount >= opts.maxRow) {
				return;
			} else {
				var $tpl = row.clone(tplId);
				if (id == null) {
					id = data.id = uid.generate();
				}
			}
			$tpl.attr('data-id', id);
			$tpl.find('[name*=_ID_]').each(function() {
				var name = $(this).attr('name').replace(/_ID_/, id);
				$(this).attr({name: name});
			});
			$tpl.find('[data-id]').each(function() {
				helper.bindData($(this), id);
			});
			Util.bindData(data, $tpl);
			helper.applyUniform($tpl);
			
			$els.trigger('tableinput.row.render', [$tpl, id, data]);
			if (typeof(callback.obj.rowRendered) == 'function') {
				callback.obj.rowRendered($tpl, id, data);
			}
			if (++state.rowCount >= opts.maxRow && opts.maxRow > -1) {
				$cursor.siblings('.table-toolbar').find('.add').addClass('disabled').prop({disabled: true});
			}
			return($tpl);
		},
		clone: function(tplId) {
			var selector = '> tbody > tr.template' + (tplId ? '[data-template="'+tplId+'"]' : '');
			var $tpl = $els.find(selector).clone();
			$tpl.removeClass('template').removeAttr('data-template').show();
			$tpl.find(':disabled').prop({disabled: false});
			$els.find('> tbody > tr.empty').hide();
			buffer['T' + new Date().getTime()] = $tpl;
			opts.autoDraw && draw();
			return($tpl);
		},
		Delete: function($row, omitConfirmation) {
			if (omitConfirmation || confirm('Are you sure to delete the entry?')) {
				$row.remove();
				if (--state.rowCount < opts.maxRow) {
					$cursor.siblings('.table-toolbar').find('.add').removeClass('disabled').prop({disabled: false});
				}
				if (state.rowCount == 0) {
					$els.find('> tbody > tr.empty').show();
				}
				$els.trigger('tableinput.row.delete');
				if (typeof(callback.obj.rowDeleted) == 'function') {
					callback.obj.rowDeleted();
				}
			}
		},
	};
	
	var draw = function() {
		for (var i in buffer) {
			buffer[i].insertBefore($els.find('> tbody > tr.template:eq(0)'));
			delete(buffer[i]);
		}
	};
	
	var reset = function() {
		$els.find('> tbody > tr:not(.empty):not(.template)').remove();
		$els.find('> tbody > tr.empty').show();
		state.rowCount = 0;
	};
	
	var refresh = function() {
		state.rowCount = $els.find('> tbody > tr:not(.empty):not(.template)').length;
		if (state.rowCount == 0) {
			$els.find('> tbody > tr.empty').show();
		} else {
			$els.find('> tbody > tr.empty').hide();
		}
	};
	
	var callback = {
		set: function(event, fn) { // deprecated
			event = event.split(' ');
			for (var i = 0; i < event.length; i++) {
				var ofn = typeof(callback.obj[event[i]]) == 'function' ? callback.obj[event[i]] : function() {};
				callback.obj[event[i]] = function($row, id, data) {
					ofn($row, id, data);
					fn($row, id, data);
				};
			}
		},
		obj: {}
	}
	
	var uid = {
		generate: function() {
			var str = '';
			var char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			while (str.length < 8) {
				str += char[Math.floor(Math.random()*char.length)];
			}
			if (str.match(/^[0-9]/) || $.inArray(str, uid.pool) > -1) {
				return(uid.generate());
			}
			uid.pool.push(str);
			return(str);
		},
		pool: []
	};
	
	var helper = {
		bindData: function(ele, value) {
			var id, label;
			if (typeof(value) == 'object' && value != null) {
				id = value.id;
				label = value.label;
			} else {
				id = label = value;
			}
			switch (true) {
				case $(ele).is(':radio'):
					if ($(ele).val() == id) {
						$(ele).prop({checked: true});
					}
					break;
				case $(ele).is(':checkbox'):
					if (typeof(id) != 'object') {
						id = (id+'').split(',');
					}
					if ($.inArray($(ele).val(), id) > -1) {
						$(ele).prop({checked: true});
					}
					break;
				case $(ele).is('button'):
					opts.escapeHTML ? $(ele).text(label) : $(ele).html(label);
					$(ele).val(id);
					break;
				default:
					if ($(ele).prop('value') === undefined) {
						opts.escapeHTML ? $(ele).text(label) : $(ele).html(label);
					} else {
						$(ele).val(id);
					}
			}
		},
		applyUniform: function(els) {
			if (jQuery().uniform) {
				$(els).find(':checkbox, :radio').uniform();
			}
		}
	};
	
	return(init());
};
