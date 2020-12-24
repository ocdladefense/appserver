<?php

use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use Http\HttpResponse;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;





class Salesforce {


    private $oauth_config = array();
    private $reqBody = array();

    private const MAX_LOGIN_ATTEMPTS = 3;



    public function __construct($oauth_config = array())
    {
        $this->oauth_config = $oauth_config;
    }



    public function getReqBody(){
        return $this->reqBody;
    }

    public function checkConfig() {
        $config = $this->oauth_config;
        
        // Removed redirect_uri, security_token cuz it can be empty.
        $check = array("oauth_url","client_id","client_secret","username","password");
        
        
        // @TODO should it be 5 or 7; should be able to omit keys that aren't required
        // and still have a valid OAuth config.
        if(empty($config) || !is_array($config) || sizeof($config) < 7) {
            throw new SalesforceAuthException("Invalid Auth config");
				}


        foreach($check as $key) {
        		$value = $config[$key];
            
            // None of the configs should be missing!
            if(!array_key_exists($key,$config)) {
                throw new SalesforceAuthException("Invalid OAuthConfig Parameter {$key}.");
            }
            
            // Nothing important should be empty!
            if(empty($value)) {
                throw new SalesforceAuthException("Empty/null value in OAuthConfig Key Name: {$key}.");
            }
        }
        
        
        if(!self::isValidSalesforceUsername($config["username"])) {
					throw new SalesforceAuthException("Invalid OauthConfig Username: ".$config["username"]);
        }
        
        if(!self::isValidOAuthTokenUrl($config["oauth_url"])) {
					throw new SalesforceAuthException("Invalid OauthConfig Login Url: ".$config["oauth_url"]);
				}
    }
	private static function isValidSalesforceUsername($username) {
        //checking username for @ and .
        return strpos($username,"@") !== false && strpos($username,".") !== false;
	}
		
	private static function isValidOAuthTokenUrl($url) {
	
		//checking oauth url for .salesforce.com/services/oauth2/token
		return strpos(strtolower($url),".salesforce.com/services/oauth2/token") !== false;
    }
    
    public function sendRequestFromSession($endpoint,$method = "GET",$body = null,$contentType = "application/json"){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->sendRequest($endpoint,$method,$body,$contentType);
    }

    private function isOauthRequest($endpoint){
        return strpos($endpoint,"oauth");
    }
    public function sendRequest($endpoint,$method = "GET",$body = null,$contentType = "application/json"){

        if (!$this->isOauthRequest($endpoint)){
            $endpoint = $_SESSION["salesforce_instance_url"] . $endpoint;
            $token = new HttpHeader("Authorization", "Bearer " . $_SESSION["salesforce_access_token"]);
        }

        $req = new HttpRequest($endpoint);
        if($token != null){
            $req->addHeader($token);
        }

        $content_type = new HttpHeader("Content-Type",$contentType);
        $req->addHeader($content_type);
        if($body != null)
        {
            if($contentType == "application/json"){
                $body = json_encode($body);
            }
            else if($contentType == "application/x-www-form-urlencoded"){
                $body = http_build_query($body);
            }
            $req->setBody($body);
        }
        $req->setMethod($method);
        $config = array(
            // "cainfo" => null,
            // "verbose" => false,
            // "stderr" => null,
            // "encoding" => '',
            "returntransfer" => true,
            // "httpheader" => null,
            "useragent" => "Mozilla/5.0",
            // "header" => 1,
            // "header_out" => true,
            "followlocation" => true,
            "ssl_verifyhost" => false,
            "ssl_verifypeer" => false
        );

        $http = new Http($config);
        $response = $http->send($req);
        $apiresult = new RestApiResult($response);

        //trying to authenticate again if token exp or invalid
        if(!$response->isSuccess() && !$this->isOauthRequest($endpoint)){
            
            if($apiresult->isTokenExpired() || $apiresult->isTokenInvalid()){
                $this->authorizeToSalesforce();
                $this->sendRequest($endpoint,$method,$body,$contentType);
            }

        }
         return $response;
    }
    
    public function authorizeToSalesforce() {

        unset($_SESSION["salesforce_access_token"]);
        unset($_SESSION["salesforce_instance_url"]);
        unset($_SESSION["salesforce_token_expire_date"]);
        $_SESSION["login_attempts"]++;
        if($_SESSION["login_attempts"] > $this->MAX_LOGIN_ATTEMPTS){
            throw new Exception ("Too many login attempts");
        }
        
        //check for token in session and timeout value
        // if(isset($_SESSION["salesforce_access_token"]) && isset($_SESSION["salesforce_instance_url"]) && ($_SESSION["salesforce_token_expire_date"] < new DateTime("now"))){
        //     $_SESSION["sf_oauth_authenticated"] = false;
        //     unset($_SESSION["salesforce_access_token"]);
        //     unset($_SESSION["salesforce_instance_url"]);
        //     unset($_SESSION["salesforce_token_expire_date"]);
        // }else if (isset($_SESSION["salesforce_access_token"]) && isset($_SESSION["salesforce_instance_url"]) && ($_SESSION["salesforce_token_expire_date"] >= new DateTime("now"))){
        //     $_SESSION["sf_oauth_authenticated"] = false;
        //     $resp = new HttpResponse("self oath auth");
        //     $resp->setStatusCode(200);
        //     return (new SalesforceAuthResult($resp));
        // }        

        $oauth_config = $this->oauth_config;
        $this->checkConfig();
        $body = array(
            "grant_type" => "password",
            "client_id" => $oauth_config["client_id"],
            "client_secret"=> $oauth_config["client_secret"],
            "username"=>$oauth_config["username"],
            "password"=>$oauth_config["password"] . $oauth_config["security_token"]
        );
        $respApiResult = $this->sendRequest($oauth_config["oauth_url"],"POST",$body,"application/x-www-form-urlencoded");

        if($respApiResult->isSuccess()) {
            $_SESSION["salesforce_instance_url"] = $respApiResult->getInstanceUrl();
            $_SESSION["salesforce_access_token"]= $respApiResult->getAccessToken();
            $_SESSION["login_attempts"] = 0;
        }else{
            throw new SalesforceAuthException("Not Authorized");
        }
        return $respApiResult;
    }
    
    public function createRecordsFromSession($sObjectName,$sObjectFields){
        $this->authorizeToSalesforce();
        return $this->createRecords($sObjectName,$sObjectFields,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }


  
    public function createRecords($sObjectName,$records,$instance_url = null,$access_token = null){
        $pluralEndpoint = "/services/data/v49.0/composite/tree/".$sObjectName;
        $singularEndpoint = "/services/data/v49.0/sobjects/".$sObjectName;
        $plural = is_array($records) && isset($records[0]);
        $endpoint = $plural ? $pluralEndpoint : $singularEndpoint;
        $fn = function ($record,$index) use($sObjectName){
            $record["attributes"] = array("type"=>$sObjectName,"referenceId"=>"ref".++$index);
            return $record;
        };
        $records = $plural ? array_map($fn,$records,array_keys($records)):$records;
        $records = $plural ? array("records" => $records ) : $records;
        $resp = $this->sendRequest($endpoint,"POST",$records);
        if (strpos($resp->getBody(),"hasErrors:true")){
            throw new Exception($resp->getBody());
        }
        $body = json_decode($resp->getBody(),true);
        return $body;
    }
    
    public function getAttachment($id) {
			$endpoint = "/services/data/v49.0/sobjects/Attachment/{$id}/body";
			$resp = $this->sendRequest($endpoint);
			
			return $resp;
    }

    public function createQueryFromSession($soql){
        $this->authorizeToSalesforce();
        return $this->createQuery($soql,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }

    public function createQuery($soql,$instance_url = null,$access_token = null){
        $endpoint = "/services/data/v49.0/query/?q=";

        $resp = $this->sendRequest($endpoint . urlencode($soql));
        $body = json_decode($resp->getBody(),true);
        return $body;
    }

    public function queryIdsFromSession($sObjectName,$ids,$fields){
        $this->authorizeToSalesforce();
        return $this->queryIds($sObjectName,$ids,$fields,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }

    public function queryIds($sObjectName,$ids,$fields,$instance_url = null,$access_token = null){
        $endpoint = "/services/data/v50.0/composite/sobjects/".$sObjectName."?ids=";
        foreach($ids as $id){
            $endpoint = $endpoint.$id.",";
        }
        $endpoint = rtrim($endpoint, ',');//deleting last comma
        $endpoint = $endpoint."&fields=";
        foreach($fields as $field){

            $endpoint = $endpoint.$field.",";
        }
        $endpoint = rtrim($endpoint, ',');//deleting last comma
        $resp = $this->sendRequest($endpoint);
        $body = json_decode($resp->getBody(),true);
        return $body;
    }
    public function updateRecordFromSession($records, $sObject = null){
        return $this->updateRecordsFromSession($records, $sObject = null);
    }

    public function updateRecordsFromSession($records,$sObject = null){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->updateRecords($records,$sObject,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }
    
    public function updateRecords($records,$sObject = null,$instance_url = null,$access_token = null){
        
        $singularEndpoint = "/services/sobjects/";
        $pluralEndpoint = "/services/data/v49.0/composite/sobjects/";
        $plural = is_array($records) && isset($records[0]);
        
        if($plural){
            foreach($records as $record){
                if(!isset($record["attributes"])){
                    throw new Exception ("Attribute field not set");
                }
            }
        }
        $endpoint = $plural ? $pluralEndpoint : $singularEndpoint.$sObject."/".$records[0]["Id"];

        $fn = function ($record,$index) use($sObject){
            $record["attributes"] = array("type"=>$sObject);
            return $record;
        };
        $records = $plural && $sObject!= null ? array_map($fn,$records,array_keys($records)):$records;
        $records = $plural ? array("records" => $records ) : $records;
        if($plural){
            $records["allOrNone"] = false;
        }

        //better way to do the trailing front slash
        $resp = $this->sendRequest($endpoint."/","PATCH",$records);
        $body = json_decode($resp->getBody(),true);
        return $body;
    }

    public function deleteRecordFromSession($sObject,$sObjectIds){
        return $this->deleteRecordsFromSession($sObject,$sObjectIds);
    }

    public function deleteRecordsFromSession($sObject,$sObjectIds){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->deleteRecords($sObject,$sObjectIds,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }
    public function deleteRecords($sObject,$sObjectIds,$instance_url = null,$access_token = null){
        $pluralEndpoint = function () use($sObjectIds){
            $endpoint = "/services/data/v49.0/composite/sobjects?ids=";
            foreach ($sObjectIds as $value)
                $endpoint = $endpoint.$value.",";
            return $endpoint."&allOrNone=false";
        };
        //$singularEndpoint = "/services/data/v49.0/sobjects/".$sObjectName."/".$sObjectIds;
        $endpoint = is_array($sObjectIds)? $pluralEndpoint."/" : "/services/data/v49.0/sobjects/".$sObject."/".$sObjectIds."/";
        $resp = $this->sendRequest($endpoint,"DELETE");
        $body = json_decode($resp->getBody(),true);
        //var_dump($resp);
        if(is_array($sObjectIds) && $resp->getStatusCode() != 200){
            throw new Exception("Status Code: ".$resp->getStatusCode()." Error deleating the record(s): ".$resp->getBody());
            
        }
        else if(!is_array($sObjectIds) && $resp->getStatusCode() != 204){
            throw new Exception("Status Code: ".$resp->getStatusCode()." Error deleating the record(s): ".$resp->getBody());
            
        }
        return true;
    }

    public function addToBatch($fields,$metod = null){
        $req = array();//final request to add to batch

        if(empty($fields) && (!is_array($fields) || !is_string($fields))){
            throw new Exception("Invalid request");
        }
        if(!is_array($fields) && strpos($fields,"SELECT")){
            $req["url"] = "v50.0/query/?q=".urlencode($fields);
            $req["method"] = "GET";
            array_push($req);
            return $this->reqBody;
        }



        // if($req["method"] == "PATCH" &&  strpos($req["url"],"v50.0/sobjects/")){//if its calling the update
        //     if(empty($req["richInput"]) || !is_array($req["richInput"])){
        //         throw new Exception("Invalid richInput of an UPDATE/PATCH");
        //     }
        //     if (array_keys($req["richInput"]) !== range(0, count($req["richInput"]) - 1)){
        //         throw new Exception("Invalid richInput BODY of an UPDATE/PATCH");
        //     }
        //     if(count($req) != 3){
        //         throw new Exception("Invalid UPDATE/PATCH, malformed elements");
        //     }

        //     if($this->reqBody == null){
        //         $this->reqBody = array(
        //             "batchRequests" => array($req)
        //         );
        //     }
        //     array_push($this->reqBody["batchRequests"],$req);
        // }
        // if($req["method"] == "GET" &&  strpos($req["url"],"v50.0/sobjects/")){//if its calling the update
        //     if(strpos($req["url"],"?fields=")){
        //         throw new Exception("Invalid url of QUERY/GET or not encoded ");
        //     }
        //     if(count($req) != 2){
        //         throw new Exception("Invalid QUERY/GET, malformed elements");
        //     }
        //     if($this->reqBody == null){
        //         $this->reqBody = array(
        //             "batchRequests" => array($req)
        //         );
        //     }
        //     array_push($this->reqBody["batchRequests"],$req);
        // }
        // if($req["method"] == "POST" &&  strpos($req["url"],"v50.0/sobjects/")){//if its calling the update
        //     if(empty($req["richInput"]) || !is_array($req["richInput"])){
        //         throw new Exception("Invalid richInput of an CREATE/POST");
        //     }
        //     if (array_keys($req["richInput"]) !== range(0, count($req["richInput"]) - 1)){
        //         throw new Exception("Invalid richInput BODY of an CREATE/POST");
        //     }
        //     if(count($req) != 3){
        //         throw new Exception("Invalid CREATE/POST, malformed elements");
        //     }

        //     if($this->reqBody == null){
        //         $this->reqBody = array(
        //             "batchRequests" => array($req)
        //         );
        //     }
        //     array_push($this->reqBody["batchRequests"],$req);
        // }

        // if($req["method"] == "DELETE" &&  strpos($req["url"],"v50.0/sobjects/")){//if its calling the update
        //     if(empty($req["richInput"]) || !is_array($req["richInput"])){
        //         throw new Exception("Invalid richInput of an CREATE/POST");
        //     }
        //     if (array_keys($req["richInput"]) !== range(0, count($req["richInput"]) - 1)){
        //         throw new Exception("Invalid richInput BODY of an CREATE/POST");
        //     }
        //     if(count($req) != 2){
        //         throw new Exception("Invalid CREATE/POST, malformed elements");
        //     }

        //     if($this->reqBody == null){
        //         $this->reqBody = array(
        //             "batchRequests" => array($req)
        //         );
        //     }
        //     array_push($this->reqBody["batchRequests"],$req);
        // }


    }

    public function sendBatchFromSession($reqBody = null){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->deleteRecords($reqBody,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }

    public function sendBatch($reqBody = null,$instance_url = null,$access_token = null){
        if(empty($reqBody)){
            $reqBody = $this->reqBody;
        }
        if (!is_array($reqBody)){
            throw new Exception("request body is not an array");
        }
        

        $endpoint = "/services/data/v50.0/composite/batch/";
        $resp = $this->sendRequest($endpoint,"POST",array("batchRequests" => $reqBody));
        if(strpos($resp->getBody(),"\"hasErrors\" : true")){
            throw new Exception($resp->getBody());
        }
        $body = json_decode($resp->getBody(),true);
    }



}