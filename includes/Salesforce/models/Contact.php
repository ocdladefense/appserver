<?php

class Contact {

    public $Id;
    public $FirstName;
    public $LastName;
    public $MailingCity;
    public $MailingState;
    public $Phone;
    public $Email;
    public $Ocdla_Occupation_Field_Type__c;
    public $Ocdla_Organization__c;
    public $AreasOfInterest__r;

    public function __construct(){}

    public static function from_query_result_records($records){

        $contacts = array();

        foreach($records as $record){

            $c = new Contact();
            $c->Id = $record["Id"];
            $c->FirstName = $record["FirstName"];
            $c->LastName = $record["LastName"];
            $c->Ocdla_Occupation_Field_Type__c = $record["Ocdla_Occupation_Field_Type__c"];
            $c->Ocdla_Organization__c = $record["Ocdla_Organization__c"];
            $c->MailingCity = $record["MailingCity"];
            $c->AreasOfInterest__r = $record["AreasOfInterest__r"]["records"];
            $c->MailingState = $record["MailingState"];
            $c->Phone = $record["Phone"];
            $c->Email = $record["Email"];
            
            $contacts[] = $c;
        }

        return $contacts;
    }

    public function getId(){

        return $this->Id;
    }

    public function getFirstName(){

        return $this->FirstName;
    }

    public function getLastName(){

        return $this->LastName;
    }
    
    public function getMailingCity(){

        return $this->MailingCity;
    }
    
    public function getAreasOfInterest(){

        if(!empty($this->AreasOfInterest__r)){

            $interests = array();
            foreach($this->AreasOfInterest__r as $record) $interests[] = $record["Interest__c"];

            return implode(", ", $interests);

        } else {

            return null;
        }
    }

    public function getMailingState(){

        return $this->MailingState;
    }

    public function getPhone(){

        return $this->Phone;
    }

    public function getEmail(){

        return $this->Email;
    }
    public function getOccupationFieldType(){

        return $this->Ocdla_Occupation_Field_Type__c;

    }

    public function getOcdlaOrganization(){

        return $this->Ocdla_Organization__c;
    }
}