<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
require "bootstrap.php";

// 1. Open JSON file
// 2. Convert JSON to PHP
// 3. Foreach loop through list of websites
// 4. Instantiate an HTTPRequest object for each website
// 5. Get the status code of each website and save it
$fileName = "sites.json";
$handle = fopen ($fileName, "r");
$json = fread($handle,filesize($fileName));

$allSitesHealthy = true;

$sites = json_decode($json);

$page = new Template("page");
$content = new Template("site-status");



echo $page->render(array('content' => $content->render(array('sites'=>$sites))));
