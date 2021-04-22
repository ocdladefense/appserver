<?php


class FileSystem {


    public function __construct(){}
    
    public function hasDirectory($parentDir,$childDir){
        $dirs = filterScanResults(scandir($parentDir));

        if(in_array($childDir, $dirs))
            return true;
        return false;

    }
    public function createDir(){
        
    }
    function createRequiredDirectories(){
        $dirs = scandir(getPathToContent());
        if(!in_array("uploads",$dirs)){
            mkdir(getPathToContent()."/uploads");
        }
        $dirs = scandir(getPathToContent());
        if(!in_array("json",$dirs)){
            mkdir(getPathToContent()."/json");
        }
        $dirs = scandir(getPathToContent()."/uploads");
        if(!in_array("chapter-material-files",$dirs)){
            mkdir(getPathToContent()."/uploads/chapter-material-files");
        }
        $dirs = scandir(getPathToContent()."/json");
        if(!in_array("json-chapter-materials",$dirs)){
            mkdir(getPathToContent()."/json/json-chapter-materials");
        }
    }
}