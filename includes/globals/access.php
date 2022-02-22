<?php

use function Session\is_admin;

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

