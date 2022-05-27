const HttpClient = (function() {

	// I think for now the server referenc
	//  should be global
	//  unless we want some kind of faux DNS class.
	// const server = new Server();
	
	
	
	p.send = function(req) {
	
		dispatchBeforeSendEvents(params);
	
		if(!this.server) return this.fetch(req);
		
		route = server.getRoute(req);
		let body = route(req.getBody());
		
		return new Response(body);
	}
	
	
	p.fetch = function(url, params) {
		// type check url for instance of Request
		return fetch(url,params);
	}
	
	p.mock = function(req) {
		return 
	};
	
	

	
	function processResponse(resp, req, params) {

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
	}



	HttpClient.dispatchBeforeSendEvents = function(params) {

		if (hasFile(params)) {
	
			triggerEvent('fileuploadstart', getFiles(params));
		}
	},
	
	
	HttpClient.dispatchAfterSendEvents = function(params, result) {
	
			hasFile(params) && triggerEvent('fileuploadcomplete', { filesBefore: getFiles(params), filesAfter: result.files });
	},
	
	
	HttpClient.dispatchErrorEvents = function(params, reject) {
	
			hasFile(params) && triggerEvent('fileuploaderror', getFiles(params));
	}
	
	
	
	HttpClient.hasFile = function(params) {
	
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
	
	
	HttpClient.getFiles = function(params) {
	
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


	return HttpClient;
})();