<?php

class SalesforceAuthResult {

    private $resp;
    private $instance_url;
    private $access_token;
    private $isSuccess = true;

    public function __construct($resp){
        $body =json_decode($resp->getBody(), true);

        if(!$resp->isSuccess()){ //and tries != 4 ?
            $this->isSuccess =false;
            $error = array(
                0 => "Invalid URl",
                400 => "Bad Oauth_url/Request",
                401 => "Unauthorized - Auth required",
                403 => "Forbidden - Server Request Unfulfilled",
                404 => "Oauth_url Not Found",
                405 => "Method type not allowed",
                406 => "Request content not acceptable"

            );
            if(isset($body["error"]) || isset($body["error_description"])){
                $error = array(
                    "Code"=>$resp->getStatusCode(),
                    "Status"=>$error[$resp->getStatusCode()],
                    "error"=>$body["error"],
                    "error_message"=>$body["error_description"]
                );
                //throw new SalesforceAuthException($error);
            }else{
                //throw new SalesforceAuthException($error[$resp->getStatusCode()]);
            }
        }
        

        $this->access_token = $body["access_token"];
        $this->instance_url = $body["instance_url"];
        
    }
    public function isSuccess(){
        return $this->isSuccess;
    }
    public function getAccessToken(){
        return $this->access_token;
    }
    public function getInstanceUrl(){
        return $this->instance_url;
    }
}