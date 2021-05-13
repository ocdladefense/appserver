<?php
namespace Salesforce;

use File\File as File;

class ContentDocument extends SalesforceFile { // implements ISObject

    public $SObjectName = "ContentVersion";

    protected $Id;

    private $ContentDocumentId;

    private $LinkedEntityId;

    public $isLocal = false;

    public $Name;


    public function __construct($id = null){ // Maybe the default constructor takes the Id.

        $this->Id = $id;
    }

    public static function newFromSalesforceRecord($record){
        //creates an instance/object of ContentDocument by reference using the record id
        $myDocument = new ContentDocument($record["Id"]);
        //retrieves the value in the "Title" Element in the ContentDocument array of record
        $name = $record["ContentDocument"]["Title"];
        //calls setName() passes in the name; giving $myDocument the property $Name
        $myDocument->setName($name);
        
        return $myDocument;
        
    }

    public function setName($name){

        $this->Name = $name;
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
        $sfFile->Id = $obj["id"];

        return $sfFile;
    }

    public static function fromJson($json){

        $obj = json_decode($json);

        $sfFile = new Attachment();
        $sfFile->Id = $obj->id;

        return $sfFile;
    }

    public function getName(){
       
        $name = $this->Name;
        //var_dump($name);exit;
        $parts = explode(".", $name);
        $ext = array_pop($parts); 
        $name = implode(".", $parts);
        $tooLong = strlen($name) > 20;
        $short = substr($name, 0, 10);

        //no extension appended "." prepended to the front of name? PROBABLY WHY THIS RUBBISH MADE NO SENSE!!
        $filename = ($tooLong ? ($short . "...") : ($name.".")) . $ext;


       //var_dump($filename); exit; // dumps appropriate $filename. ~ well did 
       return $filename;
					
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
