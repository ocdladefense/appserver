const Application = (function() {

    let application = {

        addListener(obj) {

            // Get the event listeners off the object
            let listeners = obj.listeners || {}; 
    
            for (const eventName in listeners) {
    
                // Use the handleEvent method on the object if available
                // Bind 'this' to the object
                let fn = obj.handleEvent ? obj.handleEvent.bind(obj) : listeners[eventName].bind(obj);

                // Check if event is already in the listeners object
                if (!this.listeners[eventName]) { 

                    // Register a listener for the event on the document
                    // If one of these events happens, it will call 'handleEvent' from Application.js
                    document.addEventListener(eventName, this, { capture: true });

                    // Set up a new array for multiple listeners for the event
                    this.listeners[eventName] = [];
                } 

                // Add the event
                this.listeners[eventName].push(fn);
            }
        },


        handleEvent(e) {

            let type = e.type;
            let listeners = this.listeners[type];

            if (listeners.length === 0) {

                return;
            }

            listeners.forEach(listener => {

                listener(e);
            });

        },


        injectElement(config) {

            return new Promise((resolve, reject) => {
    
                let node;

                if (config.type === 'CSS') {

                    node = document.createElement('link');
                
                } else if (config.type === 'JS') {

                    node = document.createElement('script');
                }

                node.onload = resolve;
    
                let head = document.body.getElementsByTagName('HEAD')[0];
                head.appendChild(node);
            });
        },


        render: function(vNode, location) {
            location.appendChild(createElement(vNode));
        }
    };


    function Application(appName) {

        this.name = appName;
        this.listeners = {};
    }

    Application.prototype = application;

    return Application;
})();