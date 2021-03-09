<?php



namespace Salesforce;


use Http\HttpResponse;






class OAuthResponse extends HttpResponse {

    
    private $instanceUrl;
    
    private $accessToken;
    
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


    public function __construct($body) {
    		parent::__construct($body);
    		

        $body = null != $body ? json_decode($body, true) : null;

        if(!empty($body["error"])){

            $this->hasError = true;
            $this->errorMessage = $body["error_description"];
        }

        $this->accessToken = $body["access_token"];
        $this->instanceUrl = $body["instance_url"];  

        
				/*

            if(isset($body["error"]) || isset($body["error_description"])){
                $this->error = array(
                    "HttpStatusCode"						=> $this->getStatusCode(),
                    "HttpStatusMessage"					=> self::getErrorMsg($this->getStatusCode()),
                    "Salesforce_Error"					=> $body["error"],
                    "Salesforce_Error_message"	=> $body["error_description"]
                );
            } else if($body["errorCode"] == "Session expired or invalid") {
                $this->access_token_invalid = true;
            } else {
                $this->error = array(
                    "HttpStatusCode" 						=> $this->getStatusCode(),
                    "HttpStatusMessage"					=> $this->getErrorMsg($this->getStatusCode())
                );
            }
				*/
        
    }





		
    public static function resolveFromSession() {
    
        if(empty($_SESSION["salesforce_instance_url"])) {
            throw new Exception("Instance URL is not set!");
        }
        
        if(empty($_SESSION["salesforce_access_token"])) {
            throw new Exception("Access token is not set!");
        }
    }

    // Commented out s.s. 1/9/21 error cannnot call this on a non object.//
    private static function getErrorMsg($statusCode) {
			return self::$errorCodes[$statusCode];
    }
    
    
    
    public function getAccessToken() {
        return $this->accessToken;
    }
    
    
    public function getInstanceUrl() {
        return $this->instanceUrl;
    }
    
    
    public function getError() {
        return $this->error;
    }
}