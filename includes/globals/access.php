<?php

use function Session\is_admin;
use function Session\get_current_user;



function user_has_access($module, $route, $user = null) {
	

	$user = $user == null ? get_current_user() : $user;

	$access = $route["access"];
	$args = $route["access_args"];

	if(!isset($access) || true === $access) return true;

	if($access === false) return false;

	// Define in config/config.php.

	if($user->isAdmin()){

		return true;
	}
	else if($user->isGuest()){

		return false;
	}
	else if(is_admin()) {

		return true;
	}
	
	
	if(function_exists($access)) {

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

