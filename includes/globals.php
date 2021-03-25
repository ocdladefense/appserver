<?php

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
    return BASE_PATH . "/content/uploads";
}


function path_to_modules() {
    return  BASE_PATH . "/modules";
}

function path_to_content() {
    return BASE_PATH . "/content";
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
function user_require_auth($route, $module) {

	//  Preferred types of authorization flows.
	$preferredTypes = array("ouathwebserverflow","oauthusernamepasswordflow");

	// Get the allowed authorization flow types from the module.
	$modAuthTypes = $module["authorization"];

	$oauthAccepted;
	foreach($preferredTypes as $type){

		if(in_array($type, $modAuthTypes)){

			header("Location:/oauth/start");  // return a new response with the location header.
			$oauthAccepted = true;

			// $resp = new Http\HttpResponse();
			// $resp->addHeader(new Http\HttpHeader("Location", "/oauth/start"));

			// return $resp;
		}
	}

	if(!$oauthAccepted){

		doSAMLAuthorization();
	}
}

function user_has_access($route, $module) {

	// Define in config/config.php.
	if(defined("ADMIN_USER") && ADMIN_USER === true) return true;
	
	
	$access = $route["access"];
	$args = $route["access_args"];
	
	
	if(!isset($access) ) {
		return true;
		
	} else if( true === $access ) {
		return true;
		
	} else if(function_exists($access)) {

		return null == $args ? call_user_func($access) : call_user_func_array($access, $args);
	}
}

function is_authenticated() {
	
	return isset($_SESSION["userId"]);
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


function user_get_initials() {
	return !is_authenticated() ? "G" : ucfirst(substr($_SESSION["username"], 0, 1));
}


function getOAuthConfig($key = null) {

	global $oauth_config;

	if(null == $key) {
		foreach($oauth_config as $connectedApp) {
			$isdefault = $connectedApp["default"];
			if($isdefault) return $connectedApp;
		}
		
	} else {
		return $oauth_config[$key];
	}
	
	throw new Exception("HTTP_INIT_ERROR: No default Connected App / Org.  Check your configuration.");
}







