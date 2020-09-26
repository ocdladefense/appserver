class LookupElement extends IFormElement {

    constructor(name, values, props) {

        super();
        this.name = name;
        this.values = values;
        this.props = !!props ? this.formatProps(props) : this.defaultProps();
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
                id: "insert-" + this.name + "-label", 
                className: "lookup-label"
            },
            group: {
                id: this.name + "-group", 
                className: "lookup-group"
            },
            input: {
                id: "insert-" + this.name, 
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
                    if (!props[prop][attribute]) {
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

        let values = this.values;
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
            field + ": "
        );

        return vNode(
            "div",
            this.props.lookup,
            [labelVNode, divVNode]
        );
    }
}