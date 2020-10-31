class TextInputElement extends IFormElement {

    constructor(name, label, props, type) {

        super();
        this.validTypes = ["input", "textarea"];
        this.name = name;
        this.label = label;
        this.props = !!props ? this.formatProps(props) : this.defaultProps();
        this.type = this.validateType(type);

        if (!this.props.id) {
            this.props.id = this.name;
        }
    }

    defaultProps() {
        return {
            textInput: {
                id: this.name, 
                className: "textInput"
            },
            label: {
                id: this.name + "-label", 
                className: "textInput-label"
            },
            input: {
                id: this.name + "-input",
                className: "textInput-input"
            }
        };
    }

    formatProps(props) {
        let dProps = this.defaultProps();
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

    validateType(type) {
        for (let i in this.validTypes) {
            let validType = this.validTypes[i];
            if (type && type.toLowerCase() === validType) {
                return type;
            }
        }

        return "input";
    }

    getId() { return this.props.id};


    getName() { return this.name; }


    getValue() { return this.label; }


    render(formId) {
        this.props.label.for = this.props.input.id;

        let labelVNode = vNode(
            "label",
            this.props.label,
            this.label
        );

        let inputVNode = vNode(
            this.type,
            this.props.input,
            []
        );

        let textInputVNode = vNode(
            "div",
            this.props.textInput,
            [labelVNode, inputVNode]
        );

        return textInputVNode;
    }
}