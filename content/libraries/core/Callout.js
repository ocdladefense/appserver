// import core/libraries/http/XMLHttpRequestFetch.js
// import core/libraries/http/HttpServer2.js
// import core/libraries/http/Route.js
// import core/libraries/http/HttpClient2.js (the fetch method defers to the FetchAPI.)


/**
 * @class Callout
 *
 * @description Callout is a transient, convenience object
 *  that encapsulates the relationship between an internal
 *  HttpServer and its Routes (and callbacks).
 */

const Callout = (function() {

	// From HttpServer2.js
	// @static
	const server = new HttpServer();
	
	
	// Param 1 could be: string, Error, function, Request
	// If string: construct Request setting URL to Param 1
	// If Error: construct Request from null (unused Request)
	// If function: construct Request from null (unused Request)
	// If Request: use it with Fetch
	function Callout(url, mockCallback) {
		// server.addRoute(new Route(url, mockCallback));
		let requestInit = null; // @jbernal
		this.url = url || null;
		this.requestInit = requestInit || { method: 'POST', headers: { 'Content-Type': 'application/json' }};
		this.method = this.requestInit.method;
		this.isMock = !!mockCallback;
	}
	
	
	/**
	 * @function send
	 *
	 *
	 * @return Response, the http response object either returned from the fetch call or
	 *   from an XMLHttpRequest call or a synthetic response generated by a Worker, ServiceWorker from 
	 *   an HttpServer object.
	 *
	 *
	 * @description The send method is a utility method that objects can call
	 *  without worrying about the underlying details of Request and Response.
	 *  Instead, the Callout class wires up an HttpServer instance with the appropriate
	 *   route and callback; it constructs a low-level Request object
	 *    and uses the HttpClient to send the Request to the HttpServer object to obtain a Response.
	 */
	function send(params) {

		return Promise.resolve(this.url());
		params = typeof params === 'function' ? params() : params;

		let body = params instanceof Error ? { error: params.message } : params;

		if (this.requestInit.headers['Content-Type'] === 'application/json') {

			body = JSON.stringify(body);
		}

		let init = {
			method: 'POST',
			headers: { 
				'Content-Type': 'application/json'
			},
			body: body
		};

		// Builds a request object that is either:
		// 		1. A real request to be sent to the server
		//		2. A mock request for testing (Should we even build a request for a mock?)
		// This API needs to be consistent with the FetchEvent.respondWith event handler.
		const req = new Request(this.url, init);
		req.setBody(params);
	
		// Construct an HttpClient.
		let client = new HttpClient();
		// client.setTimeout(2000), etc.
	
		// Finally, return a response object.
		return this.isMock ? client.send(req) : client.fetch(req);
	}







	// @deprecated?
	function defaultCallout(formData, currentEndpoint) {

			return !currentEndpoint ? Promise.reject("Endpoint not set! XmlHttpRequest not sent.") : XMLHttpRequestFetch(currentEndpoint, formData);
	}	
	


	Callout.prototype = {
		send: send
	};


	// Static methods moved to HttpClient2.js
	return Callout;
})();


