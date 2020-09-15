const Callout = (function() {



	function Callout(fn) {
		this.source = fn || null;
	}
	
	
	let callout = {

		// What is params?
		// Params = new FormData()
		// files = params.get('files[]');
		send: function(params) {

			if (typeof params === 'function') {

				params = params();
			}


			if (!(params instanceof FormData)) {

				throw new Exception("Params must be of type 'FormData'");
			}

			// httpRequest

			// httpRequest.headers

			// inspect headers for content-type === 'multipart/form-data'

			// if (headers.includes('multipart/form-data)) 

			// It's possible that a request of 'multipart/form-data' will not include files to be sent

			let req = new Request();

			this.dispatchBeforeSendEvents(params);

			let resp = new Promise((resolve, reject) => {
				
				try {

					let result = this.source.call(null, params);
					resolve(result);

				} catch(e) {

					reject(e);
				}
			});

			resp.then((result) => {
				
				this.dispatchAfterSendEvents(params, result);
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

				triggerEvent('fileuploadcomplete', params.getFiles());
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

// formData.hasFiles();

// let filesInFormData = formData.getFiles();