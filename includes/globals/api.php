<?php

use Salesforce\RestApiRequest;



function api_is_bootstrapped($connectedAppName) {
	
	$flow = "usernamepassword";
	
	if(cache_get("access_token") == null) {

		return false;

	} else {

		return true;
	}
}


function loadApi() {

	$config = get_oauth_config();
	
	// If a OAuth flow is set on the route get that flow, and get the
	// access token that is stored in at the index of the flow for the connected app.
	// Refresh token does not work with the username password flow.
	$flow = "usernamepassword";

	
	// $accessToken = Session::get($config->getName(), $flow, "access_token");
	// $instanceUrl = Session::get($config->getName(), $flow, "instance_url");
	$instanceUrl = cache_get("instance_url");
	$accessToken = cache_get("access_token");

	return new RestApiRequest($instanceUrl, $accessToken);
}


/*
protected function loadForceApi($app = null, $debug = false) {
	var_dump($this->getInfo()); 
	if(empty($this->getInfo()["connectedApp"])){
		
		throw new Exception("CONFIGURATION_ERROR: No 'Connected App' sepecified.  Update the 'module.json' file for your module.");
	}


	$config = get_oauth_config($connectedAppName);
	
	$route = $this->getCurrentRoute();
	
	// If a OAuth flow is set on the route get that flow, and get the
	// access token that is stored in at the index of the flow for the connected app.
	// Refresh token does not work with the username password flow.
	$flow = isset($route["authorization"]) ? $route["authorization"] : "usernamepassword";

	
	if("usernamepassword" == $flow) {
		$instanceUrl = cache_get("instance_url");
		$accessToken = cache_get("access_token");
	} else if("webserver" == $route["authorization"]) {
		$accessToken = Session::get($config->getName(), $flow, "access_token");
		$instanceUrl = Session::get($config->getName(), $flow, "instance_url");
	}
	
	// var_dump($accessToken, $instanceUrl);exit;
	$req = new RestApiRequest($instanceUrl, $accessToken);

	return $req;
}
*/