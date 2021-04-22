const MIME_APPLICATION_JSON = "application/json";
const MIME_TEXT_HTML = "text/html";


const HttpRequest = (function(){


	var defaultHeaders = {
		"Accept":"application/javascript"
	};


	var _HttpRequest = {
		url: null,
	
		body: null,
	
		sent: false,

		method: "GET",
	
		params: {
	
		},
	
		headers: {
	
		},
		
		_synthetic: false,
	  
	  setContent:function(contentType){
	  	this.headers["Content-Type"] = contentType;
	  },

	  setMethod:function(method) {
		this.method = method;
	  },
	  
		newRequest: function() {
			headreq = new Headers();
			headreq.append('Content-Type', this.headers["Content-Type"] || MIME_APPLICATION_JSON);
			headreq.append('Accept', this.headers["Accept"] || MIME_APPLICATION_JSON);

			var init = { 
				method: this.method,
				headers: this.headers,
				mode: 'cors',
				cache: 'default'
			};
			
			if(this.body) {
				init['body'] = this.body;
			}
	
			return new Request(this.url, init);
		},
	  
	  
	  synthetic: function(bool){
	  	this._synthetic = true;
	  },
	  
	  isSynthetic: function(){
	  	return this._synthetic === true;
	  },
	  
	  
		send: function() {
			var req, resp;
			req = this.newRequest();
			
			this.sent = true;
			
			return fetch(req);
		},
		
		
		setBody: function(body){
			this.body = body;
		},
		
		getBody: function(){
			return this.body;
		},
		
		json: function(){
			return JSON.parse(this.body);
		},
		
		html: function(){
			return this.body;
		},
		
		text:function(){
			return this.body;
		}
	};


	function HttpRequest(url,init,body) {
		this.url = url || "";
		this.headers = (init && init.headers) || defaultHeaders;
		this.body = body || null;
	}
	
	HttpRequest.prototype = _HttpRequest;


	return HttpRequest;
})();