class IFormElement {

    constructor() {

        if (new.target === IFormElement) {

            throw new TypeError('IFormElement is an interface and cannot be directly instantiated.');
        }

        if (typeof this.getName !== 'function') {

            throw new TypeError(new.target.name + ' must implement getName(): string');
        }

        if (typeof this.getValue !== 'function') {

            throw new TypeError(new.target.name + ' must implement getValue(): all');
        }

        if (typeof this.render !== 'function') {

            throw new TypeError(new.target.name + ' must implement render(locationId): void');
        }
    }

}