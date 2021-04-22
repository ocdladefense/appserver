const Form = (function() {

    let form = {

        addElement: function(element) {

            if (!(element instanceof IFormElement)) {

                throw new TypeError(element.constructor.name + ' must extend FormElement');
            }

            this.elements.push(element);
        },


        getElementFormData: function() {

            let formData = new FormData();

            this.elements.forEach(element => {

                let name = element.getName(); // Interface method
                let value = element.getValue(); // Interface method


                if (Array.isArray(name)) { // Name could be an array of names

                    name.forEach(name => {

                        this.appendData(name, value, formData);
                    });

                } else {

                    this.appendData(name, value, formData);
                }

            });

            return formData;
        },


        appendData: function(name, value, formData) {

            if (Array.isArray(value)) { // Value could be an array of values

                value.forEach(value => {

                    formData.append(name, value);
                });

            } else {

                formData.append(name, value);
            }

        },


        render: function() {

            return createVNode(
                "form",
                { id: this.formId },
                this.elements,
                this
            };
        },


        compile: function() { // Combine HTML DOM form data and form data from elements

            let formDataFromDom = this.getDomFormData();

            this.formData = this.getElementFormData();

            return this.merge(this.formData, formDataFromDom); // If any key is duplicated, gives precedence to DOM data
        },


        getDomFormData: function() {

            // If there is an HTML DOM form, get all the data out of it 
            return !!this.formElement ? new FormData(this.formElement) : new FormData();
        },


        merge(formData1, formData2) { // If any key is duplicated, gives precedence to formData2

            for (let key of formData2.keys()) {

                formData1.set(key, formData2.get(key));
            }

            return formData1;
        },

        setCalloutService: function(calloutService) {

            this.calloutService = calloutService;
        },


        send: function(formData) {

            this.calloutService.send(formData);
        },


        overrideFormSubmit: function() {

            this.formElement.addEventListener('submit', (e) => {

                e.preventDefault();

                let formData = this.compile();
                this.send(formData);

                this.dispatchFileSentEvent(formData); // Only if there is a 'files[]' key in formData

            });
        },


        dispatchFileSentEvent: function(formData) {

            let files = formData.getAll('files[]');

            if (files) {

                let event = new CustomEvent('filesent', { detail: files });
                document.dispatchEvent(event);
            }
        }

    };


    function Form(formId, calloutService) {

        this.formId = formId;

        this.formElement = document.getElementById(formId);

        if (this.formElement) {

            this.overrideFormSubmit();
        }


        this.calloutService = calloutService;

        this.formData = new FormData();

        this.elements = [];
        this.renderedElements = [];
    }


    Form.prototype = form;

    return Form;
})();