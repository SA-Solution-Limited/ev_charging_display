/* Technetium PHP Framework
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Select-All-Checkboxes.js - v0.1
   
   Dependencies:
   jQuery - http://jquery.com/
*/

(function() {
	'use strict';
	
	if (!window.jQuery) {
		console.error('jQuery is required for select-all-checkboxes.js.');
		return;
	}
	
	$(':checkbox[data-toggle=select-all]').each(function() {
		var $all = $(this);
		var $target = $('[name="' + $(this).data('target') + '"]');

		var toggleTargets = function() {
			$target.filter(':not(:disabled)').prop({checked: $all.prop('checked')});
		};
		var toggleAll = function() {
			var allChecked = $target.filter(':not(:disabled)').toArray().map(function(elem) {
				return($(elem).prop('checked'));
			}).reduce(function(a, b) {
				return(a && b);
			});
			$all.prop({checked: allChecked});
		};
		
		toggleAll();

		$all.on('change', function() {
			toggleTargets();
		});
		$target.on('change', function() {
			toggleAll();
		});
	});
}());
