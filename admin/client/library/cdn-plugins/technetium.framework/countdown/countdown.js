/* Technetium PHP Framework
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Countdown.js - v1.0
*/

'use strict';

window.Countdown = function(time, continueCallback, timeoutCallback) {
	
	var timeout;
	var counter;
	
	var api = {
		getRemainingTime: function() {
			return(counter);
		},
		terminate: function() {
			clearTimeout(timeout);
		},
		reset: function(t) {
			api.terminate();
			counter = t;
			helper.countdown(t);
		},
		restart: function() {
			api.reset(time);
		}
	};
	
	var helper = {
		countdown: function() {
			typeof(continueCallback) == 'function' && continueCallback(counter);
			timeout = setTimeout(function() {
				if (--counter > 0) {
					helper.countdown();
				} else {
					typeof(timeoutCallback) == 'function' && timeoutCallback();
				}
			}, 1000);
		}
	};
	
	api.restart();
	return(api);
};
