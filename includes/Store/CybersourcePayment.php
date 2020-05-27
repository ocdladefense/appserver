<?php

class CybersourcePayment extends Payment{
    public function __construct(){
        $this->authorizedAmount = $this->respBody->orderInformation->amountDetails->authorizedAmount;
    }
    public static function FromResponse($resp){

        $payment = new CybersourcePayment();

        $this->resp = $resp;
        $this->respBody = json_decode($this->resp->getBody());
        $this->status = $this->respBody->status;
        $this->amount = $this->respBody->orderInformation->amountDetails->authorizedAmount;
        $this->cardType = $this->respBody->paymentAccountInformation->card->type;
        $this->currency = $this->respBody->orderInformation->amountDetails->currency;
    }
}