<?php

namespace ClickpdxStore;
class CreditCard{

    private $cardNumber;
    private $expirationMonth;
    private $expirationYear;
    private $cardType;
    private $securityCode;

    public function __construct(){}

    //Getters
    public function getCardNumber(){
        return null == $this->cardNumber ? "" : $this->cardNumber;
    }

    public function getExpirationMonth(){
        return null == $this->expirationMonth ? "" : $this->expirationMonth;
    }

    public function getExpirationYear(){
        return null == $this->expirationYear ? "" : $this->expirationYear;
    }

    public function getCardType(){
        return null == $this->cardType ? "" : $this->cardType;
    }
    
    public function getSecurityCode(){
        return null == $this->securityCode ? "" : $this->securityCode;
    }

    //Setters
    public function setCardNumber($cardNumber){
        $this->cardNumber = $cardNumber;
    }

    public function setExpirationMonth($expirationMonth){
        $this->expirationMonth = $expirationMonth;
    }

    public function setExpirationYear($expirationYear){
        $this->expirationYear = $expirationYear; 
    }

    public function setCardType($cardType){
        $this->cardType = $cardType;
    }

    public function setSecurityCode($securityCode){
        $this->securityCode = $securityCode;
    }
}