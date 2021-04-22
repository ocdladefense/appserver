const IncomingFileList = (function() {

    let incomingFileList = {

        add: function(fileOrFileList) {

            if (fileOrFileList instanceof FileList) {
                
                for (let file of fileOrFileList) {

                    this.files.push(file);
                }

            } else if (fileOrFileList instanceof File) {

                this.files.push(fileOrFileList);

            } else {

                console.error("IncomingFileList can only add objects of type 'File' or 'FileList'.");
            }
        },

    
        getFiles: function() {

            return this.files;
        },


        size: function() {

            return this.files.length;
        },
        

        isEmpty: function() {

            return this.files.length === 0;
        }

    }

    
    function IncomingFileList() {

        this.files = [];
    }


    IncomingFileList.prototype = incomingFileList;

    IncomingFileList.newFromFileData = function(files) {

        let fileList = new IncomingFileList();

        files.forEach( file => {

            let theFile = new File([''], file.name, {type: file.ext});
            theFile.fileSize = file.size;
            theFile.creationDate = file.creationDate;
            fileList.add(theFile);
        });

        return fileList;
    };

    return IncomingFileList;
})();



