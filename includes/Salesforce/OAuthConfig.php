<?php

namespace Salesforce;

class OAuthConfig {

    private $config;

    private $name;

    private $default;

    private $sandbox;

    private $clientId;

    private $clientSecret;
    
    private $authorizationUrl;

    private $authorizationRedirectUrl;

    private $callbackUrl;

    private $tokenUrl;


    public function __construct($config){

        $this->config = $config;
        $this->name = $config["name"];
        $this->default = $config["default"];
        $this->sandbox = $config["sandbox"];
        $this->clientId = $config["client_id"];
        $this->clientSecret = $config["client_secret"];
        $this->tokenUrl = $config["token_url"];

        $this->callbackUrl = $config["callback_url"];

    }

    public function getFlowConfig($flow = "usernamepassword"){

        $tmp = array(
            "username" => $this->config["username"],
            "password" => $this->config["password"],
            "securityToken" => $this->config["security_token"]
        );

        return new OAuthFlowConfig($tmp);

    }

    public static function fromFlow($config, $flow){

        $oauthConfig = new self($config);
        
        return $oauthConfig->newFromFlow($oauthConfig, $flow);
    }

    public function getName(){

        return $this->name;
    }

    public function getPasswordWithSecurityToken(){

        return $this->password . $this->securityToken;
    }

    public function getAuthorizationEndpoint(){

        return $this->authorizationEndpoint;
    }

    public function getAuthorizationRedirect(){

        return $this->authorizationRedirect;
    }

    public function getCallbackUrl(){

        return $this->callbackUrl;
    }

    public function getClientId(){

        return $this->clientId;
    }

    public function getClientSecret(){

        return $this->clientSecret;
    }

    public function getTokenUrl(){

        return $this->tokenUrl;
    }

    public function getConfig(){

        return $this->config;
    }

    public function newFromFlow($oauthConfig, $flow){



        switch($flow) {

            case "usernamePassword":
                return $this->usernamePasswordFlow($oauthConfig, $flowConfig);
                break;
            case "webserver":
                return $this->webserverFlow($oauthConfig, $flowConfig);
                break;
            default:
                throw new \Exception("OAUTH_CONFIG_ERROR:   No functionality for given config.");
        }

    }

    public function usernamePasswordFlow($oauthConfig, $flowConfig){

        $oauthConfig->username = $flowConfig["username"];
        $oauthConfig->password = $flowConfig["password"];
        $oauthConfig->securityToken = $flowConfig["security_token"];
        $oauthConfig->callbackUrl = $flowConfig["callback_url"];

        return $oauthConfig;
    }

    public function webServerFlow($oauthConfig, $flowConfig){

        $oauthConfig->authorizationUrl = $flowConfig["auth_url"];
        $oauthConfig->authorizationRedirectUrl = $flowConfig["auth_redirect_url"];
        $oauthConfig->callbackUrl = $flowConfig["final_redirect_url"];
        return $oauthConfig;
    }
}