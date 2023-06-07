/* A simple PHP AJAX Toolkit */

var a4p = {

prefix: '',
state: '',
serial: '',

busy_func: function() {},

idle_func: function() {},

init: function (prefix, state) {
	this.prefix = prefix;
	this.state = state;
	this.serial = a4p_sec.randomString(32);
},

newevent: function () {
	var type = function() {};
	type.prototype = a4p_event;
	var obj = new type;
	return obj;
},

invoke: function(method, param) {
	var type = function() {};
	type.prototype = a4p_action;
	var obj = new type;
	obj._target = this;
	obj._method = method;
	obj._param = param;
	return obj;
},

_ajaxPoll: function (poll_id, pos, feed) {
	jQuery.ajax({
		url: this.prefix + '/poll.php',
		type: 'POST',
		data: { poll_id: poll_id, pos: pos, feed: feed, time: (new Date()).getTime() },
		success: function(response) {
			if (response != '@END@') {
				feed = eval(response);
				setTimeout(function() { a4p._ajaxPoll(poll_id, pos + response.length, response.length > 0 ? '' + feed : ''); }, 100);
			}
		}
	});
},

ajaxCall: function (arg) {
	return this._ajaxCall(arg.method, arg.param, arg.formname, arg.rerender, arg.push);
},

_ajaxCall: function (method, param, formname, rerender, push) {
	if (typeof param == 'undefined')
		param = '';
	var target = this;
	var event = this.newevent();
	var poll_id = '';
	var poll_str = '';
	if (push == true) {
		poll_id = a4p_sec.randomString(32);
		setTimeout(function() { a4p._ajaxPoll(poll_id, 0, ''); }, 100);
	}
	a4p.busy_func();
	jQuery.ajax({
		url: this.prefix + '/ajaxcall.php',
		type: 'POST',
		data: {
			state: this.state,
			method: method,
			param: jQuery.toJSON(param),
			poll_id: poll_id,
			serial: this.serial,
			call: false,
			form: jQuery('#' + formname).serialize(),
			time: (new Date()).getTime()
		},
		success: function(response) {
			a4p.ajaxResponse(response, target, rerender, event);
		}
	});
	return event;
},

ajaxResponse: function (response, target, rerender, event) {
	var data = null;
	if (response.startsWith('$'))
		window.location = response.substring(1);
	else if (response.startsWith('#'))
		eval(response.substring(1));
	else if (response.startsWith('@'))
		data = eval('(' + response.substring(1) + ')');
	else
		document.body.innerHTML = response;

	if (typeof rerender == 'string' && rerender.length > 0)
		target._ajaxRerender(rerender, event, data);
	else {
		event._onComplete(data);
		a4p.idle_func();		
	}
},

ajaxRerender: function (id) {
	var event = this.newevent();
	this._ajaxRerender(id, event, null);
	return event;
},

_ajaxRerender: function (id, event, data) {
	jQuery.ajax({
		url: this.prefix + '/rerender.php',
		type: 'POST',
		data: {	
			state: this.state,
			id: id,
			serial: this.serial,
			time: (new Date()).getTime() },
		success: function (response) {
			if (response.startsWith('@')) {
				if (response.length > 1)
					a4p.ajaxDisplay(response.substring(1), id);
			}
			else
				document.body.innerHTML = response;
			event._onComplete(data);
			a4p.idle_func();		
		}
	});
},

onBusy: function (func) {
	a4p.busy_func = func;
},

onIdle: function (func) {
	a4p.idle_func = func;
},

setInnerHTML: function (element, html) {
	jQuery(element).replaceWith(html);
	if (typeof layout != 'undefined')
		layout.resize();
},

ajaxDisplay: function (response, id) {
	if (response != '')	{
		var contents = this.JSONDecode(response);
		for (var id in contents) {
			var element = document.getElementById(id);
			this.setInnerHTML(element, contents[id]);
		}
	}
},

JSONDecode: function (json) {
	return eval('(' + json + ')');
},

JSONEncode: function (obj) {
	return jQuery.toJSON(obj);
},

phpCall: function (arg) {
	return this._phpCall(arg.method, arg.param, arg.formname);
},

_phpCall: function (method, param, formname) {
	var result = '';
	if (typeof param == 'undefined')
		param = '';
	jQuery.ajax({
		url: this.prefix + '/ajaxcall.php?' + (new Date()).getTime(),
		type: 'POST',
		data: {
			state: this.state,
			method: method,
			param: jQuery.toJSON(param),
			serial: this.serial,
			call: true,
			form: jQuery('#' + formname).serialize(),
			time: (new Date()).getTime()
		},
		async: false,
		success: function (response) {
			if (response.startsWith('@')) {
				if (response.length > 1)
					result = eval('(' + response.substring(1) + ')');
			}
			else
				document.body.innerHTML = response;
		}
	});
	return result;
},

action: function (arg) {
	return this.ajaxCall(arg);
},

rerender: function (arg) {
	return this.ajaxRerender(arg);
},

call: function (arg) {
	return this.phpCall(arg);
},

get: function (url) {
	var result = '';
	jQuery.ajax({
		url: url,
		type: 'GET',
		async: false,
		success: function (response) {
			result = response;
		}
	});
	return result;
}

};

var a4p_event = {

_onComplete: function () {},

_onLoad: function () {},

_onClose: function () {},

onComplete: function (f) {
	this._onComplete = f;
	return this;
},

onLoad: function (f) {
	this._onLoad = f;
	return this;
},

onClose: function (f) {
	this._onClose = f;
	return this;
}

};

var a4p_action = {

_target: a4p,
_method: '',
_param: '',
_rerender: '',
_form: '',
_push: false,

rerender: function(rerender) {
	this._rerender = rerender;
	return this;
},

submit: function(form) {
	this._form = form;
	return this;
},

callback: function() {
	this._push = true;
	return this;
},

nowait: function() {
	return this._target.ajaxCall({ method: this._method, param: this._param, formname: this._form, rerender: this._rerender, push: this._push });
},

wait: function() {
	return this._target.phpCall({ method: this._method, param: this._param, formname: this._form });
}

};


var a4p_sec = {

alphabet: 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789',

randomString: function (length) {
	var pass = '';
	var alphaLength = this.alphabet.length;
	for (var i = 0; i < length; i++) {
		var n = Math.floor(Math.random() * alphaLength);
		pass = pass + this.alphabet.charAt(n);
	}
	return pass;
}

};

if (typeof String.prototype.startsWith != 'function') {
	String.prototype.startsWith = function (str) {
		return this.slice(0, str.length) == str;
	};
}

if (typeof String.prototype.endsWith != 'function') {
	String.prototype.endsWith = function (str) {
		return this.slice(-str.length) == str;
	};
}
