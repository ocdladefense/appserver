class Notifier {

    static types = Object.freeze({
        'drop': 'drop',
        'flip': 'flip',
        'fade-slow': 'fade-slow',
        'fade-fast': 'fade-fast'
    });

    constructor() {

        this.type = Notifier.types.drop; // Default type

        this.displayInterval = 3000; // Amount of time in ms that a notification is shown

        this.notificationContainer;

        this.notificationQueue = []; // Queue to hold notifications that have not been shown yet
        
        this.isShowingNotification = false;
        
        this.listeners = {  // Default listeners

            fileadd: function(fileList) {

                let messages = [];

                for (const file of fileList) {

                    messages.push('File ' + file.name + ' added!');
                }

                return messages;
            },

            filedelete: function(files) {
    
                return 'File ' + files[0].name + ' deleted!';
            }
        }


        this.init();
    }


    handleEvent(e) {

        let messageFn = this.listeners[e.type];
    
        if (!messageFn) {

            return;
        }
    
        let message = messageFn(e.detail);
        

        if (Array.isArray(message)) {

            message.forEach(singleMessage => {

                this.renderNotification(singleMessage);
            });

        } else {

            this.renderNotification(message);
        }


        if (!this.isShowingNotification) {

            this.wait(50).then(this.showAll.bind(this)).finally(() => console.log('Done'));
        }
    }


    init() {

        this.renderNotificationContainer();
    }


    setInterval(interval) {
    
        this.displayInterval = interval;
    }


    setType(type) {

        if(!type || !Notifier.types[type]) {

            console.error('NOTIFICATION: This type is unsupported.');
            return;
        }

        this.type = Notifier.types[type];
    }


    // showAll() {

    //     console.log('Showing next notification...');

    //     if (this.notificationQueue.length === 0) {

    //         console.log('No more notifications to show!');
    //         return;
    //     }

    //     this.show();

    //     window.setTimeout(() => {

    //         this.showAll();

    //     }, this.displayInterval);
    // }


    // Recursively show all notifications in the queue
    showAll() {

        if (this.notificationQueue.length === 0) {

            console.log('No more notifications to show!');
            this.isShowingNotification = false;
            return;
        }

        this.showNext();

        return this.wait(this.displayInterval + 50).then(this.showAll.bind(this));
    }


    // Show the next notification in the queue
    showNext() {

        if (this.isShowingNotification) {

            console.log('Notification already showing!');
            return;
        }

        this.currentNotification = this.getFromQueue();

        this.show(this.currentNotification);

        this.hide(this.currentNotification);
    }


    // Show a specific notification, regardless of queue position
    showNow(specificNotification) {

        if (this.currentNotification) {

            this.hideNow(this.currentNotification);
        }

        this.currentNotification = this.getFromQueue(specificNotification);

        this.show(this.currentNotification);

        this.hide(this.currentNotification);
    }


    show(notification) {

        this.isShowingNotification = true;

        notification.classList.add(`notification-show-${this.type}`);
    }


    hide(notification) {

        // Wait until display interval is finished to hide the notification
        this.wait(this.displayInterval)
        .then(() => {

            if (!document.body.contains(notification)) {

                return;
            }

            this.hideNow(notification);
        });
    }



    hideNow(notification) {

        this.isShowingNotification = false;

        notification.classList.remove(`notification-show-${this.type}`);

        this.unrenderNotification(notification);
    }


    getFromQueue(notification) {

        if (!notification) {

            return this.notificationQueue.shift();
        }


        let index = this.notificationQueue.indexOf(notification);

        if (index > -1) {

            this.notificationQueue.splice(index, 1);
        }

        return notification;
    }


    // forgeNotificationElement() {

    //     let notificationElement = this.createNotificationElement();

    //     this.setNotificationElement(notificationElement);
    //     document.body.appendChild(notificationElement);
        
    //     return new Promise((resolve, reject) => {
        
    //         setTimeout(() => {

    //             resolve(document.getElementById('notification-system'));
            
    //         }, 50);
        
    //     });
    
    // }

    
    renderNotification(message, onClick, type) {

        let notificationMessage = document.createElement('p');
        notificationMessage.classList.add('notification-message');
        notificationMessage.innerText = message;

        let notificationBody = document.createElement('div');
        notificationBody.classList.add('notification-body');
        notificationBody.classList.add(`notification-${this.type}`);

        if(onClick) {

            notificationBody.onclick = onClick;
            notificationBody.style.cursor = 'pointer';
        
        } else {

            notificationBody.style.cursor = 'default';
        }
        
        notificationBody.appendChild(notificationMessage);

        this.notificationContainer.appendChild(notificationBody);

        this.notificationQueue.push(notificationBody);

        return notificationBody;
    }


    unrenderNotification(notification) {

        this.wait(500).then(() => {

            if (!document.body.contains(notification)) {

                return;
            }

            this.notificationContainer.removeChild(notification);
        });
    }

    renderNotificationContainer() {

        this.notificationContainer = document.createElement('div');
        this.notificationContainer.classList.add('notification-container');
        this.notificationContainer.setAttribute('id', 'notification-system');

        document.body.appendChild(this.notificationContainer);
    }

    
    // removeNotificationElement() {
        
    //     // Wait until animation finishes to remove the element from the DOM
    //     window.setTimeout(() => {

    //         let notificationContainer = document.getElementById('notification-system');
    //         notificationContainer.parentNode.removeChild(notificationContainer);

    //         console.log('Notifier container removed!');
        
    //     }, this.opacityAnimationInterval);

    // }

    wait(ms) {

        return new Promise(resolve => setTimeout(resolve, ms));
    }

}


function message(text, errorLevel) {

    var theData = {
        detail: {
            text: text,
            errorLevel: errorLevel
        }
    };

    var theMessage = new CustomEvent('messageEvent', theData);
    document.triggerEvent(theMessage);
}