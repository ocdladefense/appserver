const TEXTINPUT = "textinput";
const TEXTAREA = "textinput-textarea"
const LOOKUP = "lookup";
const HIDDEN = "hidden";

class InsertForm {
    constructor(name, fields, props) {
        this.name = name;
        this.fields = fields;
        this.props = !!props ? props : { id: this.name };

        if (!this.props.id) {
            this.props.id = this.name;
        }
    }

    render() {
        let fieldComponents = [];

        for (let i in this.fields) {
            fieldComponents.push(this.createFieldComponent(this.fields[i]));
        }

        let formVNode = vNode(
            "form",
            this.props,
            fieldComponents
        );

        return formVNode;
    }

    createFieldComponent(field) {
        if (typeof field === 'object' && field !== null) {
            if (Object.keys(field).length === 1) {
                // This loop will only iterate once
                for (let key in field) {
                    return this.createFieldComponentFromKeyValuePair(key, field[key]);
                }
            } else if (Object.keys(field).length > 1) {
                return this.createFieldComponentFromObject(field);
            }
        } else {
            return this.createFieldComponentFromString(field);
        }
    }

    createFieldComponentFromString(field) {
        let props = {
            input: {
                "data-field": field,
                "data-row-id": 1
            }
        };

        let fieldCom = new TextInputElement(field, field, props);
        return fieldCom.render();
    }

    createFieldComponentFromKeyValuePair(field, type) {
        let fieldCom;

        let props = {
            input: {
                "data-field": field,
                "data-row-id": 1
            }
        };

        if (Array.isArray(type)) {
            fieldCom = new LookupElement(field, type, props);
            return fieldCom.render();
        }

        switch (type.toLowerCase()) {
            case TEXTAREA:
                fieldCom = new TextInputElement(field, field, props, "textarea");
                break;
            case HIDDEN:
                props = props.input;
                return this.createHiddenField(props);
            default:
                fieldCom = new TextInputElement(field, field, props);
                break;
        }

        return fieldCom.render();
    }

    createFieldComponentFromObject(fieldObj) {
        if (!fieldObj.field) {
            return;
        }

        let fieldCom;

        let field = fieldObj.field;
        let type = typeof fieldObj.type === "undefined" ? "" : fieldObj.type;
        let label = typeof fieldObj.label === "undefined" ? field : fieldObj.label;

        let objProps = typeof fieldObj.props === "undefined" ? {} : fieldObj.props;
        let props = {
            input: {
                "data-field": field,
                "data-row-id": 1
            }
        };
        props = this.formatProps(objProps, props);

        switch (type.toLowerCase()) {
            case TEXTAREA:
                fieldCom = new TextInputElement(field, label, props, "textarea");
                break;
            case LOOKUP:
                if (typeof fieldObj.values === "undefined") {
                    return;
                }
                let values = Array.isArray(fieldObj.values) ? fieldObj.values : [fieldObj.values];
                fieldCom = new LookupElement(field, values, props);
                break;
            case HIDDEN:
                props = props.input;
                return this.createHiddenField(props);
            default:
                fieldCom = new TextInputElement(field, label, props);
                break;
        }

        return fieldCom.render();
    }

    createHiddenField(props) {
        let hiddenProps = {
            style: "display: none;"
        };

        return vNode(
            "input",
            this.formatProps(props, hiddenProps),
            []
        );
    }

    formatProps(dProps, props) {
        for (let prop in dProps) {
            if (props[prop]) {
                for (let attribute in dProps[prop]) {
                    if (typeof props[prop][attribute] === "undefined") {
                        props[prop][attribute] = dProps[prop][attribute];
                    } else if(attribute == "className") {
                        props[prop][attribute] += " " + dProps[prop][attribute];
                    }
                }
            } else {
                props[prop] = dProps[prop];
            }
        }
        return props;
    }
}