class SearchBoxElement extends IFormElement {

    constructor(name, values, props) {

        super();
        this.name = name;
        this.values = Array.isArray(values) ? values : [values];
        this.props = !!props ? this.formatProps(props) : this.defaultProps();
    }


    getId() { return this.props.id};


    getName() { return this.name; }


    getValue() { return this.value; }

    defaultProps() {
        return {
            searchBox: {
                id: this.name, 
                className: "searchbox"
            },
            input: {
                id: this.name + "-input", 
                className: "searchbox-input"
            },
            checkboxes: {
                id: this.name + "-checkboxes",
                className: "searchbox-checkbox-group"
            },
            checkbox: {
                id: this.name + "-checkbox", //this string is added to in render()
                className: "searchbox-checkbox"
            },
            checkboxLabel: {
                id: this.name + "-checkbox-label", //this string is added to in render()
                className: "searchbox-checkbox-label"
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
        let searchCheckBoxes = this.values.length > 1 ? this.createCheckboxes() : this.createSingleInput();

        let checkBoxesVNode = vNode(
            "div",
            this.props.checkboxes,
            searchCheckBoxes
        );

        var inputVNode = vNode(
            "input",
            this.props.input, 
            []
        );

        let searchBoxVNode = vNode(
            "div",
            this.props.searchBox,
            [checkBoxesVNode, inputVNode]
        );

        return searchBoxVNode;
    }

    createCheckboxes() {
        return this.values.flatMap(searchField => {
            let labelText = "";

            let checkboxProps = this.copyPropObject(this.props.checkbox);
            checkboxProps.type = "checkbox";

            let labelProps = this.copyPropObject(this.props.checkboxLabel);

            if (typeof searchField === 'object' && searchField !== null) {
                for (let key in searchField) {
                    checkboxProps.id += checkboxProps.id === "" ? searchField[key] : "-" + searchField[key];
                    labelProps.id += labelProps.id === "" ? searchField[key] : "-" + searchField[key];
                    labelText += key;
                    checkboxProps.value = searchField[key];
                    break; //End now because there should only be one key/value pair
                }
            } else {
                checkboxProps.id += checkboxProps.id === "" ? searchField : "-" + searchField;
                labelProps.id += labelProps.id === "" ? searchField : "-" + searchField;
                labelText += searchField;
                checkboxProps.value = searchField;
            }

            labelProps.for = checkboxProps.id;
    
            return [vNode(
                "label",
                labelProps,
                labelText
            ),
            vNode(
                "input",
                checkboxProps,
                []
            )
            ];
        });
    }

    createSingleInput() {
        return this.values.map(searchField => {
            let checkboxProps = this.copyPropObject(this.props.checkbox);
            checkboxProps.id += checkboxProps.id === "" ? searchField : "-" + searchField;
            checkboxProps.checked = "true";
            checkboxProps.value = searchField;
            checkboxProps.style = "display: none;";

            return vNode(
                "input",
                checkboxProps,
                []
            );
        });
    }

    copyPropObject(prop) {
        let copy = {};
        for (let attribute in prop) {
            copy[attribute] = prop[attribute];
        }
        return copy;
    }
}