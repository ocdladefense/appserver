<?php




namespace Salesforce;



use Http\HttpResponse;

class RestApiResponse extends HttpResponse {

    private $errorMessage;

    private $errorCode;

    private $errordFields;

    private $hasError;

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
    private $SObject;

    public function getSObjects(){
        return $this->sObjects;
    }

    public function __construct($body) {

        parent::__construct($body);

        $body = null != $body ? json_decode($body, true) : null;

        if(!empty($body["errorCode"]) || !empty($body["error"])){

            $this->hasError = true;
            $this->error = $body["error"];
            $this->errorMessage = $body["error_description"];
        }

        if($body != null){

            $this->accessToken = $body["access_token"];
            $this->instanceUrl = $body["instance_url"]; 
        }
    }

    public function other(){

                //if the request is successful we can opt use the X-Request-Endpoint header to create the sobject class(s) or an array of them
        //new HttpHeader("X-Request-Endpoint",$endpoint)
        if($this->isSuccess() && false){
            try{
                //sep function
                $requestUrl = $this->getHeader("X-Request-Endpoint")->getValue();
                //determine the correct sobject to instanciate
                $reqClass = new RestApiRequest ();
                $reqEndpoint = explode("salesforce.com", $requestUrl)[1];//truncating the instance url and getting the endpoint
                $sobjectName = $reqClass->getEndpoint($reqEndpoint,true);
                
                if($sobjectName == "sObject"){
                    
                }
                $model = new $sObjectName($body);
                $this->sObjects = array($model);

            }catch(Exception $e){
                $SObject = null;
            }

        }

    }


    public function foobar(){

        // if(isset($body["error"]) || isset($body["error_description"])){
        //     $this->error = array(
        //         "HttpStatusCode"						=> $this->getStatusCode(),
        //         "HttpStatusMessage"					=> self::getErrorMsg($this->getStatusCode()),
        //         "Salesforce_Error"					=> $body["error"],
        //         "Salesforce_Error_message"	=> $body["error_description"]
        //     );
        // } else if((isset($body["errorCode"]) && $body["errorCode"] == "INVALID_SESSION_ID") || 
        //             (isset($body["message"]) && $body["message"] == "Session expired or invalid")) {
        //                 //        {"message": "Session expired or invalid","errorCode": "INVALID_SESSION_ID"}
        //     $this->access_token_invalid = true;
        // } else if(isset($body["errorCode"])) {

        //     $this->error = array(
        //         "message" => $body[""]
        //     )

        // } else {
        //     $this->error = array(
        //         "HttpStatusCode" 						=> $this->getStatusCode(),
        //         "HttpStatusMessage"					=> $this->getErrorMsg($this->getStatusCode())
        //     );
        // }
    }

    public function getSObject(){
        return $SObject;
    }

    
    private static function isOAuthResponse($body = null) {
    		if(null == $body) return false;
    		
        return isset($body["access_token"]) && isset($body["instance_url"]);
    }



    // Commented out s.s. 1/9/21 error cannnot call this on a non object.//
    public function getErrorMessage() {

        if(!$this->isSuccess()) {

            $this->errors = RestApiErrorCollection::fromJson($this->getBody());
            return $this->errors->getFirst()->getMessage();   
        }

        return null;
    }
    
    
}

class RestApiError {

    private $message;
    private $errorCode;


    public function __construct($message, $errorCode){

        $this->message = $message;
        $this->errorCode = $errorCode;
    }

    public function getMessage(){

        return $this->message;
    }
}

class RestApiErrorCollection {

    public $errorObjects;

    public static function fromJson($json){

        $collection = new RestApiErrorCollection();

        $errorObjs = json_decode($json);

        foreach($errorObjs as $obj){

            $collection->errorObjects[] = new RestApiError($obj->message, $obj->errorCode);
        }

        return $collection;
    }

    public function getFirst(){

        return $this->errorObjects[0];
    }

}