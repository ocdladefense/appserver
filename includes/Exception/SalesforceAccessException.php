<?php

class SalesforceAccessException extends Exception
{
    public function __construct($message){
        $this->message = "Salesforce Access Exception: ".$message;
    }
}