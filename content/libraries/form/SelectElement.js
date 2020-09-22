// let props = {
//     id: 'states-select',
//     className: ["existing-select"]
// }

// let values = {
//     All: 'All',
//     OR: 'Oregon',
//     CA: 'California',
//     WA: 'Washington'
// }

// let selectElement = new SelectElement('states', values, props);




class SelectElement extends IFormElement {

    constructor(name, values, props) {

        super();
        this.name = name;
        this.values = values;
        this.props = !!props ? props : { id: this.name };
    }


    getId() { return this.props.id};


    getName() { return this.name; }


    getValue() { return this.value; }


    render(formId) {


        let options;

        if (Array.isArray(this.values)) {

            options = values.map(value => this.createOptionFromString(value));

        } else {

            for (let value in this.values) {

                options.push(this.createOptionFromKeyValuePair(value, this.values[value]));
            }
        }
        
        


        let selectVNode = super.createVNode(
            "select",
            this.props,
            options,
            this
        );
        
    }

    createOptionFromString(value) {

        return super.createVNode(
            "option",
            { value: value },
            value,
            this
        );
    }

    createOptionFromKeyValuePair(key, value) {

        return super.createVNode(
            "option",
            { value: key },
            value,
            this
        );
    }

}