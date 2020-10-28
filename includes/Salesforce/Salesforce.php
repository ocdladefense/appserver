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

    public function __construct($oauth_config)
    {
        $this->oauth_config = $oauth_config;
    }

    public function checkConfig(){
        $oauth_config = $this->oauth_config;
        if(is_null($oauth_config) || !is_array($oauth_config)){
            throw new SalesforceAuthException("Invalid Auth config");
        }else if(empty($oauth_config)){
            throw new SalesforceAuthException("Empty Auth Config");
        }else if(sizeof($oauth_config) <7){
            throw new SalesforceAuthException("Invalid Size Config");
        }
        $this->checkConfigPairs();
    }

    public function checkConfigValues(){
        $oauth_config = $this->oauth_config;
        //checking username for @ and .
        if(strpos($oauth_config["username"],"@") === false || strpos($oauth_config["username"],".") === false){
            throw new SalesforceAuthException("Invalid OauthConfig Usernane: ".$oauth_config["username"]);
        }
        //checking oauth url for .salesforce.com/services/oauth2/token
        if(strpos(strtolower($oauth_config["oauth_url"]),".salesforce.com/services/oauth2/token")=== false){
            throw new SalesforceAuthException("Invalid OauthConfig Login Url: ".$oauth_config["oauth_url"]);
        }
        //checking for a periof in the clientId
        // if(strpos($oauth_config["client_id"],".")=== false){
        //     throw new SalesforceAuthException("Invalid OauthConfig Client Id: ".$oauth_config["client_id"]);
        // }
    }

    public function CheckConfigPairs(){
        $oauth_config = $this->oauth_config;
        $oauth_params = array("oauth_url","client_id","client_secret","username","password","security_token","redirect_uri");

        foreach ($oauth_config as $key => $value) {
            if(strtolower($key) === 0 && strtolower($value) !== "redirect_uri"){
                throw new SalesforceAuthException("Invalid OauthConfig Key pair: \"".$value."\"");
            }
            if(!in_array(strtolower($key),$oauth_params)){
                throw new SalesforceAuthException("Invalid OauthConfig Parameter: ".$key);
            }
            if((empty($value) || is_null($value)) && strtolower($key) !== "redirect_uri"){
                throw new SalesforceAuthException("Empty/null value in OauthConfig Key Name: ".$key);
            }
        }
        $this->checkConfigValues();
    }
    
    public function authorizeToSalesforce() {
        
        $oauth_config = $this->oauth_config;

        //testing
        //$_SESSION["salesforce_access_token"] = "123";
        //$_SESSION["salesforce_access_token"] =null;
        //

        // If we already have an access_token, so no need to reauthorize; return TRUE.
        //if(isset($_SESSION["salesforce_access_token"])) return true;
        
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
        $authResult = new SalesforceAuthResult($resp);

        if($authResult->isSuccess()) {
            $_SESSION["salesforce_instance_url"] = $authResult->getInstanceUrl();
            $_SESSION["salesforce_access_token"]= $authResult->getAccessToken();
            //return true;
        }
        return $authResult;
    }
    public function createQueryFromSession($soql){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->createQuery($soql,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }
    public function createRecordFromSession($sObjectName,$sObjectFields){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->createRecord($sObjectName,$sObjectFields,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }
    public function createRecord($sObjectName,$sObjectFields,$instance_url = null,$access_token = null){
        //curl https://yourInstance.salesforce.com/services/data/v20.0/sobjects/Account/ 
        //-H "Authorization: Bearer token -H "Content-Type: application/json" -d "@newaccount.json"

        $endpoint = "/services/data/v49.0/sobjects/".$sObjectName;
        // $endpoint = "/v49.0/query?q=";
        $resource_url = $instance_url . $endpoint;

        //print "<p>Will execute query at: ".$resource_url."</p>";
        $req = new HttpRequest($resource_url);
        $token = new HttpHeader("Authorization", "Bearer " . $access_token);
        $content_type = new HttpHeader("Content-Type","application/json");
        $req->addHeader($token);
        $req->addHeader($content_type);
        $req->setBody($sObjectFields);
        $req->setPost();

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
        //var_dump($resp->getBody());
        $body = json_decode($resp->getBody(),true);
        //var_dump($body);
        return $body;
    }

    public function createQuery($soql,$instance_url = null,$access_token = null){
        //$body = "";
        //for($tries = 0; $tries<5;$tries++){
        /*if (!$this->authorizeToSalesforce()) {
            throw new SalesforceAuthException("Not Authorized");
        }*/
        $endpoint = "/services/data/v49.0/query/?q=";
        // $endpoint = "/v49.0/query?q=";
        $resource_url = $instance_url . $endpoint . urlencode($soql);

        //print "<p>Will execute query at: ".$resource_url."</p>";
        $req = new HttpRequest($resource_url);
        $token = new HttpHeader("Authorization", "Bearer " . $access_token);
        $req->addHeader($token);

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

        // Get the log for this
        //var_dump($req);
        $resp = $http->send($req);
        //var_dump($resp->getBody());
        $body = json_decode($resp->getBody(),true);
        //var_dump($body);
        return $body;
    }
    public function updateRecordFromSession($sObjectName,$sObjectId,$sObjectFields){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->updateRecord($sObjectName,$sObjectId,$sObjectFields,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }
    public function updateRecord($sObjectName,$sObjectId,$sObjectFields,$instance_url = null,$access_token = null){
        $endpoint = "/services/sobjects/".$sObjectName."/".$sObjectId;
        $resource_url = $instance_url . $endpoint;

        //print "<p>Will execute query at: ".$resource_url."</p>";
        $req = new HttpRequest($resource_url);
        $token = new HttpHeader("Authorization", "Bearer " . $access_token);
        $req->addHeader($token);
        $req->setPatch();
        $req->setBody($sObjectFields);

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

        // Get the log for this
        //var_dump($req);
        $resp = $http->send($req);
        //var_dump($resp->getBody());
        $body = json_decode($resp->getBody(),true);
        //var_dump($body);
        return $body;
    }
    public function deleteRecordFromSession($sObjectName,$sObjectId){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->deleteRecord($sObjectName,$sObjectId,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }
    public function deleteRecord($sObjectName,$sObjectId,$instance_url = null,$access_token = null){
        $endpoint = "/services/sobjects/".$sObjectName."/".$sObjectId;
        $resource_url = $instance_url . $endpoint;

        //print "<p>Will execute query at: ".$resource_url."</p>";
        $req = new HttpRequest($resource_url);
        $token = new HttpHeader("Authorization", "Bearer " . $access_token);
        $req->addHeader($token);
        $req->setDelete();

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

        // Get the log for this
        //var_dump($req);
        $resp = $http->send($req);
        //var_dump($resp->getBody());
        $body = json_decode($resp->getBody(),true);
        //var_dump($body);
        return $body;
    }
    //}

}