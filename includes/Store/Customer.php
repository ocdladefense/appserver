<?php

namespace ClickpdxStore;
class Customer { 

    private $userId;
    private $customerProfileId;
    private $firstName;
    private $lastName;
    private $address1;
    private $address2;
    private $city;
    private $state;
    private $zip;
    private $country;
    private $birthdate;
    private $email;
    private $phoneNumber;
    private $card;

    public function __construct($userId = null) {
        $this->userId = $userId;
    }

    //Getters

    public function getUserId() {
        return null == $this->userId ? "" : $this->userId;
    }

    public function getCustomerProfileId() {
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

    public function getAddress1() {
        return null == $this->address1 ? "" : $this->address1;
    }

    public function getAddress2() {
        return null == $this->address2 ? "" : $this->address2;
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

    public function getCountry() {
        return null == $this->country ? "" : $this->country;
    }

    public function getBirthDate() {
        return null == $this->birthdate ? "" : $this->birthdate;
    }

    public function getPhoneNumber() {
        return null == $this->phoneNumber ? "" : $this->phoneNumber;
    }

    public function getCard(){
        return null == $this->card ? null : $this->card;
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

    public function setAddress1($address1){
        $this->address1 = $address1;
    }

    public function setAddress2($address2){
        $this->address2 = $address2;
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

    public function setCountry($country){
        $this->country = $country;
    }

    public function setBirthDate($date){
        $this->birthdate = $date;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function setPhoneNumber($phoneNumber){
        $this->phoneNumber = $phoneNumber;
    }

    public function setCard($card){
        $this->card = $card;
    }
}