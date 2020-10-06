class UpdateForm {
    constructor(name, setFields, whereFields, props) {
        this.name = name;
        this.setFields = Array.isArray(setFields) ? setFields : [setFields];
        this.whereFields = Array.isArray(whereFields) ? whereFields : [whereFields];
        this.props = !!props ? props : { id: this.name };

        if (!this.props.id) {
            this.props.id = this.name;
        }
    }

    render() {
        let setFieldComponents = [];

        for (let i in this.setFields) {
            let setFieldComponent = new FormFieldComponent(this.setFields[i], "insert");
            setFieldComponents.push(setFieldComponent.render());
        }

        let whereFieldComponents = [];

        for (let i in this.whereFields) {
            let whereFieldComponent = new FormFieldComponent(this.whereFields[i], "where");
            whereFieldComponents.push(whereFieldComponent.render());
        }

        let formVNode = vNode(
            "form",
            this.props,
            [...setFieldComponents, ...whereFieldComponents]
        );

        return formVNode;
    }
}