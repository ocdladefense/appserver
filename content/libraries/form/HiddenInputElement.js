class HiddenInputElement extends IFormElement {

    name;

    value;

    constructor(name, value) {

        super();
        this.name = name;
        this.value = value;
    }

    
    getName() { return this.name; }


    getValue() { return this.value; }
    

    render(formId) {

        let form = document.getElementById(formId);

        let hiddenElement = document.createElement('input');
        hiddenElement.setAttribute('type', 'hidden');
        hiddenElement.setAttribute('name', this.name);
        hiddenElement.value = this.value;

        form.appendChild(hiddenElement);
    }

}