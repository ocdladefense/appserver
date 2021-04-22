class FilePreview {

    FILE_PREVIEW = 'file-preview';

    FILE_PREVIEW_LABEL = 'file-preview-label';

    renderLocationId;

    previewArea;

    previewAreaStatus;

    previewAreaContainer;

    listeners = {  // Default listeners

        filesent: this.handleFileSent,

        filereceived: this.handleFileReceived,

        click: this.handleClick
    };


    constructor() {

        this.init();
    }


    init() {

        this.renderPreviewArea();
    }


    reset() {

        this.unrenderAllFilePreviews();

        this.previewArea.classList.remove('file-preview-area-show');
    }

    
    handleFileSent(e) {

        let files = e.detail;

        this.previewArea.classList.add('file-preview-area-show');

        for (const file of files) {

            this.renderFileSentPreview(file);
        }
    }


    handleFileReceived(e) {

        window.setTimeout(() => { // Use setTimeout to simulate longer file upload times, remove after testing

            let files = e.detail.files;

            this.previewAreaStatus.innerText = 'Upload Successful!';

            for (const file of files) {

                this.renderFileReceivedPreview(file);
            }

        }, 1000);
    }


    handleClick(e) {

        if (e.target.dataset.filePreviewAreaClose) {

            this.reset();
        }
    }


    unrenderAllFilePreviews() {

        while (this.previewAreaContainer.firstChild) {

            this.previewAreaContainer.removeChild(this.previewAreaContainer.firstChild);
        }
    }


    renderPreviewArea() {

        // Setup Preview Area
        this.previewArea = document.createElement('div');
        this.previewArea.classList.add('file-preview-area');
        this.previewArea.classList.add('noselect');


        // Setup Header
        let previewAreaHeader = document.createElement('div');
        previewAreaHeader.classList.add('file-preview-area-header');


        // Setup Status
        this.previewAreaStatus = document.createElement('p');
        this.previewAreaStatus.classList.add('file-preview-area-status');
        this.previewAreaStatus.innerText = 'Files uploading...';

        previewAreaHeader.appendChild(this.previewAreaStatus);


        // Setup Close Button
        let previewAreaClose = document.createElement('div');
        previewAreaClose.classList.add('file-preview-area-close');
        previewAreaClose.dataset.filePreviewAreaClose = true;  // Used in click handler


        let previewAreaXSpan = document.createElement('span');
        previewAreaXSpan.dataset.filePreviewAreaClose = true;  // Used in click handler
        previewAreaXSpan.innerText = 'X';

        previewAreaClose.appendChild(previewAreaXSpan);

        previewAreaHeader.appendChild(previewAreaClose);


        // Attach Header
        this.previewArea.appendChild(previewAreaHeader);


        // Setup Previews Container
        this.previewAreaContainer = document.createElement('div');
        this.previewAreaContainer.classList.add('file-preview-area-container');

        // Attach Previews Container
        this.previewArea.appendChild(this.previewAreaContainer);


        // Attach Preview Area
        document.body.appendChild(this.previewArea);
    }


    renderFileSentPreview(file) {

        let preview = document.createElement('div');
        preview.classList.add('file-preview-container');
        preview.dataset.fileName = file.name;

        let previewIcon = document.createElement('div');
        previewIcon.classList.add('file-preview-icon');
        previewIcon.classList.add('spinner');
        preview.appendChild(previewIcon);

        let previewText = document.createElement('p');
        previewText.classList.add('file-preview-label');
        previewText.innerText = file.name;

        preview.appendChild(previewText);

        this.previewAreaContainer.appendChild(preview);
    }



    renderFileReceivedPreview(file) {

        // Find a preview with the file name that isn't already done uploading
        // and change it from a loading icon to a done icon
        let preview = this.previewAreaContainer.querySelector(`.file-preview-container[data-file-name='${file.name}']:not(.done)`);

        if (preview) {

            preview.classList.add('done');
            let previewIcon = preview.querySelector('.file-preview-icon');
            previewIcon.classList.remove('spinner');
            previewIcon.classList.add('uploaded');
        }
    }
}