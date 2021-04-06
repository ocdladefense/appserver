<?php

namespace Salesforce;

class OAuthConfig {

    private $config;

    private $clientId;

    private $clientSecret;

    private $username;

    private $password;

    private $authorizationUrl;

    private $authorizationRedirectUrl;

    private $tokenUrl;

    private $finalRedirectUrl;

    private $callbackUrl;


    public function __construct($config){

        var_dump($config);exit;

        $this->config = $config;


    }

    public function getUsername(){

        return $this->username;
    }

    public function getClientId(){

        return $this->clientId;
    }

    public function getClientSecret(){

        return $this->clientSecret;
    }

    public function getPassword(){

        return $this->password;
    }

    public function getTokenUrl(){

        return $this->tokenUrl;
    }

    public function getAuthorizationUrl(){

        return $this->authorizationUrl;
    }

    public function getAuthorizationRedirectUrl(){

        return $this->authorizationRedirectUrl;
    }

    public function getFinalRedirectUrl(){

        return $this->finalRedirectUrl;
    }
    
    public function getCallbackUrl(){

        return $this->callbackUrl;
    }

    public function getConfig(){

        return $this->config;
    }

    public function getFlowConfig($flow){

        `
    }
}