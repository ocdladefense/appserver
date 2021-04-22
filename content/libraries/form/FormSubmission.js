
/**
 * This class should be merged into 
 *  The new Callout class.
 */
const FormSubmission = (function() {

    function FormSubmission(url) {
    	this.url = url;
    }


		FormSubmission.prototype = {
			sendMe: function(params) {
        return fetch(this.url).then((resp) => {
            return resp.json();
        });
			}
		};

    FormSubmission.send = function (url, json) {
        let req = this.getRequest(json, url);
        let response = fetch(req);
        return response.then((resp) => {
            return resp.text();
        });
    };

    FormSubmission.getRequest = function (json, url) {
        let headers = new Headers();
        headers.append('Content-Type', 'application/json');
        headers.append('Accept', 'text/html');

        let init = {
            body: json,
            method: "POST",
            headers: headers
        }

        return new Request(url, init);
    };

    return FormSubmission;

})();