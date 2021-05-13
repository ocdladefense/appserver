<?php
namespace Salesforce;

use File\File as File;

class Attachment extends SalesforceFile { // implements ISObject

    public $SObjectName = "Attachment";
    public $ParentId;
    public $Id;
    public $isLocal = false;

    public function __construct($id = null){ // Maybe the default constructor takes the Id.

        $this->Id = $id;
    }

    public function setParentId($id){

        $this->ParentId = $id;
    }

    // public function fromFile(File $file){

    //     $this->setPath($file->getPath());
    //     $this->setName($file->getName());
    //     $this->isLocal = true;
    // }

    public static function fromFile(File $file){

        $sfFile = new Attachment();
        $sfFile->setPath($file->getPath());
        $sfFile->setName($file->getName());
        $sfFile->isLocal = true;

        return $sfFile;
    }

    public static function fromArray($obj){

        $sfFile = new Attachment();
        $sfFile->Id = $obj["id"];

        return $sfFile;
    }

    public static function fromJson($json){

        $obj = json_decode($json);

        $sfFile = new Attachment();
        $sfFile->Id = $obj->id;

        return $sfFile;
    }

    // Always produce an object that is compatible with the salesforce simple object endpoint.
    public function getSObject(){ 

        return array(
            "Name" => $this->getName(),
            "ParentId" => $this->ParentId
        );
    }
}
