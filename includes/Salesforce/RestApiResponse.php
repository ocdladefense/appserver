<?php




namespace Salesforce;



use Http\HttpResponse;

class RestApiResponse extends HttpResponse {

    private $error;

    private $sObjects;
    
    private static $errorCodes = array(
        0 => "Invalid URl",
        400 => "Bad Oauth_url/Request",
        401 => "Unauthorized - Auth required",
        403 => "Forbidden - Server Request Unfulfilled",
        404 => "Oauth_url Not Found",
        405 => "Method type not allowed",
        406 => "Request content not acceptable"
    );


    public function getSObjects(){
        return $this->sObjects;
    }

    public function __construct($body) {
    		parent::__construct($body);
        
        
        $body = null != $this->getBody() ? json_decode($this->getBody(), true) : null;
        
        //if the request is successful we can opt use the X-Request-Endpoint header to create the sobject class(s) or an array of them
        //new HttpHeader("X-Request-Endpoint",$endpoint)
        if($this->isSuccess() && false){
            //sep function
            $header = $this->getHeader("X-Request-Endpoint");
            //determine the correct sobject to instanciate
            $sObjectName = "Document";
            $model = new $sObjectName($body);
            $this->sObjects = array($model);
        }

        if(!$this->isSuccess()) {

            if(isset($body["error"]) || isset($body["error_description"])){
                $this->error = array(
                    "HttpStatusCode"						=> $this->getStatusCode(),
                    "HttpStatusMessage"					=> self::getErrorMsg($this->getStatusCode()),
                    "Salesforce_Error"					=> $body["error"],
                    "Salesforce_Error_message"	=> $body["error_description"]
                );
            } else if((isset($body["errorCode"]) && $body["errorCode"] == "INVALID_SESSION_ID") || 
                        (isset($body["message"]) && $body["message"] == "Session expired or invalid")) {
                            //        {"message": "Session expired or invalid","errorCode": "INVALID_SESSION_ID"}
                $this->access_token_invalid = true;
            } else {
                $this->error = array(
                    "HttpStatusCode" 						=> $this->getStatusCode(),
                    "HttpStatusMessage"					=> $this->getErrorMsg($this->getStatusCode())
                );
            }
        }
        
    }



    

    
    private static function isOAuthResponse($body = null) {
    		if(null == $body) return false;
    		
        return isset($body["access_token"]) && isset($body["instance_url"]);
    }



    // Commented out s.s. 1/9/21 error cannnot call this on a non object.//
    private static function getErrorMsg($statusCode) {
			return self::$errorCodes[$statusCode];
    }
    
    
    
    
    public function getError() {
        return $this->error;
    }
    
    
}