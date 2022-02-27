<?php

class User {

	const GUEST_USER_ID = "0";

	private $userId;
	private $name;
	private $firstName;
	private $lastName;
	private $username;
	private $preferredUsername;
	private $email;
	private $geoZone;
	private $country;
	private $userType;
	private $organizationId;
	

	public function __construct($user = array(), $type = "salesforce") {

		if(is_array($user) && $type =="salesforce"){

			$this->userId = $user["user_id"];
			$this->name = $user["name"];
			$this->firstName = $user["given_name"];
			$this->lastName = $user["family_name"];
			$this->username = $user["preferred_username"];
			$this->preferredUsername = $user["preferred_username"];
			$this->email = $user["email"];
			$this->geoZone = $user["zoneinfo"];
			$this->country = $user["address"]["country"];
			$this->userType = $user["user_type"];
			$this->organizationId = $user["organization_id"];
		}

		if($this->userId == null) $this->userId = self::GUEST_USER_ID;
	}

	public function getId(){

		return $this->userId;
	}

	public function getName(){

		return $this->name;
	}

	public function getUserName(){

		return $this->username;
	}

	public function getFirstName(){

		return $this->firstName;
	}

	public function getUserType(){

		return $this->userType;
	}

	public function getGeoZone(){

		return $this->geoZone;
	}

	public function getEmail(){

		return $this->email;
	}

	public function getCountry(){

		return $this->country;
	}

	public function getInitials() {

		return !$this->isGuest() ? $this->firstName[0] . $this->lastName[0] : "G";
	}
	
	
	public function isAdmin($user = null){
	
		return $this->userType == "STANDARD" || $this->userId == "005j000000DSW0eAAH" || $this->userId == "0055b00000KmWbbAAF";
	}
	
	
	public function isMember($user = null){
	
		return $this->userType != "STANDARD" && $this->userId != self::GUEST_USER_ID;
	}

	public function isGuest(){

		return $this->userId == self::GUEST_USER_ID;
	}


	public function is_logged_in(){

		return $this->userId != self::GUEST_USER_ID;
	}
}