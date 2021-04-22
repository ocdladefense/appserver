<?php

namespace File;

class FileHandler {

    protected $basePath;
    protected $acceptedFileTypes;
    protected $maxUploadSize;
    protected $appId;
    protected $userId;

    public function __construct($config){
        $this->maxUploadSize = (int)(ini_get("upload_max_filesize")*1024) *1000;
        $this->acceptedFileTypes = $config["fileTypes"];
        $this->basePath = $config["path"];
        $this->appId = $config["appId"];
        $this->userId = $config["userId"];
    }

    public function move($file1, $file2) {

        move_uploaded_file($file1->getPath(), $file2->getPath());
    }


    public function getTargetFile($file, $name = null){ 

        $target = clone($file);

        $name = !empty($name) ? $name : $target->getName();

        if(file_exists($this->getTargetPath() . "/" . $name)){

            $updatedName = $this->incrementFileName($name);

            $file->setName($updatedName); // Used to immediately display modified file name
            $target->setName($updatedName);
        }

        $target->setPath($this->getTargetPath() . "/" . $target->getName());

        return $target;
    }

    public function createDirectory(){
        $targetPath = $this->getTargetPath();

        if(!file_exists($targetPath)) {

            mkdir($targetPath, 0777, true);
        }
    }

    public function validate($file){

        if($file->getSize() > $this->maxUploadSize){

            throw new Exception("FILE SIZE ERROR: Upload size cannot exceed " . ini_get("upload_max_filesize") . ".");
        }

        if(!$this->isAcceptedFileType($file->getExt())){

            throw new Exception("FILE TYPE ERROR: the file" . $file->getPath() . "/" . $file->getName() . "has an type of" . $file->getExt() . " which is not a supported file type");
        }
    }

    public function incrementFileName($name){

        $nameParts = explode(".", $name);
        $ext = array_pop($nameParts);
        $tmpName = implode(".", $nameParts);
        $increment = 1;

        while(file_exists($this->getTargetPath() . "/" . $tmpName . "(" . $increment . ")." . $ext)){

            $increment++;
        }

        return $tmpName . "(" . $increment . ")." . $ext;
    }

    private function isAcceptedFileType($type){

        $types = $this->acceptedFileTypes;

        return in_array($type, $types);
    }

    //GETTERS

    public function getTargetPath() {

        return $this->basePath;
    }

    public function getSourcePaths() {

        return array($this->basePath);
    }

    public function getAcceptedFileTypes(){

        return $this->acceptedFileTypes;
    }

    public function getMaxUploadSize(){

        return $this->maxUploadSize;
    }
}