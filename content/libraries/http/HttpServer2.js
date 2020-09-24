const HttpServer = (function() {


	var proto = {
	
		// Add a route to this server instance.
		addRoute: function(route) {
			this.routes[route["path"]] = route;
		},
		
		// Sends an HttpResponse to the client
		send: function(content) {
			// Setup headers, too.
			return new Reponse(content);
		}
	};
	
	
	
	function HttpServer() {
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