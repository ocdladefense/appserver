<?php

namespace ClickpdxStore;
class Customer {

    private $userId;
    private $customerProfileId;
    private $firstName;
    private $lastName;
    private $birthDate;
    private $email;

    public function __construct($userId) {
        $this->userId = $userId;
    }

    //Getters

    public function getUserId() {
        if($this->userId != null)
            return $this->userId;
        return null;
    }

    public function getProfileId() {
        if($this->customerProfileId != null)
            return $this->customerProfileId;
        return null;
    }

    public function getFirstName() {
        if($this->firstName != null)
            return $this->firstName;
        return null;
    }

    public function getLastName() {
        if($this->lastName != null)
            return $this->lastName;
        return null;
    }

    public function getEmail() {
        if($this->email != null)
            return $this->email;
        return null;
    }

    public function getBirthDate() {
        if($this->birthDate != null)
            return $this->birthDate;
        return null;
    }


    //Setters


    public function setCustomerProfileId($profileId){
        $this->customerProfileId = $profileId;
    }

    public function setFirstName($firstName){
        $this->firstName = $firstName;
    }

    public function setLastName($lastName){
        $this->lastName = $lastName;
    }

    public function setBirthDate($date){
        $this->birthDate = $date;
    }

    public function setEmail($email){
        $this->email = $email;
    }
}