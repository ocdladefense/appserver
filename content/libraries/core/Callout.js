const Callout = (function() {

	const server = new Server();
	// Param 1 could be: string, Error, function, Request
	// If string: construct Request setting URL to Param 1
	// If Error: construct Request from null (unused Request)
	// If function: construct Request from null (unused Request)
	// If Request: use it with Fetch
	function Callout(url, mockCallback) {
		this.server.addRoute(new Route(url, callback));

		this.url = url || null;
		this.requestInit = requestInit || { method: 'POST', headers: { 'Content-Type': 'application/json' }};
		this.method = this.requestInit.method;
		this.isMock = !!mockCallback;
		this.mockCallback = mockCallback;

	}
	
	
	let callout = {


		send: function(params) {


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
			let req = new Request(this.url, init);
	
			dispatchBeforeSendEvents(params);
			


			// Returns a response in a promise:
			//		1. If request was real, response will be real
			//		2. If request was mock, response will be mocked and wrapped in a promise
			let resp = !!this.isMock ? Promise.resolve(this.buildMockResponse(params)) : fetch(req);


			let eventsResp = resp.clone();

			
			dispatchAfterSendEvents(eventsResp); // TODO: Refactor to use resp object


			return resp;
		},



		buildRequest: function(params) {

			// if (this.method !== 'GET' && this.method !== 'HEAD' && !!params) {

				if (params instanceof Error) {

					this.requestInit.body = { error: this.source.message };
	
				} else if (typeof params === 'object') {
	
					this.requestInit.body = JSON.stringify(params);
	
				} else if (!!params) {
	
					this.requestInit.body = params;
				}

			// }

			return new Request(this.url, this.requestInit);
		},
		
















		// Might want some way to read from the request
		buildMockResponse(params) {
			// const route = server.match(this.url);
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
		},



		processResponse(resp, req, params) {

			// Every response object has access to .text() and .json()
			return resp.then(response => {
				
				console.log(response);
				return response.json();
			})
			.then(data => {
				
				dispatchAfterSendEvents(params, data);
				return data;
			})
			.catch((reject) => {

				dispatchErrorEvents(params, reject);
				return reject;
			});
		},



		defaultCallout: function(formData, currentEndpoint) {

			if (!currentEndpoint) {

				console.error('Endpoint not set! XmlHttpRequest not sent.');
				return;
			}


			let promise = new Promise((resolve, reject) => {

				var xhr = new XMLHttpRequest();

				xhr.open("POST", currentEndpoint);
			
				xhr.onreadystatechange = (e) => {
			
					let xhr = e.target;
		
					if (xhr.readyState !== XMLHttpRequest.DONE) {
		
						return;
					}
		
					let response = JSON.parse(xhr.response);

					if (xhr.status === 200) {
			
						resolve(response);
			
					} else {
			
						reject(response);
					}
				};
			
				xhr.send(formData);
			});
		
			return promise;
		}


	}
	


	Callout.prototype = callout;


	Callout.dispatchBeforeSendEvents = function(params) {

		if (hasFile(params)) {
	
			triggerEvent('fileuploadstart', getFiles(params));
		}
	},
	
	
	Callout.dispatchAfterSendEvents = function(params, result) {
	
		if (hasFile(params)) {
	
			triggerEvent('fileuploadcomplete', { filesBefore: getFiles(params), filesAfter: result.files });
		}
	},
	
	
	Callout.dispatchErrorEvents = function(params, reject) {
	
		if (hasFile(params)) {
	
			triggerEvent('fileuploaderror', getFiles(params));
		}
	}
	
	Callout.hasFile = function(params) {
	
		if (!(params instanceof FormData)) {
	
			return false;
		}
	
		for (let value of params.values()) {
					
			if (value instanceof File) {
	
				return true;
			}
		}
	
		return false;
	};
	
	
	Callout.getFiles = function(params) {
	
		let files = [];
	
		if (!(params instanceof FormData)) {
	
			return files;
		}
	
	
		for (let value of params.values()) {
			
			if (value instanceof File) {
	
				files.push(value);
			}
		}
	
		return files;
	};
	

	return Callout;
})();












// Possible File Events
// fileuploadstart, fileuploadprogress, fileuploadcomplete, fileuploaderror







