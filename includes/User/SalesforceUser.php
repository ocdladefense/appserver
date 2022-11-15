<?php

use Salesforce\SObject;


class User extends SObject implements IQueryable, ISessionHandler {


	
	const GUEST_USER_ID = "0";


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

		/*
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
		*/

		return parent::query($query);
	}
	


	public function __construct($user = array(), $type = "salesforce") {

		// var_dump($user);exit;
		$user["Id"] = $user["user_id"] ?? self::GUEST_USER_ID;
		parent::__construct($user);
		parent::__construct("User");
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

		return $this->user_type;
	}

	public function getGeoZone(){

		return $this->zoneinfo;
	}

	public function getEmail(){

		return $this->email;
	}

	public function getCountry(){

		return $this->country;
	}

	public function getInitials() {

		return !$this->isGuest() ? $this->given_name[0] . $this->family_name[0] : "G";
	}
	
	
	public function isAdmin($user = null) {
	
		return $this->user_type == "STANDARD" || $this->Id == "005j000000DSW0eAAH";
	}
	
	
	public function isMember($user = null) {
	
		return $this->user_type != "STANDARD" && $this->Id != self::GUEST_USER_ID;
	}

	public function isGuest() {

		return $this->Id == self::GUEST_USER_ID;
	}


	public function is_logged_in() {

		return $this->Id != self::GUEST_USER_ID;
	}
}