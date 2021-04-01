<?php

namespace Salesforce;

use Http\HttpResponse;
use Http\HttpHeader as HttpHeader;

class OAuthResponse extends HttpResponse {

    private $instanceUrl;
    
    private $accessToken;

    private $hasError;
    
    protected static $errorCodes = array(
        0 => "Invalid URl",
        400 => "Bad Oauth_url/Request",
        401 => "Unauthorized - Auth required",
        403 => "Forbidden - Server Request Unfulfilled",
        404 => "Oauth_url Not Found",
        405 => "Method type not allowed",
        406 => "Request content not acceptable"
    );



    public static function fromSession() {
        $body = array();
        $body["access_token"] = $_SESSION["salesforce_access_token"];
        $body["instance_url"] = $_SESSION["salesforce_instance_url"];
        
        return new OAuthResponse($body);
    }


    public function __construct($body = null) {
        
        parent::__construct($body);	

        $body = null != $body ? json_decode($body, true) : null;

        if(!empty($body["error"])){

            $this->hasError = true;
            $this->error = $body["error"];
            $this->errorMessage = $body["error_description"];
        }

        if($body != null){

            $this->accessToken = $body["access_token"];
            $this->instanceUrl = $body["instance_url"]; 
        }
    }

	
    public static function resolveFromSession() {
    
        if(empty($_SESSION["salesforce_instance_url"])) {
            throw new Exception("Instance URL is not set!");
        }
        
        if(empty($_SESSION["salesforce_access_token"])) {
            throw new Exception("Access token is not set!");
        }
    }
    
    
    public function getAccessToken() {
        return $this->accessToken;
    }
    
    
    public function getInstanceUrl() {
        return $this->instanceUrl;
    }

    public function success(){

        return !$this->hasError;
    }

    public function getErrorMessage(){

        return strtoupper($this->error . "_ERROR:" . $this->errorMessage);
    }
}