/* Technetium PHP Framework version 2.8
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Table-Expandable.js - v1.0.1
   Hide certain columns of a wide table and expand it only when necessary
   Note: Row span and column span must not be used in the table
*/

'use strict';

window.TableExpandable = function(els, opts) {

	var _d = {
		collapse: true,
		hiddenCol: [], // accepts cell index or jQuery selectors
		hiddenColClass: 'expandable-mark',
		togglerClass: 'expandable-toggle'
	};

	var $els = $(els).eq(0);
	var $cursor, $toolbar;
	
	var init = function () {
		opts = $.extend({}, _d, opts);
		if ($els.data('hidden-col')) {
			opts.hiddenCol = $els.data('hidden-col').split(',');
		}
		
		// mark columns to hide
		helper.markColumns();
		
		// setup cursor
		if ($els.parent('.table-responsive').size() > 0) {
			$cursor = $els.parent('.table-responsive');
		} else {
			$cursor = $els;
		}
		$toolbar = $cursor.siblings('.table-toolbar');
		
		// hide columns
		if (opts.collapse) {
			col.collapse();
		}
		
		// bind events
		$toolbar.on('click', '.'+opts.togglerClass, function(e) {
			e.preventDefault();
			col.toggle();
		});
		
		return({
			expand: col.expand,
			collapse: col.collapse,
			toggle: col.toggle,
			reload: reload
		});
	};
	
	var helper = {
		markColumns: function() {
			$els.find('tr').each(function() {
				for (var i = 0; i < opts.hiddenCol.length; i++) {
					if (!isNaN(opts.hiddenCol[i])) {
						$(this).children(':eq('+opts.hiddenCol[i]+')').addClass(opts.hiddenColClass);
					} else {
						$(this).children(opts.hiddenCol[i]).addClass(opts.hiddenColClass);
					}
				}
			});
		}
	};
	
	var col = {
		expand: function() {
			$els.find('.'+opts.hiddenColClass).show();
			$toolbar.find('.'+opts.togglerClass).find('.fa-expand').removeClass('fa-expand').addClass('fa-compress');
			col.status = 1;
		},
		collapse: function() {
			$els.find('.'+opts.hiddenColClass).hide();
			$toolbar.find('.'+opts.togglerClass).find('.fa-compress').removeClass('fa-compress').addClass('fa-expand');
			col.status = 0;
		},
		toggle: function() {
			if (col.status) {
				col.collapse();
			} else {
				col.expand();
			}
		},
		status: 1
	};
	
	var reload = function() {
		helper.markColumns();
		if (col.status) {
			col.expand();
		} else {
			col.collapse();
		}
	};

	return (init());
};
