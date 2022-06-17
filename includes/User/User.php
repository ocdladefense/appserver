<?php

class User {


	
	const GUEST_USER_ID = "0";

	private $SObject;

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

	private $customerProfileId;

	private $contactId;
	



	public function setSObject($SObject) {
		$this->SObject = $SObject;
	}


	/**
	 * @method query
	 * 
	 * @description Return the value stored at the given field.
	 * 
	 * User.Id, User.ContactId, User.Username, User.Email
	 * 
	 * Contact.Id, Contact.AccountId, Contact.Email
	 * 
	 * Account.
	 */
	public function query($query) {

		list($object,$field) = explode(".", $query);
		if(null == $field) throw new \Exception("PARSE_ERROR: Field is missing or null.");

		if("User" == $object) {
			return $this->SObject[$field];
		}
		else if("Contact" == $object) {
			return $this->SObject[$object][$field];
		}
		else if("Account" == $object) {
			return $this->SObject["Contact"][$object][$field];
		}

		return null;
	}



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

	public function setExternalCustomerProfileId($id) {
		$this->customerProfileId = $id;
	}

	public function getExternalCustomerProfileId() {
		return $this->customerProfileId;
	}

	public function setContactId($id) {
		$this->contactId = $id;
	}

	public function getContactId() {
		return $this->contactId;
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
	
	
	public function isAdmin($user = null) {
	
		return $this->userType == "STANDARD" || $this->userId == "005j000000DSW0eAAH";
	}
	
	
	public function isMember($user = null) {
	
		return $this->userType != "STANDARD" && $this->userId != self::GUEST_USER_ID;
	}

	public function isGuest() {

		return $this->userId == self::GUEST_USER_ID;
	}


	public function is_logged_in() {

		return $this->userId != self::GUEST_USER_ID;
	}
}