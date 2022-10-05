<?php



if(!defined("BASE_PATH")) define("BASE_PATH", __DIR__);

require(BASE_PATH . "/includes/autoload.php");

if(file_exists(BASE_PATH.'/config/config.php')) {		
	require_once BASE_PATH.'/config/config.php';
}

require(BASE_PATH . "/includes/settings.php");
