<?php

namespace Salesforce;



abstract class SObject {

    protected $Id;

    protected $Name;

    protected $SObjectName;

    public function getSObjectName(){
        return $this->SObjectName;
    }


    public function getSObject() {

        return get_object_vars($this);
    }

    public function getId(){

        return $this->Id;
    }

    public function getName(){

        return $this->Name;
    }

    public function setId($id){

        $this->Id = $id;
    }

    public function setName($name){

        $this->Name = $name;
    }





}