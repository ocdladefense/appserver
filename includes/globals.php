<?php

use function Session\is_admin;

$log = array();
$debug = true;

function l($m) {
	// print "<p>{$m}</p>";
	$log[] = $m;
}



function dump($var) {
	print "<pre>". print_r($var,true)."</pre>";
}

function getPathToModules(){
    return  BASE_PATH . "/modules";
}
function getPathToContent(){
    return BASE_PATH . "/content";
}
function getUploadPath(){
    return BASE_PATH .DIRECTORY_SEPARATOR. "content".DIRECTORY_SEPARATOR."uploads";
}


function path_to_modules() {
    return  BASE_PATH . "/modules";
}

function path_to_content() {
    return BASE_PATH . "/content";
}

function path_to_config() {
    return BASE_PATH . "/config";
}

function path_to_modules_config() {
    return BASE_PATH .DIRECTORY_SEPARATOR. "config".DIRECTORY_SEPARATOR."modules";
}

function path_to_modules_upload() {
    return getUploadPath().DIRECTORY_SEPARATOR."modules";
}

function set_active_module($module){

	$GLOBALS["active_module"] = $module;
}


function module_path(){
	
	$module = $GLOBALS["active_module"];
	$absolutePath = $module->getPath();
	$trim = BASE_PATH;  // Remove the base path in order to get the url.
	$pathParts = explode($trim, $absolutePath);
	$moduleUri = $pathParts[1];
	$removeBackSlashes = str_replace(DIRECTORY_SEPARATOR, "/", $moduleUri);

	return $removeBackSlashes;
}

function path_to_uploads() {
    return BASE_PATH . "/content/uploads";
}

function load_org($alias = "myDefaultOrg") {
	global $orgs;

	return $orgs[$alias];
}



function path_to_wsdl( $filename, $orgAlias = null ) {

		
    $file = null == $orgAlias ? $filename : ($orgAlias . "-" . $filename);
    $path = BASE_PATH . "/config/wsdl/{$file}.wsdl";
    
    if(!file_exists($path)){

        throw new Exception("INVALID_WSDL: Wsdl file for {$file} could not be found at {$path}.");
    }

    return $path;
}



//returns the path to directory at the root level
function getPath($dir) {
    $path = __DIR__."/../".$dir;
    return $path;
}



function filterScanResults($results) {
    $unfilteredResults = $results;
    $filteredResults = array();

    foreach($unfilteredResults as $unfiltered){
        if($unfiltered != "." && $unfiltered != ".."){
            $filteredResults[] = $unfiltered;
        }
    }
    return $filteredResults;
}



function stringContains($haystack, $needle){
    if(strpos($haystack, $needle) !== false){
        return true;
    }
    return false;

}

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


// Determine if the user has already authorized against a oauth flow.

function module_requires_authorization($module){

	// If the module has no connected app set, the user is authorized for the module.
	return isset($module->getInfo()["connectedApp"]);
}

function is_user_authorized($module, $route = null){

	$connectedAppSetting = $module->getInfo()["connectedApp"];
	$connectedAppName = get_oauth_config($connectedAppSetting)->getName();
	return $route == null ? is_module_authorized($module) : is_route_authorized($connectedAppName, $route);

}
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
	
	$flow = $route["authorization"];

	return !empty(\Session::get($connectedAppName, $flow, "access_token"));
}

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

	var_dump($uInfo);exit;
    return $uInfo;
}



/// These are our access related functions.
function user_has_access($module, $route) {

	$access = $route["access"];
	$args = $route["access_args"];
	
	if($access === false) return false;

	// Define in config/config.php.
	if(is_admin()) return true;
	
	
	if(!isset($access) ) {
		return true;
		
	} else if( true === $access ) {

		return true;

	} else if(false === $access) {

		return false;
		
	} else if(function_exists($access)) {

		$args = array($module, $route);

		return null == $args ? call_user_func($access) : call_user_func_array($access, $args);
	}
}

function is_authenticated($module, $route) {
	
	// The connected app setting can also be "default"
	$connectedAppSetting = $module->getInfo()["connectedApp"];
	$connectedAppName = get_oauth_config($connectedAppSetting)->getName();
	$flow = $route["authorization"];
	
	return !empty(\Session::get($connectedAppName, $flow, "userId"));
}

//option 1:
	//let the user set the language preference on their account page
	//stored in session
//option 2: 
	//present the user with a dropdown
	//arbitrarily with the setting of the dropdown
	//stored in session



function getDefaultLanguage(){
	//if lang parameter was not sent use language in session else default to english
	$language = empty($_GET["lang"]) == false ? $_GET["lang"]: $_SESSION["language"]?? "en";
	$_SESSION["language"] = $language;
	return $language;
}

function redirect($path) {

	$resp = new Http\HttpResponse();
	$resp->addHeader(new Http\HttpHeader("Location", $path));

	return $resp;
}

function path_to_root_vendor_directory(){

	return $_SERVER["DOCUMENT_ROOT"] . "/vendor";
}