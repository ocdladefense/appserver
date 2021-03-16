<?php

namespace Salesforce;

use File\File;

class SalesforceFile extends File {

    protected $SObjectName;

    protected $Id;

    protected $Name;

    public function getId(){

        return $this->Id;
    }

    public function getSObjectName(){
        return $this->SObjectName;
    }

    public function getSObject() {

        return get_object_vars($this);
    }

}