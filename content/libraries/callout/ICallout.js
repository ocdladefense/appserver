class ICallout {

    constructor() {

        if (new.target === ICallout) {

            throw new TypeError('ICallout is an interface and cannot be directly instantiated.');
        }

        if (typeof this.send !== 'function') {

            throw new TypeError(new.target.name + ' must implement send(formData, endpoint, callout): void');
        }
    }

}