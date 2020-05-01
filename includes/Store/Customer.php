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
        return $this->userId;
    }

    public function getProfileId() {
        return $this->customerProfileId;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getBirthDate() {
        return $this->birthDate;
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