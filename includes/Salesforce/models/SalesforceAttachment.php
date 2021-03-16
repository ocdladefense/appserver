<?php
use File\File as File;

class SalesforceAttachment extends File{

    public $parentId;

    public function __construct($path){

        parent::__construct($path);
    }

    public function setParentId($id){

        $this->parentId = $id;
    }

    public function getMetadata(){

        return array(
            "Name" => $this->getName(),
            "ParentId" => $this->parentId
        );
    }
}