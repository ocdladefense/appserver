<?php




namespace Salesforce;



use Http\HttpResponse;

class RestApiResponse extends HttpResponse {

    const DEFAULT_DECODING_SCHEME = "associative_array";
    const OBJECT_DECODING_SCHEME = "object";
    const JSON_DECODING_SCHEME = "json";
    const SESSION_ACCESS_TOKEN_EXPIRED_ERROR_CODE = "INVALID_SESSION_ID";

    private $errorMessage;

    private $errorCode;

    private $errordFields;

    private $hasError;

    private $sObjects;

    private $config;

    
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

        $body = null != $body ? json_decode($body, true) : null;

        parent::__construct($body);



        if(!empty($this->getBody()["errorCode"]) || !empty($this->getBody()["error"])){


            $this->hasError = true;
            $this->error = $body["error"];
            $this->errorMessage = $body["error_description"];
        }

        if($body != null){

            $this->accessToken = $body["access_token"];
            $this->instanceUrl = $body["instance_url"]; 
        }
    }

    // get body, getarray, getobject from the body;  returns default prefernce of type.

    // default decoding scheme = associative  php_associative_array.

    public function getBody($scheme = null){

        switch($scheme) {

            case self::JSON_DECODING_SCHEME:
                return json_encode($this->body);
                break;
            case self::OBJECT_DECODING_SCHEME:
                return json_decode(json_encode($this->body));
                break;
            default:
                return $this->body;
        }
    }

    public function getRecords(){

        if($this->isSuccess() && $this->body["records"] != null){

            return $this->body["records"];
        }
    }

    public function getRecord($index = null){

        return $index == null ? $this->body["records"][0] : $this->body["records"][$index];
    }


    public function getRecordCount(){

        return count($this->getRecords());
    }
    public function getConfig(){

        return $this->config != null ? $this->config->getName() : null;
    }

    public function setConfig($config){

        $this->config = $config;
    }

    public function getRecords(){

        if($this->isSuccess() && $this->body["records"] != null){

            return $this->body["records"];
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

            $this->errors = RestApiErrorCollection::fromArray($this->getBody());
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

    public static function fromArray($errorObjs){


        $collection = new RestApiErrorCollection();

        foreach($errorObjs as $obj){

            $collection->errorObjects[] = new RestApiError($obj["message"], $obj["errorCode"]);
        }

        return $collection;
    }

    public function getFirst(){

        return $this->errorObjects[0];
    }

}