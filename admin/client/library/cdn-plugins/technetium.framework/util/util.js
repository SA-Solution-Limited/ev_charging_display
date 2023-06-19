/* Technetium PHP Framework version 2.8
   Author: Tony Leung
   E-mail: tony.leung@cruzium.com
   
   Util.js - v1.2.19

   Category:
   - Asynchronous resources inclusion
   - Browser event
   - Browser version detection
   - Cookie
   - Form functions
   - Legacy browser support
   - Number formating
   - String function
   - Validation
   - Window location
   - Helper
*/

'use strict';

window.Util = {
	/* Asynchronous resources inclusion */
	includeCSS: function(src, media, insertBefore) {
		media = media || 'screen';
		if (src.constructor != Array) {
			src = [src];
		}
		var nodes = src.map(function(href) {
			var node = document.createElement('link');
			node.rel = 'stylesheet';
			node.type = 'text/css';
			node.media = media;
			node.href = href;
			return(node);
		});
		if (insertBefore) {
			var refNode = document.getElementById(insertBefore);
			if (refNode) {
				nodes.forEach(function(node) {
					refNode.parentNode.insertBefore(node, refNode);
				});
				return;
			}
		}
		var _head = document.getElementsByTagName('head')[0];
		nodes.forEach(function(node) {
			_head.appendChild(node);
		});
	},
	includeJS: function(src, callback) {
		if (src == null) {
			return;
		}
		
		var append = function(s) {
			var node = document.createElement('script');
			node.type = 'text/javascript';
			node.async = true;
			node.src = s;
			var s = document.getElementsByTagName('script');
			s = s[s.length-1];
			s.parentNode.insertBefore(node, s.nextSibling);
			return(node);
		};

		if (src.constructor == Array) {
			var node = append(src.shift());
			if (src.length > 0) {
				node.onload = function() {
					Util.includeJS(src, callback);
				};	
			} else if (typeof(callback) == 'function') {
				node.onload = callback;
			}
		} else {
			var node = append(src);
			if (typeof(callback) == 'function') {
				node.onload = callback;
			}
		}
	},
	writeCSS: function(id, css) {
		var node = document.getElementById(id + '-css');
		if (!node) {
			node = document.createElement('style');
			node.setAttribute('id', id + '-css');
			node.setAttribute('type', 'text/css');
			document.getElementsByTagName('head')[0].appendChild(node);
		}
		if (node.styleSheet) { // IE
			node.styleSheet.cssText += css;
		} else { // the world
			node.appendChild(document.createTextNode(css));
		}
	},
	
	/* Browser event */
	addOnloadEvent: function(funct, isPrepend) {
		var onload = window.onload;
		if (typeof(onload) == 'function') {
			window.onload = function() {
				if (isPrepend) funct();
				onload();
				if (!isPrepend) funct();
			};
		} else {
			window.onload = function() {
				funct();
			};
		}
	},
	
	/* Browser version detection */
	isIE: function(v) {
		return(navigator.userAgent.match(/MSIE|Trident/i) && (v == null || v <= navigator.userAgent.match(/(?:MSIE|rv:) ?(\d+)/i)[1]));
	},
	isMobile: function() {
		return(!!navigator.userAgent.match(/(ipad|iphone|ipod|android|iemobile|opera mini|blackberry|pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i));
	},
	isWebView: function() {
		var useragent = navigator.userAgent;
		var rules = ['WebView','(iPhone|iPod|iPad)(?!.*Safari\/)','Android.*(wv|\.0\.0\.0)'];
		var regex = new RegExp('(' + rules.join('|') + ')', 'ig');
		return Boolean(useragent.match(regex));
	},
	
	/* Cookie */
	getCookie: function(key, default_) {
		if (default_ == null) default_ = '';
		key = key.replace(/[\[]/, '\\\[').replace(/[\]]/, '\\\]');
		var regex = new RegExp(key + '=([^;]*)');
		var ck = regex.exec(document.cookie);
		return(ck == null ? default_ : ck[1]);
	},
	setCookie: function(key, value, expires, path) {
		if (key == null || value == null) return;
		var array = new Array();
		array.push(key + '=' + escape(value));
		if (expires == null) {
			array.push('expires=');
		} else {
			array.push('expires=' + new Date(new Date().getTime() + (expires * 1000)).toUTCString());
		}
		if (path == null) path = '/';
		array.push('path=' + path);
		document.cookie = array.join(';');
	},
	deleteCookie: function(key, path) {
		if (Util.getCookie(key)) {
			if (path == null) path = '/';
			document.cookie = key + '=;expires=Thu, 01-Jan-1970 00:00:01 GMT;path=' + path;
		}
	},
	
	/* Data binding */
	bindData: function(data, container, evtNamespace) {
		var $container = $(container || 'body');
		var evt = 'databind' + (evtNamespace ? '.' + evtNamespace : '');
		
		for (var i in data) {
			var row = data[i], id = '', label = '-';
			if (row != null) {
				if (typeof(row) != 'object') {
					// key: label
					id    = row;
					label = row;
				} else if (row.constructor == Object) {
					if (row.id !== undefined && row.label !== undefined) {
						// key: {id: id, label: label}
						id    = row.id;
						label = row.label;
					} else {
						// key: {foo: value, bar: value}
						id    = row;
						label = JSON.stringify(row);
					}
				} else if (row.constructor == Array) {
					if (row.length == 0) {
						// key: []
						id    = [];
					} else if (typeof(row[0]) != 'object') {
						// key: [label1]
						id    = row;
						label = row;
					} else {
						// key: [{id: id1, label: label1}]
						id    = row.map(function(obj, idx) {
							return(obj.id ? obj.id : '');
						});
						label = row.map(function(obj, idx) {
							return(obj.label ? obj.label : '-');
						});
					}
				}
			}
			
			// bind data to inputs
			var selector = ':input[name="'+i+'"]:not(:file), :input[name^="'+i+'["][name$="]"]:not(:file)';
			var bindToInputs = function($els) {
				var name = $els.attr('name');
				var matches = name.match(/\[.+?\]/g);
				if (matches) {
					var value = Util.getNestedProperties(id, matches.map(function(part) {
						return(part.replace(/^\[/, '').replace(/\]$/, ''));
					}).join('.'));
				} else {
					var value = id;
				}
				if (value == null) return;
				if ($els.is(':radio')) {
					$els.val() == value && $els.prop({checked: true});
				} else if ($els.is(':checkbox')) {
					// accepts array or comma-separated value
					if (typeof(value) != 'object') {
						value = (value + '').split(',');
					}
					$.inArray($els.val(), value) > -1 && $els.prop({checked: true});
				} else {
					$els.val(value);
				}
				$els.trigger(evt, {id: id});
			};
			$container.filter(selector).each(function() {
				bindToInputs($(this));
			});
			$(selector, $container).each(function() {
				bindToInputs($(this));
			});
			
			// bind data to elements
			var selector = '[data-bind="'+i+'"], [data-bind-html="'+i+'"]';
			if (label.constructor == Array) {
				label = label.join(', ');
			}
			var bindData = function($els) {
				if ($els[0].value !== undefined) {
					$els.val(id);
				} else {
					var isHtml = $els.is('[data-bind-html]');
					var prefix = $els.data('bind-prefix') || '';
					var suffix = $els.data('bind-suffix') || '';
					$els.html(prefix + (isHtml ? label : label.toString().htmlspecialchars_decode().htmlspecialchars().nl2br()) + suffix);
				}
				$els.trigger(evt, {id: id, label: label});
			};
			$container.filter(selector).each(function() {
				bindData($(this));
			});
			$(selector, $container).each(function() {
				bindData($(this));
			});
			
			// bind data to attributes (e.g. src:path_to_image;title:label)
			var selector = '[data-bind-attr*=":'+i+'"]';
			var bindData = function($els) {
				var prefix = $els.data('bind-attr-prefix') || '';
				var suffix = $els.data('bind-attr-suffix') || '';
				$.each($els.data('bind-attr').split(';'), function(idx, mapping) {
					if (mapping.indexOf(':') == -1 || mapping.split(':')[1].trim() != i) return;
					var key = mapping.split(':')[0].trim();
					var attr = {};
					attr[key] = prefix + id + suffix;
					$els.attr(attr).trigger(evt, {id: id});
					return(false);
				});
			};
			$container.filter(selector).each(function() {
				bindData($(this));
			});
			$(selector, $container).each(function() {
				bindData($(this));
			});
			
			// bind data to comparisons
			var selector = '[data-compare*="['+i+']"], [data-compare-remove*="['+i+']"]';
			var bindComparisons = function($els) {
				var isRemove = $els.is('[data-compare-remove]');
				var stmt = $els.data(isRemove ? 'compare-remove' : 'compare');
				stmt = stmt.replace(/\[(.+?)\]/g, 'data["$1"]');
				try {
					if (eval(stmt)) {
						$els.show().find(':input').prop({disabled: false});
					} else {
						$els[isRemove ? 'remove' : 'hide']().find(':input').prop({disabled: true});
					}
					$els.trigger(evt);
				} catch(e) {
					console.error('Invalid statement ' + $els.data('compare'));
				}
			};
			$container.filter(selector).each(function() {
				bindComparisons($(this));
			});
			$(selector, $container).each(function() {
				bindComparisons($(this));
			});
		}
	},
	
	/* Legacy browser support */
	enableHTML5: function() {
		var ele = ['article', 'aside', 'audio', 'bdi', 'canvas', 'command', 'datalist', 'details', 'embed', 'figcaption', 'figure', 'footer', 'header', 'hgroup', 'keygen', 'mark', 'meter', 'nav', 'output', 'progress', 'rp', 'rt', 'ruby', 'section', 'source', 'summary', 'time', 'track', 'video', 'wbr'];
		for (var i=0; i<ele.length; i++) {
			document.createElement(ele[i]);
		}
	},
	
	/* Number formating */
	formatFileSize: function(value, precision) {
		if (!precision || precision < 0) precision = 0;
		var unit = ['B', 'KB', 'MB', 'GB', 'TB'];
		var i = 0;
		while (value/1024 > 1 && i < unit.length) {
			value /= 1024;
			i++;
		}
		return(Math.round(value*Math.pow(10, precision))/Math.pow(10, precision)+' '+unit[i]);
	},
	
	/* String function */
	escapeRegExpComponent: function(value) {
		return(value.replace(/([\.\+\?\*\(\)\{\}\[\]\^\$\/])/g, '\\$1'));
	},
	unescapeRegExpComponent: function(value, char) {
		return(value.replace(/\\([\.\+\?\*\(\)\{\}\[\]\^\$\/])/g, '$1'));
	},
	generateUid: function(length, exclude) {
		if (!length || length < 0) {
			length = 12;
		}
		if (!exclude || exclude.constructor !== Array) {
			exclude = [];
		}
		var char = 'abcdefghijklmnopqrstuvwxyz0123456789';
		do {
			var uid = '';
			while (uid.length < length) {
				uid += char[Math.floor(Math.random() * char.length)];
			}
		} while (exclude.indexOf(uid) > -1);
		return(uid);
	},
	
	/* Validation */
	isValidCurrency: function(value) {
		return(!!value.match(/^(AED|AFN|ALL|AMD|AOA|ARS|AUD|AWG|AZN|BAM|BBD|BDT|BGN|BHD|BIF|BMD|BND|BOB|BRL|BSD|BTN|BWP|BYR|BZD|CAD|CDF|CHF|CLP|CNY|COP|CRC|CUP|CVE|CZK|DJF|DKK|DOP|DZD|ECS|EGP|ERN|ETB|EUR|FJD|FKP|GBP|GEL|GGP|GHS|GIP|GMD|GNF|GTQ|GWP|GYD|HKD|HNL|HRK|HTG|HUF|IDR|ILS|IMP|INR|IQD|IRR|ISK|JEP|JMD|JOD|JPY|KES|KGS|KHR|KMF|KPW|KRW|KWD|KYD|KZT|LAK|LBP|LKR|LRD|LSL|LTL|LVL|LYD|MAD|MDL|MGA|MKD|MMK|MNT|MOP|MRO|MUR|MVR|MWK|MXN|MYR|MZN|NAD|NGN|NIO|NOK|NPR|NZD|OMR|PAB|PEN|PGK|PHP|PKR|PLN|PYG|QAR|RON|RSD|RUB|RWF|SAR|SBD|SCR|SDG|SEK|SGD|SHP|SLL|SOS|SRD|SSP|STD|SVC|SYP|SZL|THB|TJS|TMT|TND|TOP|TRY|TTD|TVD|TWD|TZS|UAH|UGX|USD|UYU|UZS|VEF|VND|VUV|WST|XAF|XCD|XOF|XPF|YER|ZAR|ZMW|ZWD)$/i));
	},
	isValidContact: function(value) {
		return(!!value.match(/^(\(\d+\)|\+\d+|\d*)[\d ]+\d+$/i));
	},
	isValidEmail: function(value) {
		return(!!value.match(/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/i));
	},
	
	/* Window location */
	getOrigin: function() {
		var l = window.location;
		return(l.origin ? l.origin : l.protocol + '//' + l.hostname + (l.port ? ':' + l.port : ''));
	},
	getQueryString: function(key, default_) {
		if (default_ == null) default_ = '';
		if (window.URLSearchParams) {
			return(new URLSearchParams(window.location.search).get(key) || default_);
		} else {
			key = key.replace(/[\[]/, '\\\[').replace(/[\]]/, '\\\]');
			var regex = new RegExp('[\\?&]' + key + '=([^&#]*)');
			var qs = regex.exec(window.location.href);
			return(qs == null ? default_ : qs[1]);
		}
	},
	addQueryString: function(obj, original) {
		if (obj == null || obj.constructor != Object) return(false);
		if (!original || !original.match(/^\?/)) {
			original = window.location.search;
		}
		var params = new Object();
		if (original.length > 1) {
			var string = original.substr(1).split('&');
			for (var i = 0; i < string.length; i++) {
				var pieces = string[i].split('=');
				if (pieces[0].match(/\[\]$/)) {
					var key = pieces[0].replace(/\[\]$/, '');
					if (typeof(params[key]) != 'object') {
						params[key] = [];
					}
					params[key].push(pieces[1]);
				} else {
					params[pieces[0]] = pieces[1];
				}
			}
		}
		for (var key in obj) {
			params[key] = obj[key];
		}
		var string = [];
		for (var key in params) {
			if (params[key] == null) continue;
			switch (params[key].constructor) {
				case Array:
					string.push.apply(string, params[key].map(function(value) {
						return(key + '[]=' + value);
					}));
				break;
				case Object:
					for (var i in params[key]) {
						string.push(key + '[' + i + ']=' + params[key][i]);
					}
				break;
				default:
					string.push(key + '=' + params[key]);
			}
		}
		return(string.length ? '?' + string.join('&') : '');
	},
	parseUrl: function(url) {
		var a = document.createElement('a');
		a.href = url;
		return({
			protocol: a.protocol,
			host: a.host.replace(/\:\d+$/, ''),
			port: a.port,
			pathname: a.pathname.match(/^\//) ? a.pathname : ('/' + a.pathname),
			search: a.search,
			hash: a.hash
		});
	},
	redirect: function(url, type) {
		var fn = ['assign', 'replace', 'reload'].find(function(v) {
			return(v == type);
		}) || 'assign';
		return(window.location[fn](url));
	},
	
	/* Helpers */
	isFunction: function(fn) {
		return(typeof(fn) == 'function');
	},
	getNestedProperties: function(obj, path, _default) {
		if (typeof(obj) != 'object' || !path) return(null);
		path = path.split('.');
		for (var i = 0; i < path.length; i++) {
			if (obj && obj.hasOwnProperty(path[i])) {
				obj = obj[path[i]];
			} else {
				return(_default);
			}
		}
		return(obj);
	},
	hasNestedProperties: function(obj, path) {
		return(Util.getNestedProperties(obj, path) != null);
	},
	initiateCountdown: function(time, continueCallback, timeoutCallback) {
		Util.isFunction(continueCallback) && continueCallback(time);
		setTimeout(function() {
			if (--time > 0) {
				Util.initiateCountdown(time, continueCallback, timeoutCallback);
			} else {
				Util.isFunction(timeoutCallback) && timeoutCallback();
			}
		}, 1000);
	}
};

(function() {
	if (Util.isIE(8)) {
		Util.enableHTML5();
	}
	if (!document.getElementById('util-css')) {
		Util.writeCSS('util', '[data-compare], [data-compare-remove] {display:none;}');
	}
	if (!Array.prototype.filter) {
		Array.prototype.filter = function(func, thisArg) {
			if (!((typeof func === 'Function' || typeof func === 'function') && this)) {
				throw new TypeError();
			}
			var len = this.length >>> 0, res = new Array(len), t = this, c = 0, i = -1;
			if (thisArg === undefined) {
				while (++i !== len) {
					if (i in this) {
						if (func(t[i], i, t)) {
							res[c++] = t[i];
						}
					}
				}
			} else {
				while (++i !== len) {
					if (i in this) {
						if (func.call(thisArg, t[i], i, t)) {
							res[c++] = t[i];
						}
					}
				}
			}
			res.length = c;
			return(res);
		};
	}
	if (!Array.prototype.forEach) {
		Array.prototype.forEach = function(callback, thisArg) {
			if (this == null) {
				throw new TypeError('Array.prototype.forEach called on null or undefined');
			}
			var T, k, O = Object(this);
			var len = O.length >>> 0;
			if (typeof callback !== "function") {
				throw new TypeError(callback + ' is not a function');
			}
			if (arguments.length > 1) {
				T = thisArg;
			}
			k = 0;
			while (k < len) {
				var kValue;
				if (k in O) {
					kValue = O[k];
					callback.call(T, kValue, k, O);
				}
				k++;
			}
		};
	}
	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = function(obj) {
			for (var i = 0; i < this.length; i++) {
				if (this[i] == obj) {
					return(i);
				}
			}
			return(-1);
		};
	}
	if (!Array.prototype.map) {
		Array.prototype.map = function(callback) {
			var T, A, k;
			if (this == null) {
				throw new TypeError('this is null or not defined');
			}
			var O = Object(this);
			var len = O.length >>> 0;
			if (typeof callback !== 'function') {
				throw new TypeError(callback + ' is not a function');
			}
			if (arguments.length > 1) {
				T = arguments[1];
			}
			A = new Array(len);
			k = 0;
			while (k < len) {
				var kValue, mappedValue;
				if (k in O) {
					kValue = O[k];
					mappedValue = callback.call(T, kValue, k, O);
					A[k] = mappedValue;
				}
				k++;
			}
			return A;
		};
	}
	if (!Array.prototype.sum) {
		Array.prototype.sum = function() {
			return(this.reduce(function(a, b){
				return(a + (isNaN(b) ? 0 : b));
			}, 0));
		};
	}
	if (!Array.prototype.unique) {
		Array.prototype.unique = function() {
			return(this.filter(function(value, idx, self) {
				return(self.indexOf(value) === idx);
			}));
		};
	}
	if (!String.prototype.htmlspecialchars) {
		String.prototype.htmlspecialchars = function() {
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;'
			};
			return(this.replace(/[&<>"]/g, function(m) {
				return(map[m]);
			}));
		};
	}
	if (!String.prototype.htmlspecialchars_decode) {
		String.prototype.htmlspecialchars_decode = function() {
			var map = {
				'&amp;': '&',
				'&lt;': '<',
				'&gt;': '>',
				'&quot;': '"'
			};
			return(this.replace(/(&amp;|&lt;|&gt;|&quot;|&#39;)/g, function(m) {
				return(map[m]);
			}));
		};
	}
	if (!String.prototype.nl2br) {
		String.prototype.nl2br = function() {
			return((this + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2'));
		};
	}
	if (!String.prototype.padStart) {
		String.prototype.padStart = function padStart(targetLength, padString) {
			targetLength = targetLength >> 0; //truncate if number or convert non-number to 0;
			padString = String((typeof padString !== 'undefined' ? padString : ' '));
			if (this.length > targetLength) {
				return String(this);
			} else {
				targetLength = targetLength-this.length;
				if (targetLength > padString.length) {
					padString += padString.repeat(targetLength/padString.length); //append to original to ensure we are longer than needed
				}
				return padString.slice(0,targetLength) + String(this);
			}
		};
	}
	if (!String.prototype.trimToFit) {
		String.prototype.trimToFit = function(width, placeholder) {
			if (!width || isNaN(width)) return(this.toString());
			placeholder = document.querySelector(placeholder ? placeholder : 'body');
			if (!placeholder) return(this.toString());
			var str = this.replace(/\s/g, ' ');
			var node = document.createElement('div');
			node.innerHTML = str;
			node.style.position = 'absolute';
			placeholder.appendChild(node);
			while (node.offsetWidth > width && str.length > 0) {
				var idx = str.lastIndexOf(' ');
				str = str.substring(0, idx > -1 ? idx : str.length - 1);
				node.innerHTML = str + '...';
			}
			placeholder.removeChild(node);
			return(str.length == 0 ? this.toString() : (str == this ? str : str + '...'));
		};
	}
	if (!String.prototype.toCamelCase) {
		String.prototype.toCamelCase = function() {
			var s = this.replace(/[-_\s.]+(.)?/g, function(_, c) {
				return(c ? c.toUpperCase() : '');
			});
			return(s.substr(0, 1).toLowerCase() + s.substr(1));
		};
	}
	if (!String.prototype.toKebabCase) {
		String.prototype.toKebabCase = function() {
			return(this.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/[\s_]+/g, '-').toLowerCase());
		};
	}
	if (!String.prototype.toPascalCase) {
		String.prototype.toPascalCase = function() {
			var s = this.replace(/[-_\s.]+(.)?/g, function(_, c) {
				return(c ? c.toUpperCase() : '');
			});
			return(s.substr(0, 1).toUpperCase() + s.substr(1));
		};
	}
	if (!String.prototype.toSnakeCase) {
		String.prototype.toSnakeCase = function() {
			return(this.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/[\s-]+/g, '_').toLowerCase());
		};
	}
	if (!String.prototype.ucwords) {
		String.prototype.ucwords = function() {
			return((this + '').replace(/^(.)|\s+(.)/g, function($1) {
				return($1.toUpperCase());
			}));
		};
	}
	if (!Number.prototype.map) {
		Number.prototype.map = function(in_min, in_max, out_min, out_max) {
			return((this - in_min) * (out_max - out_min) / (in_max - in_min) + out_min);
		};
	}
	Math.log10 = Math.log10 || function(x) {
		return(Math.log(x) * Math.LOG10E);
	};
	Math.degToRad = function(rad) {
		return(rad*Math.PI/180);
	};
	Math.radToDeg = function(deg) {
		return(deg*180/Math.PI);
	};
	Math.fibonacci = function(index) {
		if (index <= 0) return(0);
		var array = [0, 1];
		for (var i=array.length; i<index; i++) {
			array.push(array[array.length-2]+array[array.length-1]);
		}
		return(array[array.length-1]);
	};
	Math.gaussianRandom = function(m, sd) {
		if (sd < 0) return(false);
		var x = Math.random()*2-1 + Math.random()*2-1 + Math.random()*2-1;
		return(x*sd+m);
	};
})();
