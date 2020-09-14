class FileListTable {

    STYLE_CLASSES = {

        wrapper: 'results-table-wrap',
        table: 'results-table'
    };

    options = {
        tableMapping: {
            "File Name": "name",
            "File Type": "type",
            "File Size": "fileSize",
            "Creation Date": "creationDate"
        },
        actions: {
            "download": {
                "headerLabel": "Download",
                "buttonLabel": "Download File",
                "styleClasses": ['download-button'],
                "actionUrl": "/download"
            },
            "delete": {
                "headerLabel": "Delete",
                "buttonLabel": "Delete File",
                "styleClasses": ['delete-button'],
                "actionUrl": "/delete"
            }
        }
    };
    
    tableMapping;

    actions;

    renderLocationId;

    wrapperElement;

    tableElement;

    tableHeaderElement;

    files;

    userData;

    constructor(renderLocationId, options) {

        this.options = options || this.options;

        this.tableMapping = this.options.tableMapping || null;

        this.actions = this.options.actions || null;

        this.renderLocationId = renderLocationId;
    }


    setTableMapping(tableMapping) { this.tableMapping = tableMapping; }

    setFiles(files) { this.files = files; }

    setUserData(data) { this.userData = data; };

    generateTableMappingFromFile() {

        if (!this.files.getFiles()) {

            console.error('No file data is set!');
        }

        let tableMapping = {};

        let file = this.files.getFiles()[0];

        for (const fieldName in file) {

            let columnName = fieldName.replace( /([A-Z])/g, " $1" ); // Add spaces between camelCase props

            tableMapping[columnName] = fieldName;
        }

        this.tableMapping = tableMapping;
    }

    
    render(fileList) {

        this.files = fileList.getFiles();

        if (!this.tableMapping) {

            this.generateTableMappingFromFile();
        }


        let renderLocationElement = !!this.renderLocationId ? document.getElementById(this.renderLocationId) : document.body;

        if(!renderLocationElement) {

            console.error('No element with the id "' + this.renderLocationId + '". File list not rendered.');
            return;
        }

        if ( !(this.wrapperElement = renderLocationElement.querySelector('.' + this.STYLE_CLASSES.wrapper)) ) {

            this.wrapperElement = document.createElement('div');
            this.wrapperElement.classList.add(this.STYLE_CLASSES.wrapper);
        }
        

        this.renderTable();

        this.renderTableHeader();



        this.files.forEach(file => {

            this.addRow(file);
        });

        this.wrapperElement.appendChild(this.tableElement);

        renderLocationElement.appendChild(this.wrapperElement);
    }


    renderTable() {

        if ( !(this.tableElement = this.wrapperElement.querySelector('.' + this.STYLE_CLASSES.table)) ) {

            this.tableElement = document.createElement('div');
            this.tableElement.classList.add(this.STYLE_CLASSES.table);
            this.tableElement.addEventListener('fileuploadresponse', (e) => {

                console.log('In event...');
                this.handleFileUpload.call(this, e);
            });

        } else { // table has already been rendererd, clear it to redisplay results

            let rows = this.tableElement.querySelectorAll('.table-row');

            rows.forEach(row => {

                row.parentNode.removeChild(row);
            });
        }

    }


    renderTableHeader() {

        this.tableHeaderElement = document.createElement('ul');
        this.tableHeaderElement.classList.add('table-row');

        for (const columnName in this.tableMapping) {

            let cell = document.createElement('li');
            cell.classList.add(...['table-cell', 'column-header']);
            cell.innerText = columnName;
            this.tableHeaderElement.appendChild(cell);
        }

        for (const actionKey in this.actions) {

            let cell = document.createElement('li');
            cell.classList.add(...['table-cell', 'column-header']);
            cell.innerText = this.actions[actionKey].headerLabel;
            this.tableHeaderElement.appendChild(cell);
        }

        this.tableElement.appendChild(this.tableHeaderElement);
    }


    addRow(file, shouldPrepend) {

        let row = document.createElement('ul');
        row.classList.add('table-row');

        for (const columnName in this.tableMapping) {

            let fieldName = this.tableMapping[columnName];

            let cell = document.createElement('li');
            cell.classList.add('table-cell');
            cell.innerText = file[fieldName];

            row.appendChild(cell);
        }

        if (this.actions) {

            this.attachActionButtons(row, file);
        }


        if (shouldPrepend) {

            this.tableElement.insertBefore(row, this.tableHeaderElement.nextSibling);

        } else {

            this.tableElement.appendChild(row);
        }

    }


    addRows(fileList, shouldPrepend) {

        fileList.getFiles().forEach(file => {

            this.addRow(file, shouldPrepend);
        });
    }


    handleFileUpload(e) {

        let data = e.detail;

        console.log(data);

    }


    attachActionButtons(row, file) {

        for (const actionKey in this.actions) {

            let action = this.actions[actionKey];

            let form = document.createElement('form');
            form.method = 'POST';
            form.action = action.actionUrl;

            if (this.userData) {

                for (const valueKey in this.userData) {

                    let hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = valueKey;
                    hiddenInput.value = this.userData[valueKey];
                    
                    form.appendChild(hiddenInput);
                }

            }

            let fileNameInput = document.createElement('input');
            fileNameInput.type = 'hidden';
            fileNameInput.name = 'filename';
            fileNameInput.value = file.name;
            
            form.appendChild(fileNameInput);

            let submit = document.createElement('input');
            submit.type = 'submit';
            submit.classList.add(...action.styleClasses);
            submit.value = action.buttonLabel;

            form.appendChild(submit);

            let cell = document.createElement('li');
            cell.classList.add('table-cell');
            cell.appendChild(form);

            row.appendChild(cell);
        }

    }

}