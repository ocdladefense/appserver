<?php

function api_is_bootstrapped($connectedAppName) {
	
	$flow = "usernamepassword";

	return !empty(\Session::get($connectedAppName, $flow, "access_token"));
}