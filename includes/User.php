<?php

class User {


	public static function add_session_data($saml = array()) {
		$userId 				= $saml["userId"][0];
		$username 			= $saml["username"][0];
		$email 					= $saml["email"][0];
		$isPortalUser 	= $saml["is_portal_user"][0];
		
	
		$_SESSION["userId"] 			= $userId;
		$_SESSION["username"] 		= $username;
		$_SESSION["email"] 				= $email;
		$_SESSION["isPortalUser"] = $isPortalUser;
		$_SESSION["FirstName"]		= $firstName;
		$_SESSION["LastName"] 		= $lastName;
		$_SESSION["initials"] 		= substr($firstName, 0, 1) . substr($lastName, 0, 1);
	}


	public static function is_authenticated() {
		return defined("ADMIN_USER") && ADMIN_USER === true || isset($_SESSION["userId"]);
	}


	// Assume that uid of 1 is the super-user for now.
	//  Other ids will be unique for each individual logged-in member.
	public static function getId() {
		return defined("ADMIN_USER") && ADMIN_USER === true ? 1 : $_SESSION["userId"];
	}




}