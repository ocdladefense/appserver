<?php

class User {


function add_session_data($saml = array()) {
	$userId = $saml["userId"][0];
	$username = $saml["username"][0];
	$email = $saml["email"][0];
	$isPortalUser = $saml["is_portal_user"][0];
	
	$_SESSION["userId"] = $userId;
	$_SESSION["username"] = $username;
	$_SESSION["email"] = $email;
	$_SESSION["isPortalUser"] = $isPortalUser;
}


function is_authenticated() {
	return isset($_SESSION["userId"]);
}







}