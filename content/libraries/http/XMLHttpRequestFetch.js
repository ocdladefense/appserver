function XMLHttpRequestFetch(url,formData) {
	return new Promise((resolve,reject) => {
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
}