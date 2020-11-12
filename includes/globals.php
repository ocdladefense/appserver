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

function load_org($name = "myDefaultOrg") {
	global $orgs;
	
	return $orgs[$name];
}

function path_to_wsdl( $fileName, $orgName = null ) {

    $path = $orgName == null ? $fileName : $orgName . "-" . $fileName;
    $file = BASE_PATH . "/config/wsdl/{$path}.wsdl";

    $exists = !file_exists($file) ? false : $file;
    
    if(!$exists){

        throw new Exception("INVALID_WSDL: Wsdl file for $class could not be found at {$file}.");
    }

    return $exists;
}


//returns the path to directory at the root level
function getPath($dir){
    $path = __DIR__."/../".$dir;
    return $path;
}
function filterScanResults($results){
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



