<?php

namespace ClickpdxStore;
class Customer {

    private $userId;
    private $customerProfileId;
    private $firstName;
    private $lastName;
    private $address;
    private $city;
    private $state;
    private $zip;
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

    public function getAddress() {
        return null == $this->address ? "" : $this->address;
    }

    public function getCity() {
        return null == $this->city ? "" : $this->city;
    }

    public function getState() {
        return null == $this->state ? "" : $this->state;
    }

    public function getZip() {
        return null == $this->zip ? "" : $this->zip;
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

    public function setAddress($address){
        $this->address = $address;
    }

    public function setCity($city){
        $this->city = $city;
    }

    public function setState($state){
        $this->state = $state;
    }

    public function setZip($zip){
        $this->zip = $zip;
    }

    public function setBirthDate($date){
        $this->birthdate = $date;
    }

    public function setEmail($email){
        $this->email = $email;
    }
}