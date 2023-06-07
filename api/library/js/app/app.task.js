/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

app.factory('$task', ['$interval', function($interval) {
	
	var tasks = {};

	return({
		register: function(name, fn, interval, $executeOnRegister) {
			if (tasks[name]) return;
			tasks[name] = {
				fn: fn,
				interval: interval
			};
			if ($executeOnRegister) {
				fn();
			}
			if (typeof(interval) == 'function') {
				var callback = function() {
					fn();
					$interval(callback, interval() * 1000, 1);
				};
				callback();
			} else {
				$interval(fn, interval * 1000);
			}
		}
	});
}]);
