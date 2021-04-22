<?php

namespace Salesforce;

class OAuthFlowConfig {

    private $username;

    private $password;

    private $securityToken;

    public function __construct($config){

        $this->username = $config["username"];
        $this->password = $config["password"];
        $this->securityToken = $config["securityToken"];

    }


    public function getUsername(){

        return $this->username;
    }

    public function getPassword(){

        return $this->password;
    }

    public function getSecurityToken(){

        return $this->securityToken;
    }
}