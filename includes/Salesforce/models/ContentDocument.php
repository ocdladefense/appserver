<?php
namespace Salesforce;

use File\File as File;

class ContentDocument extends SalesforceFile { // implements ISObject

    public $SObjectName = "ContentVersion";

    protected $Id;

    private $ContentDocumentId;

    private $LinkedEntityId;

    public $isLocal = false;

    public function __construct($id = null){ // Maybe the default constructor takes the Id.

        $this->Id = $id;
    }

    public function setId($id){

        $this->Id = $id;
    }

    public function setLinkedEntityId($id){

        $this->LinkedEntityId = $id;
    }

    public function setContentDocumentId($id){

        $this->ContentDocumentId = $id;
    }

    public function getId(){

        return $this->Id;
    }

    public function getLinkedEntityId(){

        return $this->LinkedEntityId;
    }

    public function getContentDocumentId(){

        return $this->ContentDocumentId;
    }
    public static function fromFile(File $file){

        $sfFile = new ContentDocument();
        $sfFile->setPath($file->getPath());
        $sfFile->setName($file->getName());
        $sfFile->isLocal = true;

        return $sfFile;
    }

    public static function fromArray($obj){

        $sfFile = new Attachment();
        $sfFile->Id = $ojb["id"];

        return $sfFile;
    }

    public static function fromJson($json){

        $obj = json_decode($json);

        $sfFile = new Attachment();
        $sfFile->Id = $ojb->id;

        return $sfFile;
    }

    // Always produce an object that is compatible with the salesforce simple object endpoint.
    public function getSObject(){ 

        return array(
            "Title"              => $this->getName(),
            "ContentDocumentId"  => $this->getContentDocumentId(),
            "PathOnClient"       => $this->getPath()
        );
    }
}
