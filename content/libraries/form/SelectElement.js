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

        if (!this.props.id) {
            this.props.id = this.name;
        }
    }


    getId() { return this.props.id};


    getName() { return this.name; }


    getValue() { return this.value; }


    render(formId) {


        let options = [];

        /*if (Array.isArray(this.values)) {

            options = values.map(value => this.createOptionFromString(value));

        } else {

            for (let value in this.values) {

                options.push(this.createOptionFromKeyValuePair(value, this.values[value]));
            }
        }*/

        let values = Array.isArray(this.values) ? this.values : [this.values];

        for (let i in values) {
            options.push(this.createOption(values[i]));
        }
        
        


        let selectVNode = vNode(
            "select",
            this.props,
            options
        );
        return selectVNode;
    }

    createOption(option) {
        if (typeof option === 'object' && option !== null) {
            if (Object.keys(option).length === 1) {
                // This loop will only iterate once
                for (let key in option) {
                    return this.createOptionFromKeyValuePair(key, option[key]);
                }
            } else if (Object.keys(option).length > 1) {
                return this.createOptionFromObject(option);
            }
        } else {
            return this.createOptionFromString(option);
        }
    }

    createOptionFromString(value) {

        return vNode(
            "option",
            { value: value },
            value + ""
        );
    }

    createOptionFromKeyValuePair(text, value) {

        return vNode(
            "option",
            { value: value },
            text + ""
        );
    }

    createOptionFromObject(props) {
        if (!props.text || !props.value) {
            return;
        }

        let text = props.text;
        delete props.text;

        return vNode(
            "option",
            props,
            text + ""
        );
    }
}