<?php

use Http\HttpHeader;
use Http\HttpRequest;
use Http\HttpResponse;
require 'bootstrap.php';     

if ($_SESSION["salesforce_access_token"] == null)
{

    $req = new HttpRequest("https://login.salesforce.com/services/oauth2/token");
    $client_id = "3MVG9FxR3Tq3eZN_t4r27D3y_tnXtB5ee8ExXP.EdH9b9Njv2UQOWnTn3TW19c7Kd6OW6g5pyR_Ss1RlYSvnV";
    $secret = "43EF03AB5F74900355E4139D2880FFC488088136439C69892F62E4FD795FB84B";
    $username = "test-fdz8tpa3yigj@example.com";
    $SecurityToken = "BSKMdRJV8SFJXw9enLakWcTg";
    $password ="Password1234".$SecurityToken;
    $contentTypeHeader = new HttpHeader("Content-Type","application/x-www-form-urlencoded");
    $req ->setHeaders($contentTypeHeader);
    $body = "clientId=".$client_id."&clientSecret=".$secret."&redirectUri=https://&grant_type=password&username="
    .$username."&password=".$password;

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