<?php

namespace ClickpdxStore;
class Customer {

    private $userId;
    private $customerProfileId;
    private $firstName;
    private $lastName;
    private $birthday;
    private $email;

    public function __construct($userId) {

    }

    public function getName() {
        return $firstName . $lastName;
    }

    public function getEmail() {
        return $email;
    }
}