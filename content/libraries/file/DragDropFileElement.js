class DragDropFileElement extends IFormElement {

    DROP_AREA_ID = 'drop-area-' + document.querySelectorAll("[id^='drop-area-']").length; // allows multiple drop areas on a page

    options = {};

    allowMultipleUpload = false;

    incomingFileList;

    renderLocationId;

    hasForm = false;

    form;

    dropAreaElement;

    submitButtonElement;

    dragCount = 0;
    

    constructor(options) {

        super();

        if (options) {

            this.configure(options);
        }

        // A helper class to manage files that are uploaded by the user.
        this.incomingFileList = new IncomingFileList();


        // this.toggleUploaderVisibility = this.renderForm && !!options.toggleUploaderVisibility ? true : false;

        this.name = 'files[]'; // PHP specifically requires this name for multiple file uploads
    }


    configure(options) {

        this.options = options;

        this.allowMultipleUpload = options.allowMultipleUpload || false;
    }



    getName() {

        return this.name;
    }


    getValue() {

        return this.incomingFileList.getFiles();
    }

    getDropAreaElement() {

        return !!this.hasForm ? this.dropAreaElement : document.getElementById(this.renderLocationId);
    }

    reset() {

        // if (this.toggleUploaderVisibility) {

            // this.hide();

            // this.hideSubmitButton();
        // }

        // this.showDropLabel();

        this.incomingFileList = new IncomingFileList();
        
        if (this.resetFn) {

            this.resetFn();
        } 
    }

    ///////
    // RENDER METHODS
    ///////

    render(formId) {

        // this.renderLocationId = formId;
        let elem = document.getElementById(formId) || document;

        elem = this.getDefaultContainer();

        document.body.appendChild(elem);
        // if (renderLocationElement.nodeName === 'FORM') {

        //     console.warn('A form id was passed to file uploader.');
        //     this.hasForm = true;

        //     this.form = renderLocationElement;
        //     this.renderWithExistingForm(this.form);

        // } else {

            this.addEventListeners(elem);
        // }

    }


    renderWithExistingForm(form) {

        // this.validateFormAttributes(form);

        this.renderDropArea(form, true);
    }

    renderDropArea(form, shouldPrepend) {



        // Add various drag/drop events to the file drop element
        this.addEventListeners(this.dropAreaElement);

        if (shouldPrepend) {

            form.prepend(this.dropAreaElement);

        } else {

            form.appendChild(this.dropAreaElement);
        }

    }

    getDefaultContainer() {

        // Create the file drop label
        let dropLabel = document.createElement('p');
        dropLabel.classList.add('drop-area-label');
        dropLabel.innerText = 'Click or drag files here.';
        dropLabel.innerText += !this.allowMultipleUpload ? '\nMultiple file uploads are not allowed.' : '';


        // Create the file drop element and add it to the form
        let dropAreaElement = document.createElement('div');
        dropAreaElement.id = this.DROP_AREA_ID;
        dropAreaElement.classList.add('drop-area');
        dropAreaElement.classList.add('drop-area-default');

        // if (!this.toggleUploaderVisibility) {

        //     this.show();
        // }

        dropAreaElement.appendChild(dropLabel);

        return dropAreaElement;
    }


    ///////
    // FILE METHODS
    ///////
    
    addFiles(fileList) {

        if (!this.allowMultipleUpload && (fileList.length > 1 || (this.incomingFileList.size() + fileList.length) > 1)) {

            alert('Multiple file uploads are not allowed.');
            return;
        }


        // if (this.toggleUploaderVisibility) {

        //     this.showSubmitButton();
        // }

        // this.hideDropLabel();

        this.incomingFileList.add(fileList);


        // if (!this.hasForm) {

        //     this.send();

        //     this.reset();

        //     return;
        // }

        triggerEvent('fileadd', fileList);
    }



    ///////
    // ATTACH EVENTS
    ///////

    handleEvent(e) {

        e.preventDefault();
        e.stopPropagation();
        let type = e.type;
        let container = e.currentTarget;
        let target = e.target;
        let dataTransfer = e.dataTransfer;
        let files = dataTransfer ? dataTransfer.files : target.files; // 'FileList' from a drop or a click

        if (['dragenter'].includes(type)) { // Removed 'dragover' event, might need it

            dataTransfer.dropEffect = "copy";
            this.highlight(type, container, target, files);
        }

        if (['dragleave'].includes(type)) {

            if (!target.classList.contains('drop-area')) {
                
                return;
            }

            // console.log(this.dragCount++);
            // console.log(type);
            // console.log('Target is: ', target);
            // console.log('Container is: ', container);

            this.unhighlight(type, container, target, files);
        }

        if (type === 'drop') {

            // console.log(e);
            this.unhighlight(type, container, target, files);
            this.handleFiles(type, container, target, files);
        }

        // // Show the drop area when user drags a file over the page
        // if (['dragenter', 'dragleave', 'drop'].includes(type)) {
            
        //     this.handleUploaderVisibility(type, container, target, files);
        // }

    }

    addEventListeners(elem) {

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {

            document.addEventListener(eventName, this, { capture: true} ); 
        });



        // document.addEventListener(eventName, this.preventDefaults, false);

    }



    ///////
    // EVENT HANDLERS
    ///////


    clickToUpload() {
        if (this.hasForm) {

            // Handle click to upload
            elem.addEventListener('click', (e) => {

                this.openFileDialog.call(this, e);
            });

        }
    }


    onReset(fn) {

        this.resetFn = fn;
    }


    handleUploaderVisibility(target, type) {

        if (type === "drop") {

            this.hide(target);
            return;
        }


        this.dragCount += (type === "dragenter" ? 1 : -1); // dragCount is used to prevent 'flickering' of drop area

        if (this.dragCount === 1) {

            this.show(target);

        } else if (this.dragCount === 0) {
        
            this.hide(target);
        }
    }


    show() {

        document.body.classList.add('highlight');
    }

    hide() {

        document.body.classList.remove('highlight');
    }

    highlight(type, container, target, files) {

        let elem = container === document ? document.body : container;

        elem.classList.add('highlight');
    }
     
      
    unhighlight(type, container, target, files) {

        let elem = container === document ? document.body : container;

        elem.classList.remove('highlight');
    }


    handleFiles(type, container, target, files) {

        this.addFiles(files);
    }


    // show(type, container, target, files) {

    //     target.classList.add('show-drop-area');
    //     // this.submitButtonElement.classList.add('show-drop-submit');
    // }


    // hide(type, container, target, files) {

    //     this.dragCount = 0; // Ensure that drag count is reset after hiding
    //     target.classList.remove('show-drop-area');
    //     // this.submitButtonElement.classList.remove('show-drop-submit');
    // }


    showDropLabel() {

        let dropAreaLabel = this.getDropAreaElement().querySelector('.drop-area-label');
        dropAreaLabel.classList.remove('hide-label');
    }


    hideDropLabel() {

        let dropAreaLabel = this.getDropAreaElement().querySelector('.drop-area-label');
        dropAreaLabel.classList.add('hide-label');
    }


    showSubmitButton() {

    }


    hideSubmitButton() {

    }
    
    
    openFileDialog() {  
        // this function must be called from  a user
        // activation event (ie an onclick event)

        // Create an input element
        let inputElement = document.createElement("input");

        // Set its type to file
        inputElement.type = "file";


        if (this.options.allowMultipleUpload) {

            inputElement.setAttribute('multiple', '');
        }

        // Set accept to the file types you want the user to select. 
        // Include both the file extension and the mime type
        //inputElement.accept = accept;

        // set onchange event to call callback when user has selected file
        inputElement.addEventListener("change", (e) => {

            this.handleFiles.call(this, e);
        
        }, false);

        // dispatch a click event to open the file dialog
        inputElement.dispatchEvent(new MouseEvent("click"));

    }



    ///////
    // VALIDATION METHODS
    ///////

    /*
    validateFormAttributes(form) {

        if (form.method !== 'post') {

            console.warn('Form with id ' + form.id + ' must have a method type of "post" for file uploader to function properly.');
        }

        if (form.enctype !== 'multipart/form-data') {

            console.warn('Form with id ' + form.id + ' must have an enctype of "multipart/form-data" for file uploader to function properly.');
        }
    }
    */

}
