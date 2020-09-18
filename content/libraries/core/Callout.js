const Callout = (function() {



	function Callout(fn) {
		this.source = fn || null;
	}
	
	
	let callout = {

		// What is params?
		// Params = new FormData()
		// files = params.get('files[]');
		send: function(params) {

			// Should be able to mock file download
			if (typeof params === 'function') {

				params = params();
			}


			if (!(params instanceof FormData)) {

				throw new Exception("Params must be of type 'FormData'");
			}


			let body = this.source instanceof Error ? { error: this.source.message } : this.source;
			let status = this.source instanceof Error ? 500 : 200;
	
	
			// httpRequest

			// httpRequest.headers

			// inspect headers for content-type === 'multipart/form-data'

			// if (headers.includes('multipart/form-data)) 

			// It's possible that a request of 'multipart/form-data' will not include files to be sent

			let req = new Request();

			this.dispatchBeforeSendEvents(params);


			let resp;

			if (this.source instanceof Error) {

				resp = new Response();
				resp.setBody(typeof body !== 'function' && typeof body === 'object' ? JSON.stringify(body) : body);
				resp.setHeader('Content-Type', typeof body !== 'function' && typeof body === 'object' ? 'application/json' : 'text/html');
				resp.setStatus(status);
				resp.setHeader('X-Mock-Resp', '');


			} else if (typeof this.source === 'function') {

				resp = new Promise((resolve, reject) => {
					
					try {

						let result = this.source.call(null, params);
						resolve(result);

					} catch(e) {

						reject(e);
					}
				});
			}




			







			resp.then(result => result.body)
			.then(body => {
				
				this.dispatchAfterSendEvents(params, body);
			})
			.catch((reject) => {

				this.dispatchErrorEvents(params, reject);
			});

			return resp;
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
		},


		dispatchBeforeSendEvents: function(params) {

			if (params.hasFile) {

				triggerEvent('fileuploadstart', params.getFiles());
			}
		},


		dispatchAfterSendEvents: function(params, result) {

			if (params.hasFile) {

				triggerEvent('fileuploadcomplete', { filesBefore: params.getFiles(), filesAfter: result.files });
			}
		},


		dispatchErrorEvents: function(params, reject) {

			if (params.hasFile) {

				triggerEvent('fileuploaderror', params.getFiles());
			}
		}


	}
	


	Callout.prototype = callout;



	return Callout;
})();



// Possible File Events
// fileuploadstart, fileuploadprogress, fileuploadcomplete, fileuploaderror


window.onload = () => {

	// Creates a property (self-executing function)
	// let formData = new FormData();
	// formData.hasFile
	Object.defineProperty(FormData.prototype, "hasFile", {
		get: function hasFile() {			
		
			for (let value of this.values()) {
				
				if (value instanceof File) {

					return true;
				}
			}

			return false;
		}
	});

	// Creates a function
	// let formData = new FormData();
	// formData.hasFile();
	// FormData.prototype.hasFile = function() {

	// 	for (let value of this.values()) {
			
	// 		if (value instanceof File) {

	// 			return true;
	// 		}
	// 	}

	// 	return false;
	// };


	// Creates a function
	// let formData = new FormData();
	// let files = formData.getFiles();
	FormData.prototype.getFiles = function() {

		let files = [];

		for (let value of this.values()) {
			
			if (value instanceof File) {

				files.push(value);
			}
		}

		return files;
	};


	// let formData = new FormData();

	// formData.set('Test String 1', 'Test 1');
	// formData.set('Test String 2', 'Test 2');
	// formData.set('Test File 1', new File(['Test File 1'], 'TestFile1.txt', { type: 'text' }));
	// formData.set('Test File 2', new File(['Test File 2'], 'TestFile2.txt', { type: 'text' }));

	// formData.hasFile;

	// let filesInFormData = formData.getFiles();



};





