<?php

use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

class Salesforce {

    /*
    $oauth_config = array(
        "oauth_url" => SALESFORCE_LOGIN_URL,
        "client_id" => SALESFORCE_CLIENT_ID,
        "client_secret" => SALESFORCE_CLIENT_SECRET,
        "username" => SALESFORCE_USERNAME,
        "password" => SALESFORCE_PASSWORD,
        "security_token" => SALESFORCE_SECURITY_TOKEN,
        "redirect_uri" => SALESFORCE_REDIRECT_URI
    );
    */
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

    public function sendRequest($endpoint,$body = null,$method = "POST"){
        $endpoint = $_SESSION["salesforce_instance_url"] . $endpoint."/";
        $req = new HttpRequest($endpoint);
        $token = new HttpHeader("Authorization", "Bearer " . $_SESSION["salesforce_access_token"]);
        $content_type = new HttpHeader("Content-Type","application/json");
        $req->addHeader($token);
        $req->addHeader($content_type);
        if($body != null)
        {
            $req->setBody(json_encode($body));
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
        $resp = $http->send($req);
        return $resp;
    }
    
    public function authorizeToSalesforce() {
        
        $oauth_config = $this->oauth_config;
        $this->checkConfig();
        $req = new HttpRequest($oauth_config["oauth_url"]);
        $req->setPost();

        $contentTypeHeader = new HttpHeader("Content-Type", "application/x-www-form-urlencoded");
        $req->addHeader($contentTypeHeader);
        $body = "grant_type=password&client_id=".$oauth_config["client_id"]."&client_secret=".$oauth_config["client_secret"]."&username="
                .$oauth_config["username"]."&password=".$oauth_config["password"] . $oauth_config["security_token"];
        $req->setBody($body);
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
        $resp = $http->send($req);
        //$body =json_decode($resp->getBody(), true);
        //different class
        $resp = 
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


  
    public function createRecords($sObjectName,$records,$instance_url = null,$access_token = null){
        $pluralEndpoint = "/services/data/v49.0/composite/tree/".$sObjectName;
        $singularEndpoint = "/services/data/v49.0/sobjects/".$sObjectName;
        $plural = is_array($records) && isset($records[0]);
        $endpoint = $plural ? $pluralEndpoint : $singularEndpoint;
        $fn = function ($record,$index) use($sObjectName){
            $record["attributes"] = array("type"=>$sObjectName[$index],"referenceId"=>"ref".++$index);
            return $record;
        };
        $records = $plural ? array_map($fn,$records,array_keys($records)):$records;
        $records = $plural ? array("records" => $records ) : $records;
        $resp = $this->sendRequest($endpoint,$records);
        $body = json_decode($resp->getBody(),true);
        if ($body["hasErrors"] == true){
            throw new Exception("Error inserting request");
        }
        return $body;
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
        $resource_url = $instance_url . $endpoint . urlencode($soql);
        $resp = $this->sendRequest($endpoint . urlencode($soql));
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

    public function testUpdateRecords(){
        $sObjects = array(
            "Contact","Contact","Account","Account"
        );
        $records = array(
            array (
                "Id" => "3424",
                "LastName" => "NOTBOB"
            ),array(
                "Id" => "3423",
                "LastName" => "NOTBOB"
            ),array(
                "Id" => "3422",
                "CompanyName" => "NOTBOB"
            ),array(
                "Id" => "3421",
                "CompanyName" => "NOTBOB"
            )
        );

        $accounts = array(
            array(
                "attributes" => array("type"=>"Account"),
                "Id" => "3422",
                "CompanyName" => "NOTBOB"
            ),array(
                "attributes" => array("type"=>"Account"),
                "Id" => "3421",
                "CompanyName" => "NOTBOB"
            )
        );
        $this->updateRecordsFromSession($records,"Contact");
        $this->updateRecordsFromSession($accounts);
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

        $resource_url = $instance_url . $endpoint;
        $resp = $this->sendRequest($endpoint,$records,"PATCH");
        $body = json_decode($resp->getBody(),true);
        return $body;
    }

    public function deleteRecordFromSession($sObjectName,$sObjectIds){
        return $this->deleteRecordsFromSession($sObjectName,$sObjectIds);
    }

    public function deleteRecordsFromSession($sObjectIds,$sObject){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->deleteRecords($sObjectIds,$sObject,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }
    public function deleteRecords($sObjectIds,$sObject,$instance_url = null,$access_token = null){
        $pluralEndpoint = function () use($sObjectIds){
            $endpoint = "/services/data/v49.0/composite/sobjects?ids=";
            foreach ($sObjectIds as $value)
                $endpoint = $endpoint.$value.",";
            return $endpoint."&allOrNone=false";
        };
        //$singularEndpoint = "/services/data/v49.0/sobjects/".$sObjectName."/".$sObjectIds;
        $endpoint = is_array($sObjectIds)? $pluralEndpoint : "/services/data/v49.0/sobjects/".$sObject."/".$sObjectIds;
        $resp = $this->sendRequest($endpoint,null,"DELETE");
        $body = json_decode($resp->getBody(),true);
        //var_dump($resp);
        if(is_array($sObjectIds) && $resp->getStatusCode() != 200){
            throw new Exception("Status Code: ".$resp->getStatusCode()." Error deleating the record(s): ".$resp->getBody());
            
        }
        else if(!is_array($sObjectIds) && $resp->getStatusCode() != 204){
            throw new Exception("Status Code: ".$resp->getStatusCode()." Error deleating the record(s): ".$resp->getBody());
            
        }
        return $resp->getStatusCode() == 204 ?true:false;
    }



}