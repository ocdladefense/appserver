<?php

class RestApiResult {

    private $resp;
    private $instance_url;
    private $access_token;
    private $isSuccess = true;
    private $error;
    private $errorCodes = array(
        0 => "Invalid URl",
        400 => "Bad Oauth_url/Request",
        401 => "Unauthorized - Auth required",
        403 => "Forbidden - Server Request Unfulfilled",
        404 => "Oauth_url Not Found",
        405 => "Method type not allowed",
        406 => "Request content not acceptable"

    );

    private function isOauthResponse($body){
        return isset($body["access_token"]) && isset($body["instance_url"]);
    }

    private function getErrorMsg($statusCode){
        return $this->errorCodes[$statusCode];
    }

    public function __construct($resp){
        $body =json_decode($resp->getBody(), true);

        if($resp->isSuccess() && $this->isOauthResponse($body)){
            $this->access_token = $body["access_token"];
            $this->instance_url = $body["instance_url"];  
        }
        
        if(!$resp->isSuccess()){
            $this->isSuccess = false;

            if(isset($body["error"]) || isset($body["error_description"])){
                $this->error = array(
                    "HttpStatusCode"=>$resp->getStatusCode(),
                    "HttpStatusMessage"=>$this->getErrorMsg($resp->getStatusCode()),
                    "Salesforce_Error"=>$body["error"],
                    "Salesforce_Error_message"=>$body["error_description"]
                );
            }
        }
        
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
    public function getError(){
        return $this->error;
    }
    public function isTokenExpired(){
        return true;
    }
    public function isTokenInvalid(){
        return true;
    }
}