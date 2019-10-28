<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
require "bootstrap.php";

// this is an admin user
define("USER_LEVEL_ADMIN", 1);

// this is a normal user
define("USER_LEVEL_PUBLIC", 2);

// create a test public user and populate it
$publicUser = new stdClass();

$publicUser->firstName = "John";
$publicUser->lastName = "Doe";
$publicUser->accessLevel = USER_LEVEL_PUBLIC;

// create a test admin user and populate it
$adminUser = new stdClass();

$adminUser->firstName = "Jane";
$adminUser->lastName = "Doe";
$adminUser->accessLevel = USER_LEVEL_ADMIN;

define("USER_ACCESS_GRANTED", 1);
define("USER_ACCESS_DENIED", 0);

function accessDenied() {
    return USER_ACCESS_DENIED;
}

function accessGranted() {
    return USER_ACCESS_GRANTED;
}

function hasAccess($user) {
    return $user->accessLevel == USER_LEVEL_ADMIN;
}

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
