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

function filesystem_path($identifier) {
	$module = $GLOBALS["active_module"];
	$absolutePath = $module->getPath();
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









/// These are our access related functions.

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



