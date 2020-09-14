class XmlHttpRequestCallout extends ICallout {

    constructor(endpoint, callout) {

        super();
        this.endpoint = endpoint;
        this.callout = callout;

        this.successFn;
        this.failureFn;
    }


    setEnpoint(endpoint) {

        this.endpoint = endpoint;
    }


    setCallout(callout) {

        this.callout = callout;
    }


    onSuccess(fn) {

        this.successFn = fn;
    }


    onFailure(fn) {

        this.failureFn = fn;
    }


    send(formData, endpoint, callout) {

        let currentEndpoint = endpoint || this.endpoint;

        let currentCallout = (callout || this.callout) || this.defaultCallout;

        currentCallout(formData, currentEndpoint)
        .then(response => {

            if (formData.get('files[]')) {

                triggerEvent('filereceived', response); // Custom global appserver method
            }
                
            if (this.successFn) {

                this.successFn(response);
            }
        })
        .catch(reject => {

            if (this.failureFn) {

                this.failureFn(reject);
            }
        });
    }


    defaultCallout(formData, currentEndpoint) {

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

                if (xhr.status === 200 && response.status === 'success') { // 'success' is a custom value from the server
        
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