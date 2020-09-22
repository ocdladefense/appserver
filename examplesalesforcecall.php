<?php
require 'bootstrap.php'; 
session_start();

use Http\HttpHeader;
use Http\HttpRequest;
use Http\HttpResponse;
use Http\Http;


// From config/config.php
global $oauth_config; 


/**
 * Should be placed in config.php:
 *
 *
		$oauth_config = array(
			"oauth_url" => "", 
			"client_id" => "",
			"client_secret" => "",
			"username" => "",
			"password" => "",
			"security_token" => ""
		);
*/


   

// print "<h2>Config is: </h2>";
// print "<pre>" . print_r($oauth_config,true) . "</pre>";




// MAIN CODE - AUTHORIZE AND QUERY
if( authorize_to_salesforce($oauth_config) ) {
	$results = query_salesforce("SELECT Id, Name FROM Account LIMIT 10");
	print "<pre>" . print_r($results,true) . "</pre>";
} else {
	print "<h2>Salesforce authorization failed!</h2>";
}






// AUXILLIARY FUNCTION TO AUTHORIZE TO SALESFORCE
function authorize_to_salesforce($config) {


	// print "<h2>Settings are: </h2><pre>".print_r($config,true)."</pre>";

	// If we already have an access_token, so no need to reauthorize; return TRUE.
	if(isset($_SESSION["salesforce_access_token"])) return true;

	$req = new HttpRequest($config["oauth_url"]);
	$req->setPost();

	$contentTypeHeader = new HttpHeader("Content-Type", "application/x-www-form-urlencoded");
	$req->addHeader($contentTypeHeader);
	$body = "grant_type=password&client_id=".$config["client_id"]."&client_secret=".$config["client_secret"]."&username=".$config["username"]."&password=".$config["password"] . $config["security_token"];

	print "OAuth parameters will be: {$body}.<br />";

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
		print "<h2>Salesforce authorization successful!</h2>";
		return true;
	}
	
	else return false;
}




// AUXILLIARY FUNCTION TO QUERY SALESFORCE
function query_salesforce($query) {
	$endpoint = "/services/data/v49.0/query/?q=";
	// $endpoint = "/v49.0/query?q=";
	$resource_url = $_SESSION["salesforce_instance_url"].$endpoint.urlencode($query);
	
	print "<p>Will execute query at: ".$resource_url."</p>";

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
	
	$body = $resp->getBody();


	return json_decode($body);
}

