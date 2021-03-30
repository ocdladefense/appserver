<?php
namespace Salesforce;

use File\File as File;

class Attachment extends SalesforceFile { // implements ISObject

    public $ParentId;
    public $Id;
    public $isLocal = false;

    public function __construct($id = null){ // Maybe the default constructor takes the Id.

        $this->Id = $id;
    }

    public function setParentId($id){

        $this->ParentId = $id;
    }

    public static function fromFile(File $file){

        $sfFile = new Attachment();
        $sfFile->setPath($file->getPath());
        $sfFile->isLocal = true;

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
