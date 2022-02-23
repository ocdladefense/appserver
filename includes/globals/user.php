<?php

function is_admin_user(){

	return defined("ADMIN_USER") && ADMIN_USER === true;
}

function get_user_info(){

	$module = new Module\Module();
	
	// Get the salesforce "user info" for the current user.
	$userInfoEndpoint = "/services/oauth2/userinfo?access_token={$accessToken}";
	$req = new RestApiRequest($instanceUrl, $accessToken);
	$resp = $req->send($userInfoEndpoint);

	$uInfo = $resp->getBody();

	//var_dump($uInfo);exit;
    return $uInfo;
}
