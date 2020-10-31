<?php

use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

class Salesforce {

    private $oauth_config = array(
        "oauth_url" => "", 
        "client_id" => "",
        "client_secret" => "",
        "username" => "",
        "password" => "",
        "security_token" => ""
    );

    public function __construct($config)
    {
        $this->oauth_config = $config;
    }

    public function queryChecker($soql){
        
    }
    
    public function authorizeToSalesforce() {
        
        $oauth_config = $this->oauth_config;

        // If we already have an access_token, so no need to reauthorize; return TRUE.
        if(isset($_SESSION["salesforce_access_token"])) return true;

        $req = new HttpRequest($oauth_config["oauth_url"]);
        $req->setPost();

        $contentTypeHeader = new HttpHeader("Content-Type", "application/x-www-form-urlencoded");
        $req->addHeader($contentTypeHeader);
        $body = "grant_type=password&client_id=".
                $oauth_config["client_id"]."&client_secret=".$oauth_config["client_secret"]."&username="
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
        $body =json_decode($resp->getBody(), true);
        var_dump($body);

        if(isset($body["instance_url"]) && isset($body["access_token"])) {
            $_SESSION["salesforce_instance_url"] = $body["instance_url"];
            $_SESSION["salesforce_access_token"]= $body["access_token"];
            return true;
        }
        
        else if(isset($body["error"])){
            //if($body["error"] == ""){
                
            //}
            throw new Exception($body["error_description"]);
        }
    }
    
    public function CreateQuery($soql){
        if(!$this->authorizeToSalesforce()){
            throw new Exception("no authorized");
        }
        
        if(!QueryStringParser::Validate($soql))
        {
            throw new QueryException("Invalid SOQL statement");
        }
        
            $endpoint = "/services/data/v49.0/query/?q=";
            // $endpoint = "/v49.0/query?q=";
            $resource_url = $_SESSION["salesforce_instance_url"].$endpoint.urlencode($soql);
            
            //print "<p>Will execute query at: ".$resource_url."</p>";

            $req = new HttpRequest($resource_url);
            $token = new HttpHeader("Authorization","Bearer ".$_SESSION["salesforce_access_token"]);
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
            var_dump($req);
            $resp = $http->send($req);
            
            // var_dump($resp);


            return $resp->getBody();
    }

}