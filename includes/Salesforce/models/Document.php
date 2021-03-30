<?php
namespace Salesforce;

use File\File as File;

class Document extends SalesforceFile{

    public $parentId;

    private $metadata = array(
        "Description" => "Marketing brochure for Q1 2011",
        "Keywords" => "marketing,sales,update",
        "FolderId" => "005D0000001GiU7",
        "Name" => "Marketing Brochure Q1",
        "Type" => "pdf"
    );

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