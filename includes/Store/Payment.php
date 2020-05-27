<?php

namespace ClickpdxStore;
class Payment {

    private $resp;
    private $respBody;
    private $lastFour;
    private $currency;
    private $authorizedAmount;
    private $cardType;
    private $status;
    private $paymentProfileId;
    private $hasError;
    private $log;
    

    public function __construct($resp){

        $this->resp = $resp;
        $this->respBody = json_decode($this->resp->getBody());
        $this->status = $this->respBody->status;
        $this->authorizedAmount = $this->respBody->orderInformation->amountDetails->authorizedAmount;
        $this->cardType = $this->respBody->paymentAccountInformation->card->type;
        $this->currency = $this->respBody->orderInformation->amountDetails->currency;
    }

    public function setPaymentProfileId($id){
        $this->paymentProfileId = $id;
    }

    public function setLastFour($lastfour){
        $this->lastFour = $lastfour;
    }
    
    public function setError($log){
        $this->hasError = true;
        $this->statusCode = $this->resp->getStatusCode();
        $this->log = $log;
    }

    /**
     * toJson will return json for either paymentRespnse with errors or paymentResponse without errors
     * 
     */
    public function toJson(){
        $info = array(
            "status"            => $this->status,
            "authorizedAmount"  => $this->authorizedAmount,
            "lastFour"          => $this->lastFour,
            "cardType"          => $this->cardType,
            "currency"          => $this->currency,
            "paymentProfileId"  => $this->paymentProfileId
        );

        $error = array(
            "status"    => "Server returned a " . $this->status . "status code.",
            "body"          => "Response Body: <pre>" . print_r($this->respBody,true) . "</pre>",
            "curlInfo"      => "Curl Info: <pre>" . print_r($this->log,true) . "</pre>"
        );

        return json_encode($this->hasError ? $error : $info);
    }
}