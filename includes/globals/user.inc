<?php 



function current_user() {

    $user = \Session::getUser();

    if($user == null) $user = new \User();

    return $user;
}


function is_admin() {

    $mock = defined("ADMIN_USER") && ADMIN_USER === true;
	if(true === $mock) return true;
	else {
		$user = current_user();
		return $user->isAdmin();
	}
}


function is_admin_user() {
	$user = current_user();
	return is_admin() || $user->isAdmin();
}



function get_user_info() {

	$module = new Module\Module();
	
	// Get the salesforce "user info" for the current user.
	$userInfoEndpoint = "/services/oauth2/userinfo?access_token={$accessToken}";
	$req = new RestApiRequest($instanceUrl, $accessToken);
	$resp = $req->send($userInfoEndpoint);

	$uInfo = $resp->getBody();

	//var_dump($uInfo);exit;
    return $uInfo;
}
