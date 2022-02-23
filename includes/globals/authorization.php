<?php


// Decided which oauth flow to use.
function user_require_auth($connectedAppName, $route) {

	if(isset($route["access"]) && $route["access"] != true && $route["access"] != false && !isset($route["authorization"])){

		throw new Exception("ROUTE_AUTHORIZATION_ERROR:You must set an authoriztion key that is set to a flow, when executing a route that has an access modifier.");
	}

	$authFlow = $route["authorization"];

	$config = get_oauth_config($connectedAppName);

	// Start now takes two parameters.
	return Salesforce\OAuth::start($config, $authFlow);
}

function refresh_user_pass_access_token(Salesforce\RestApiRequest $req){

	$config = get_oauth_config();

	$oauthRequest = Salesforce\OAuth::start($config, "usernamepassword");

	$oauthResponse = $oauthRequest->authorize();

	$req->setAccessToken($oauthResponse->getAccessToken());

	Salesforce\OAuth::setSession($config->getName(), "usernamepassword", $oauthResponse->getAccessToken(), $oauthResponse->getInstanceUrl());

	return $req;
}




function is_user_authorized($module, $route = null){

	$connectedAppSetting = $module->getInfo()["connectedApp"];
	$connectedAppName = get_oauth_config($connectedAppSetting)->getName();

	return $route == null ? is_module_authorized($module) : is_route_authorized($connectedAppName, $route);
}




// Does the user have session data stored for the usernamepassword flow of the connected app?
function is_module_authorized($module) {

	$moduleName = $module->getCurrentRoute()["module"];

	// If the module has no connected app set, the user is authorized for the module.
	if(!isset($module->getInfo()["connectedApp"])) return true;
	
	// Necessary because key can be "default".
	$connectedAppSetting = $module->getInfo()["connectedApp"];
	$connectedAppName = get_oauth_config($connectedAppSetting)->getName();
	$flow = "usernamepassword";

	return !empty(\Session::get($connectedAppName, $flow, "access_token"));
}




// Determine if the user has already authorized against a oauth flow.
function is_route_authorized($connectedAppName, $route) {

	// If the route has no authorization flow set, the user is authorized for the route.
	if(!isset($route["authorization"])) return true;
	

	// When set, usually set to "webserver".
	$flow = $route["authorization"];

	return !empty(\Session::get($connectedAppName, $flow, "access_token"));
}





function module_requires_authorization($module){

	// If the module has no connected app set, the user is authorized for the module.
	return isset($module->getInfo()["connectedApp"]);
}




// DEPRECATED
function doSAMLAuthorization(){

	header("Location: /login", true, 302);
	exit;
	
	
	$as = new \SimpleSAML\Auth\Simple('default-sp');

	$as->requireAuth();

	$attributes = $as->getAttributes();
	// print_r($attributes);

	// This session will be a SimpleSAML session.
	// print_r($_SESSION);

	// This session will be a PHP session.
	// cleanup the SimpleSAML session; also restores the previous session.
	$session = \SimpleSAML\Session::getSessionFromRequest();
	$session->cleanup();

	$_SESSION["saml"] = $attributes;
	// print_r($_SESSION);
}


function get_oauth_config($key = null) {

	global $oauth_config;

	if(null == $key || $key == "default") {

		$defaultConfigs = array();

		foreach($oauth_config as $key => $connectedApp) {

			$connectedApp["name"] = $key;

			if($connectedApp["default"]) {

				$defaultConfigs[] = $connectedApp;
			}
		}

        //if(count($defaultConfigs) > 1) throw new Exception("CONFIG_ERROR: Only one connected app can be set to default in you configuration.");
        if(count($defaultConfigs) == 0) throw new Exception("CONFIG_ERROR: No connected app is set to default in your configuration, and no connected app is set on the module.");

        return new Salesforce\OAuthConfig($defaultConfigs[0]);

		
	} else {

		$config = $oauth_config[$key];
		$config["name"] = $key;

		return new Salesforce\OAuthConfig($config);
	}
	
	throw new Exception("HTTP_INIT_ERROR: No default Connected App / Org.  Check your configuration.");
}

