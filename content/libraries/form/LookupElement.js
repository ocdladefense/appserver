class LookupElement extends IFormElement {

    constructor(name, values, props, label) {

        super();
        this.name = name;
        this.values = values;
        this.props = !!props ? this.formatProps(props) : this.defaultProps();
        this.label = label;
    }

    getId() { return this.props.id};


    getName() { return this.name; }


    getValue() { return this.value; }

    defaultProps() {
        return {
            lookup: {
                id: this.name, 
                className: "lookup"
            },
            label: {
                id: this.name + "-label", 
                className: "lookup-label"
            },
            group: {
                id: this.name + "-group", 
                className: "lookup-group"
            },
            input: {
                id: this.name + '-input', 
                className: "lookup-input"
            },
            select: {
                id: this.name + '-select',
                className: 'lookup-select'
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

    render(formId) {

        let values = [...this.values];
        values.unshift({ "--NEW--": "NEW" });

        let field = this.name;

        let selectVNode = new SelectElement(field + '-select', values, this.props.select);

        let inputVNode = vNode(
            "input",
            this.props.input,
            []
        );

        let divVNode = vNode(
            "div",
            this.props.group,
            [selectVNode.render(), inputVNode]
        );

        let labelVNode = vNode(
            "label",
            this.props.label,
            !!this.label ? this.label : field
        );

        return vNode(
            "div",
            this.props.lookup,
            [labelVNode, divVNode]
        );
    }

    attachEventListeners() {
        let select = document.getElementById(this.props.select.id);
        let input = document.getElementById(this.props.input.id);

        select.addEventListener("input", () => {
            input.value = "";
            if (select.value == "NEW") {
                input.disabled = false;
            } else {
                input.disabled = true;
            }
        });
    }
}