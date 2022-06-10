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