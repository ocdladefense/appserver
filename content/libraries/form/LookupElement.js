class LookupElement extends IFormElement {

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


        let selectVNode = new SelectElement(field + '-select', values, { id: field + '-select', className: 'existing-select'});

        let inputVNode = super.createVNode(
            "input",
            { id: "insert-" + field, class: "existing-input car-create-field", "data-field": field, "data-row-id": 1 },
            [],
            this
        );

        let divVNode = super.createVNode(
            "div",
            { id: field + "-group", class: "existing-group" },
            [selectVNode, inputVNode],
            this
        );

        let labelVNode = super.createVNode(
            "label",
            { id: "insert-" + field + "-label" },
            formatLabel(field) + ": ",
            this
        );

        return super.createVNode(
            "div",
            { id: field, class: "form-field" },
            [labelVNode, divVNode],
            this
        );
    }



}