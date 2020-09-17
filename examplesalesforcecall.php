<?php

use Http\HttpHeader;
use Http\HttpRequest;
use Http\HttpResponse;
use Http\Http;
require 'bootstrap.php';     

if ($_SESSION["salesforce_access_token"] == null)
{

    $req = new HttpRequest("https://login.salesforce.com/services/oauth2/token");
    $req->setPost();
    $client_id = "123.456";
    $secret = "321";
    $username = "foobar@foobar.com";
    $SecurityToken = "foobar";
    $password ="Password1234".$SecurityToken;
    $contentTypeHeader = new HttpHeader("Content-Type", "application/x-www-form-urlencoded");
    $req ->addHeader($contentTypeHeader);
    $body = "grant_type=password&client_id=".$client_id."&client_secret=".$secret."&username=".$username."&password=".$password;

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
    $response = $http->send($req);
    $body =json_decode($response->getBody(), true);
    var_dump($body);

    $_SESSION["salesforce_instance_url"] = $body["instance_url"];
    $_SESSION["salesforce_access_token"]= $body["access_token"];
}


var_dump($_SESSION["salesforce_instance_url"]);
var_dump($_SESSION["salesforce_access_token"]);
$query = "Select Id from Account";
$endpoint = "/v49.0/query/?q=";

$query_req = new HttpRequest($_SESSION["salesforce_instance_url"].$endpoint.$query);
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
$body =json_decode($response->getBody(), true);
var_dump($body);