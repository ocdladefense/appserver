<?php

namespace Salesforce;

use File\File;

class SalesforceFile extends File {

    public $isLocal;

    protected $SObjectName;

    protected $Id;


    public function getId(){

        return $this->Id;
    }

    public function getSObjectName(){
        return $this->SObjectName;
    }

    public function getInstanceName(){

        return get_class($this);
    }

    public function getSObject() {

        return get_object_vars($this);
    }

}