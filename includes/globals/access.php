<?php
use Ocdla\Session as Session;


function user_has_access($module, $route, $user = null) {
	

	$user = $user == null ? current_user() : $user;

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
	
	// The connected app setting can also be "default".
	$key = $module->getInfo()["connectedApp"];
	$app = get_oauth_config($key);
	
	$name = $app->getName();
	$flow = $route["authorization"];
	
	return !empty(Session::get([$name, $flow, "userId"]));
}

