<?php

namespace ClickpdxStore;
class Customer {

    private $userId;
    private $customerProfileId;
    private $firstName;
    private $lastName;
    private $birthdate;
    private $email;

    public function __construct($userId = null) {
        $this->userId = $userId;
    }

    //Getters

    public function getUserId() {
        return null == $this->userId ? "" : $this->userId;
    }

    public function getProfileId() {
        return null == $this->customerProfileId ? "" : $this->customerProfileId;
    }

    public function getFirstName() {
        return null == $this->firstName ? "" : $this->firstName;
    }

    public function getLastName() {
        return null == $this->lastName ? "" : $this->lastName;
    }

    public function getEmail() {
        return null == $this->email ? "" : $this->email;
    }

    public function getBirthDate() {
        return null == $this->birthdate ? "" : $this->birthdate;
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
        $this->birthdate = $date;
    }

    public function setEmail($email){
        $this->email = $email;
    }
}