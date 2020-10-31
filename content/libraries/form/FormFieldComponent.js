class FormFieldComponent {
    constructor(field, propsType) {
        this.field = field;
        this.propsType = propsType;
    }

    render() {
        let field = this.field;
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
        let props = this.props(field);

        let fieldCom = new TextInputElement(field, field, props);
        return fieldCom.render();
    }

    createFieldComponentFromKeyValuePair(field, type) {
        let fieldCom;

        let props = this.props(field);

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
                return this.createHiddenField(field, props);
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
        let props = this.props(field);
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
                fieldCom = new LookupElement(field, values, props, label);
                break;
            case HIDDEN:
                props = props.input;
                return this.createHiddenField(field, props);
            default:
                fieldCom = new TextInputElement(field, label, props);
                break;
        }

        return fieldCom.render();
    }

    createHiddenField(field, props) {
        let hiddenProps = {
            id: field + "-input",
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

    props(field) {
        switch(this.propsType) {
            case "insert":
                return this.insertProps(field);
                break;
            case "where":
                return this.whereProps(field);
                break;
        }
    }

    insertProps(field) {
        return {
            input: {
                "data-field": field,
                "data-row-id": 1
            }
        };
    }

    whereProps(field) {
        return {
            input: {
                "data-field": field
            }
        };
    }
}