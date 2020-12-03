<?php

use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;





class Salesforce {


    private $oauth_config = array();

    public function __construct($oauth_config = array())
    {
        $this->oauth_config = $oauth_config;
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



    public function queryChecker($soql){
        
    }

    public function sendRequest($endpoint,$method = "GET",$body = null,$contentType = "application/json"){

        if (!strpos($endpoint,"oauth")){
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
        return $http->send($req);
    }
    
    public function authorizeToSalesforce() {
        
        $oauth_config = $this->oauth_config;
        $this->checkConfig();
        $body = array(
            "grant_type" => "password",
            "client_id" => $oauth_config["client_id"],
            "client_secret"=> $oauth_config["client_secret"],
            "username"=>$oauth_config["username"],
            "password"=>$oauth_config["password"] . $oauth_config["security_token"]
        );
        $resp = $this->sendRequest($oauth_config["oauth_url"],"POST",$body,"application/x-www-form-urlencoded");
        $authResult = new SalesforceAuthResult($resp);

        if($authResult->isSuccess()) {
            $_SESSION["salesforce_instance_url"] = $authResult->getInstanceUrl();
            $_SESSION["salesforce_access_token"]= $authResult->getAccessToken();
            //return true;
        }
        return $authResult;
    }

    public function createRecordFromSession($sObjectName,$sObjectFields){
        return $this->createRecordsFromSession($sObjectName,$sObjectFields);
    }
    
    public function createRecordsFromSession($sObjectName,$sObjectFields){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }

        return $this->createRecords($sObjectName,$sObjectFields,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }


  
    public function createRecords($sObjectName, $records, $instance_url = null, $access_token = null){
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
        $body = json_decode($resp->getBody(),true);
        if ($body["hasErrors"] == true){
            throw new Exception("Error inserting request");
        }
        return $body;
    }



    
    public function getAttachment($id) {
			$endpoint = "/services/data/v49.0/sobjects/Attachment/{$id}/body";
			$resp = $this->sendRequest($endpoint);
			
			return $resp;
    }
    



    public function createQueryFromSession($soql){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->createQuery($soql,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }

    public function createQuery($soql,$instance_url = null,$access_token = null){
        $endpoint = "/services/data/v49.0/query/?q=";

        $resp = $this->sendRequest($endpoint . urlencode($soql));
        $body = json_decode($resp->getBody(),true);
        return $body;
    }

    public function queryIdsFromSession($sObjectName,$ids,$fields){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
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
        $endpoint = is_array($sObjectIds)? $pluralEndpoint : "/services/data/v49.0/sobjects/".$sObject."/".$sObjectIds;
        $resp = $this->sendRequest($endpoint."/","DELETE");
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



}