<?php
// this class is used for creating a new custom exception when validation in DomainManager fails
class DomainManagerException extends Exception {

    function __construct($domainRecord, $domainRecordStatus) {

        $this->setMessage("The .JSON file for ".$domainRecord->domain." is invalid. Please check that the file has a 'domain' key with a 
        value that begins with 'http' and a 'probes' key that contains an array of objects, each with a 'path'.");
    }

    public function setMessage($message){
        $this->message = $message;
    }
}