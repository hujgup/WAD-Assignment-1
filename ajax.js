function validMIMEType(mime) {
	return mime.match(/^[a-z\d\-]+\/[a-z\d\-]+$/);
}
function objectHasAnyProperty(obj) {
	var res;
	if (Object.getOwnPropertyNames) {
		res = Object.getOwnPropertyNames(obj).length !== 0;
	} else {
		res = false;
		for (var item in obj) { // ECMAScript 3 fallback (won't handle enumerable = false properties on ECMAScript 5 objects)
			if (obj.hasOwnProperty(item)) {
				res = true;
				break;
			}
		}
	}
	return res;
}
function objectShallowClone(obj) {
	var res = {};
	var names;
	if (Object.getOwnPropertyNames) {
		names = Object.getOwnPropertyNames(obj);
	} else {
		names = [];
		for (var item in obj) {
			if (obj.hasOwnProperty(item)) {
				names.push(item);
				break;
			}
		}
	}
	for (var i = 0; i < names.length; i++) {
		res[names[i]] = obj[names[i]];
	}
	return res;
}
var HTTPMethods = {
	OPTIONS: "OPTIONS",
	GET: "GET",
	HEAD: "HEAD",
	POST: "POST",
	PUT: "PUT",
	DELETE: "DELETE",
	TRACE: "TRACE",
	CONNECT: "CONNECT",
	isMethod: function(value) {
		value = value.toUpperCase();
		var res = false;
		for (var item in HTTPMethods) {
			if (HTTPMethods.hasOwnProperty(item) && item !== "isMethod") {
				if (value === HTTPMethods[item]) {
					res = true;
					break;
				}
			}
		}
		return res;
	}
};
var HTTPHeaders = {
	ACCEPT: "Accept",
	ACCEPT_LANGUAGE: "Accept-Language",
	AUTHORIZATION: "Authorization",
	CACHE_CONTROL: "Cache-Control",
	CONTENT_TYPE: "Content-Type",
	FROM: "From",
	IF_MATCH: "If-Match",
	IF_MODIFIED_SINCE: "If-Modified-Since",
	IF_NONE_MATCH: "If-None-Match",
	IF_RANGE: "If-Range",
	IF_UNMOFIFIED_SINCE: "If-Unmodified-Since",
	MAX_FORWARDS: "Max-Forwards",
	PRAGMA: "Pragma",
	RANGE: "Range",
	WARNING: "Warning",
	validHeader: function(key,value) {
		var res = false;
		for (var item in HTTPHeaders) {
			if (HTTPHeaders.hasOwnProperty(item) && item !== "validate" && item !== "validHeader" && item !== "validHeaderCollection") {
				if (HTTPHeaders[item] === key) {
					res = true;
					break;
				}
			}
		}
		return res;
	},
	validHeaderCollection: function(headers) {
		var res = true;
		for (var item in headers) {
			if (headers.hasOwnProperty(item)) {
				res = HTTPHeaders.validHeader(item,headers[item]);
				if (!res) {
					break;
				}
			}
		}
		return res;
	}
};
// todo getter/setter properties, call get/set methods
var AJAXRequest = function(method,url) {
	var _method;
	var _url;
	var _headers = {};
	var _postData = {};
	if (arguments.length >= 2) {
		if (HTTPMethods.isMethod(method)) {
			if (typeof url === "string") {
				_method = method.toUpperCase();
				_url = url;
				if (arguments.length > 2) {
					var badHeaders = function() {
						throw new Error("The third argument of the AJAXRequest constructor, if present, must be an object with property keys from the HTTPHeaders enumeration");
					};
					if (typeof arguments[2] === "object") {
						if (HTTPHeaders.validHeaderCollection(arguments[2])) {
							_headers = objectShallowClone(arguments[2]);
							if (arguments.length > 3) {
								if (typeof arguments[3] === "object") {
									_postData = objectShallowClone(arguments[3]);
								} else {
									throw new Error("The fourth argument of the AJAXRequest constructor, if present, must be an object");
								}
							}
						} else {
							badHeaders();
						}
					} else {
						badHeaders();
					}
				}
				this.useAuthorization = false;
				this.authUsername = "";
				this.authPassword = "";
				// todo move this out of ctor context
				var AJAXResponse = function(status,raw,text,xml,type) {
					var setOptional = function(value) {
						return value ? value : null;
					};
					if (status === 0) {
						status = 400;
					}
					this.error = status >= 400;
					this.status = status;
					this.raw = setOptional(raw);
					this.text = setOptional(text);
					this.xml = setOptional(xml);
					this.type = setOptional(type);
					this.success = !this.error && typeof raw !== "undefined";
				};
				var compilePostData = function() {
					var str = "";
					for (var item in _postData) {
						if (_postData.hasOwnProperty(item)) {
							str += encodeURIComponent(item)+AJAXRequest._eq+encodeURIComponent(_postData[item])+AJAXRequest._amp;
						}
					}
					if (str !== "") {
						str = str.substring(0,str.length - AJAXRequest._amp.length);
					}
					return str;
				};
				this.getMethod = function() {
					return _method;
				};
				this.setMethod = function(value) {
					var res = HTTPMethods.isMethod(value);
					if (res) {
						if (_method === HTTPMethods.POST) {
							if (value !== HTTPMethods.POST) {
								_postData = {};
							}
						}
						_method = value;
					}
					return res;
				};
				this.getURL = function() {
					return _url;
				};
				this.setURL = function(value) {
					var res = typeof value === "string";
					if (res) {
						_url = value;
					}
					return res;
				};
				this.getHeader = function(key) {
					return _headers.hasOwnProperty(key) ? _headers[key] : null;
				};
				this.setHeader = function(key,value) {
					var res = HTTPHeaders.validHeader(key,value);
					if (res) {
						_headers[key] = value;
					}
					return res;
				};
				this.resetHeader = function(key) {
					if (_headers.hasOwnProperty(key)) {
						delete _headers[key];
					}
				};
				this.getAllHeaders = function() {
					return objectShallowClone(_headers);
				};
				this.setAllHeaders = function(value) {
					var res = HTTPHeaders.validHeaderCollection(value);
					if (res) {
						_headers = objectShallowClone(value);
					}
					return res;
				};
				this.getPostData = function() {
					return _method === HTTPMethods.POST ? objectShallowClone(_postData) : null;
				};
				this.setPostData = function(value) {
					var res = _method === HTTPMethods.POST;
					if (res) {
						_postData = objectShallowClone(value);
					}
					return res;
				};
				this.queue = function(callback,mime) {
					var res = typeof callback === "function";
					if (res) {
						AJAXRequest._enqueue(new QueueItem(this,callback,mime));
					}
					return res;
				};
				this.execute = function(callback,mime) {
					callback = typeof callback === "function" ? callback : function() { };
					try {
						var req = new XMLHttpRequest();
						req.open(_method,_url,true,this.authUsername,this.authPassword);
						for (var item in _headers) {
							if (_headers.hasOwnProperty(item)) {
								req.setRequestHeader(item,_headers[item]);
							}
						}
						var post = _method === HTTPMethods.POST;
						if (post) {
							req.setRequestHeader(HTTPHeaders.CONTENT_TYPE,"application/x-www-form-urlencoded");
						}
						req.onload = function() {
							if (req.status >= 400) {
								req.onerror();
							} else {
								callback(new AJAXResponse(req.status,req.response,req.responseText,req.responseXML,req.responseType));
							}
						};
						req.onerror = function() {
							callback(new AJAXResponse(req.status,req.response,req.responseText,req.responseXML,req.responseType));
						};
						req.onabort = req.onerror;
						if (this.useAuthorization) {
							req.withCredentials = true;
						}
						if (typeof mime === "string") {
							if (validMIMEType(mime)) {
								req.overrideMimeType(mime);
							} else {
								req.responseType = mime;
							}
						}
						if (post) {
							req.send(compilePostData());
						} else {
							req.send();
						}
					} catch (e) {
						callback(new AJAXResponse(0));
					}
				};
			} else {
				throw new Error("The second argument of the AJAXRequest constructor must be a string");
			}
		} else {
			throw new Error("The first argument of the AJAXRequest constructor must be in the HTTPMethods enumeration");
		}
	} else {
		throw new Error("Not enough arguments passed to the AJAXRequest constructor - a minimum of 3 is expected");
	}
};
AJAXRequest._threads = 0;
AJAXRequest._queue = [];
AJAXRequest._enqueue = function(item) {
	AJAXRequest._queue.push(item);
};
AJAXRequest._eq = "=";
AJAXRequest._amp = "&";
AJAXRequest.maximumThreads = 16;
AJAXRequest.createQueryString = function(value) {
	var res = "";
	if (objectHasAnyProperty(value)) {
		res += "?";
		for (var item in value) {
			if (value.hasOwnProperty(item)) {
				res += encodeURIComponent(item)+AJAXRequest._eq+encodeURIComponent(value[item])+AJAXRequest._amp;
			}
		}
		res = res.substring(0,res.length - AJAXRequest._amp.length);
	}
	return res;
};
var QueueItem = function(req,callback,mime) {
	this.request = req;
	this.callback = callback;
	this.mime = mime;
}

setInterval(function() {
	var item;
	while (AJAXRequest._queue.length !== 0 && AJAXRequest._threads < AJAXRequest.maximumThreads) {
		AJAXRequest._threads++;
		var item = AJAXRequest._queue.shift();
		item.request.execute(function(res) {
			AJAXRequest._threads--;
			item.callback(res);
		},item.mime);
	}
},16);

/*
var req = new AJAXRequest(HTTPMethods.GET,"global.js");
req.execute(function(res) {
	console.log(res.text);
});
*/
