const HttpServer = (function() {


	var proto = {
	
		// Add a route to this server instance.
		addRoute: function(route) {
			this.routes[route["path"]] = route;
		},
		
		match: function(path) {
			return this.routes[path];
		},
		
		run: function(req) {
			var url = req.getRequestUri();
			var path = url.getPath();
			
			const route = this.match(this.url);
			const content = process
		},
		
		
		process: function(req) {
			const callback = route.getParams()["callback"];
			
			// Probably pass in req.body or req.params or req.querystring.
			return callback(req);
		},
		
		
		// Sends an HttpResponse to the client
		send: function(content) {
			// Setup headers, too.

			const route = this.match(this.url);
			if (typeof this.mockResponseBody === 'function') {

				this.mockResponseBody = this.mockResponseBody(params);
			}

			let body = params instanceof Error ? { error: params.message } : this.mockResponseBody;
			body = typeof body === 'object' ? JSON.stringify(body) : body;

			let status = params instanceof Error ? 500 : 200;

			return new Response(body, {
				headers: {
					'Content-Type': 'application/json',
					'X-Mock-Resp': ''
				},
				status: status
			});
		}
	};
	
	
	
	function HttpServer(identifiers) {
		this.identifiers = identifiers;  // Key,value store to be attached as X-headers to responses.
		// Let's install one foobar route, by default.
		this.routes = {
			"foobar": {
				callback: function() { return "Hello World!"; },
				contentType: "application/json"
			}
		};
	}
	
	HttpServer.prototype = proto;


	return HttpServer;
})();