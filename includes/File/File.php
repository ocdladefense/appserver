<?php

namespace File;

class File implements \JsonSerializable {

    private $name;

    private $type;

    private $size;

    private $ext;

    private $path;

    private $error;

    private $creationDate;

    private $content;

    private $metadata;

    public function __construct($fileName, $path = null){
        $this->name = $fileName;
        $this->path = $path;
    }

    public static function fromParams($params){

        $file = !empty($params["name"]) ? new File($params["name"]) : self::fromPath($params["path"]);
        $file->type = $file->getType();
        $file->path = $params["path"];
        $file->size = $params["size"];
        $file->error = $params["error"];
        $file->ext = $file->getExt();
        $file->creationDate = $file->getCreationDate();

        return $file;
    }

    //Path will always inclued the filename
    //if our file is foobar.csv
    //then our file path is going to be /path/to/foobar.csv
    public static function fromPath($path){

        $pathParts = explode("/", $path);
        $file = new File($pathParts[count($pathParts) -1], $path);

        return $file;
    }

    //SETTERS
    public function setName($fileName){

        $this->name = $fileName;
    }

    public function setType($fileType){

        $this->type = $fileType;
    }

    public function setSize($fileSize){

        $this->size = $fileSize;
    }

    public function setPath($tmpPath){

        $this->path = $tmpPath;
    }

    public function setError($error){

        $this->error = $error;
    }

    public function setContent($content){

        $this->content = $content;
    }

    public function setMetadata($data){

        $this->metadata = array(
            "Name" => $this->name,
            "Type" => $this->getExt()
        );
    }


    //GETTERS
    public function getName(){

        return  $this->name;
    }

    public function getMetadata(){

        return $this->metadata;
    }

    public function getType(){

        return $this->exists() ? mime_content_type($this->path) : $this->type;
    }

    public function getExt(){

        $nameParts = explode(".", $this->name);
        $ext = $nameParts[count($nameParts) -1];

        return $ext;
    }

    public function getSize(){

        return $this->exists() ? filesize($this->path) : $this->size;
    }

    public function getPath(){

        return $this->path;
    }

    public function getError(){

        return $this->error;
    }

    public function getCreationDate(){

        return $this->exists() ? date("F d, Y", filemtime($this->getPath())) : $this->creationDate;
    }

    public function getContent(){

        return $this->content;
    }

    //ADDITIONAL METHODS

    public function exists(){
        
        return file_exists($this->path);
    }

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}

    public function jsonSerialize()
    {
        return array(
             'name' => $this->getName(),
             'type' => $this->getType(),
             'ext' => $this->getExt(),
             'size' => $this->getSize(),
             'path' => $this->getPath(),
             'error' => $this->getError(),
             'creationDate' => $this->getCreationDate()
        );
    }
}