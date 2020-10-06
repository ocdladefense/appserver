const TEXTINPUT = "textinput";
const TEXTAREA = "textinput-textarea"
const LOOKUP = "lookup";
const HIDDEN = "hidden";

class InsertForm {
    constructor(name, fields, props) {
        this.name = name;
        this.fields = Array.isArray(fields) ? fields : [fields];
        this.props = !!props ? props : { id: this.name };

        if (!this.props.id) {
            this.props.id = this.name;
        }
    }

    render() {
        let fieldComponents = [];

        for (let i in this.fields) {
            let fieldComponent = new FormFieldComponent(this.fields[i], "insert");
            fieldComponents.push(fieldComponent.render());
        }

        let formVNode = vNode(
            "form",
            this.props,
            fieldComponents
        );

        return formVNode;
    }
}