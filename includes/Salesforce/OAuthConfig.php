<?php

namespace Salesforce;

class OAuthConfig extends Config{

    private $config;

    private $username;

    private $password;

    private $securityToken;

    private $callbackUrl;


    public function __construct($config){

        $this->config = $config;
        $this->clientId = $config["client_id"];
        $this->clientSecret = $config["client_secret"];
        $this->tokenUrl = $config["token_url"];
        $this->username = $config["username"];
        $this->password = $config["password"];
        $this->securityToken = $config["security_token"];
        $this->callback = $config["callback_url"];
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
    
    public function getCallbackUrl(){

        return $this->callbackUrl;
    }
}