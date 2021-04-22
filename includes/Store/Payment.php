<?php

namespace ClickpdxStore;
class Payment {

    private $lastFour;
    private $currency;
    private $amount;
    private $cardType;
    private $status;
    //token, sourceId, customerId
    private $token;

    
    public function __construct($token){
        $this->token = $token;
    }

    public function setToken($token){
        $this->token = $token;
    }
    public function setAmount($amount){
        $this->amount = $amount;
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