<?php

class User {
	public $data ="";
	private $userId;
	private $salesforceData = array();
	public $name;
	public $firstName;
	public $lastName;
	public $username;
	public $shortUsername;
	private $connectedApp;
	private $flow;
	private $userType;

	public function __construct($user = array(), $type = "salesforce")
	{
		$this->data = $user;
		if(is_array($user) && $type =="salesforce"){
			$this->userId = $user["user_id"];
			$this->salesforceData["organization_id"] = $user["organization_id"];
			$this->salesforceData["preferred_username"] = $user["preferred_username"];
			$this->salesforceData["nickname"] = $user["nickname"];
			$this->name = $user["name"];
			$this->firstName = $user["given_name"];
			$this->lastName = $user["family_name"];
			$this->username = $user["preferred_username"];
			$this->shortUsername = substr($user["preferred_username"], 0, 18)."...";
			$this->salesforceData["zoneinfo"] = $user["zoneinfo"];
			$this->salesforceData["photos"] = $user["photos"];
			$this->userType = $user["user_type"];
		}
	}



	public static function add_session_data($saml = array()) {		
		if(!empty($saml)){
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