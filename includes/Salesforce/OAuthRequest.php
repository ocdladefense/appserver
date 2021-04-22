<?php

namespace Salesforce;

use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use Http\HttpResponse;

class OAuthRequest extends HttpRequest {

	const WEB_SERVER_FLOW = 0x001;
	
	const USERNAME_PASSWORD_FLOW = 0x000;
		
    private $oauth_config = array();



    public function __construct($url) {

        parent::__construct($url);

    }

	// Figure out which flow to return.
	public static function newAccessTokenRequest($config, $flow){

		switch($flow){
			case "usernamepassword":
				return self::usernamePasswordFlowAccessTokenRequest($config, $flow);
				break;
			case "webserver":
				return self::webServerFlowAccessTokenRequest($config, $flow);
				break;
			case "refreshtoken":
				return self::refreshAccessTokenRequest($config, $flow);
			default:
				throw new \Exception("ACCESS_TOKEN_REQUEST_ERROR: No built in functionality for {$flow} OAuth flow");
		}
	}
	

	public static function usernamePasswordFlowAccessTokenRequest($config, $flow) {

		$flowConfig = $config->getFlowConfig($flow);

		if($flowConfig->getTokenUrl() == null){

			throw new \Exception("null token url");
		}

		$req = new OAuthRequest($flowConfig->getTokenUrl());

		$body = array(
			"grant_type" 			=> "password",
			"client_id" 			=> $config->getClientId(),
			"client_secret"			=> $config->getClientSecret(),
			"username"				=> $flowConfig->getUserName(),
			"password"				=> $flowConfig->getPassword() . $flowConfig->getSecurityToken()
		);

		$body = http_build_query($body);
		$contentType = new HttpHeader("Content-Type", "application/x-www-form-urlencoded");
		$req->addHeader($contentType);
		
		$req->setBody($body);
		$req->setMethod("POST");
		// Sending a HttpResponse class as a Header to represent the HttpResponse.
		$req->addHeader(new HttpHeader("X-HttpClient-ResponseClass","\Salesforce\OAuthResponse"));

		return $req;
	}
	
	
	public static function webServerFlowAccessTokenRequest($config, $flow) {

		$flowConfig = $config->getFlowConfig($flow);

		$body = array(
			"grant_type"		=> "authorization_code",
			"client_id"			=> $config->getClientId(),
			"client_secret" 	=> $config->getClientSecret(),
			"code" 				=> $config->getAuthorizationCode(),
			"redirect_uri"		=> $flowConfig->getCallbackUrl()
		);

		//var_dump($body);exit;

		$body = http_build_query($body);

		$req = new OAuthRequest($flowConfig->getTokenUrl());
		
		$req->setMethod("POST");
		$req->setBody($body);
		$req->addHeader(new HttpHeader("Content-Type","application/x-www-form-urlencoded")); 

		return $req;
	}

	public static function refreshAccessTokenRequest($config, $flow) {

		$flowConfig = $config->getFlowConfig($flow);

		$body = array(
			"grant_type"		=> "refresh_token",
			"client_id"			=> $config->getClientId(),
			"client_secret" 	=> $config->getClientSecret(),
			"refresh_token" 	=> \Session::get($config->getName(), $flow, "refresh_token")
		);

		$body = http_build_query($body);

		$req = new OAuthRequest($flowConfig->getTokenUrl());
		
		$req->setMethod("POST");
		$req->setBody($body);
		$req->addHeader(new HttpHeader("Content-Type","application/x-www-form-urlencoded")); 

		return $req;
	}


	public function authorize($debug = false) {
		
		// Use a custom HttpResponse class to represent the HttpResponse.

		$config = array(
				"returntransfer" => true,
				"useragent" => "Mozilla/5.0",
				"followlocation" => true,
				"ssl_verifyhost" => false,
				"ssl_verifypeer" => false
		);

		$this->addHeader(new HttpHeader("X-HttpClient-ResponseClass","\Salesforce\OAuthResponse"));

		$http = new Http($config);
		$resp = $http->send($this);
		
		if($debug) {
			var_dump($this);
			$http->printSessionLog();
			var_dump($resp);
		}
		
		return $resp;
	}
		
    
    /**
    	* Use an OAuth 2.0 username/password flow 
    	*  for authorizing to Salesforce.
    	*  After authorizing we can use the REST API.
    	*  
    	*  If the session already has an access_token saved,
    	*  then attempt to use it before re-authenticating.
    	*
    	* @return RespApiResult
    	*/
    public static function init() {
    
		$resp = new HttpResponse();
		
		$body = new stdClass();
		// $body->

		// No need to re-authenticate.
		if(!empty($_SESSION["salesforce_access_token"])) {
		
		}
        $_SESSION["login_attempts"]++;
    
				// We're authenticating, so reset any previous variables.
        unset($_SESSION["salesforce_access_token"]);
        unset($_SESSION["salesforce_instance_url"]);


        //commented out to get around too many login errors~not sure why I am getting that message//
        if($_SESSION["login_attempts"] > self::MAX_LOGIN_ATTEMPTS) {
            throw new Exception ("OAUTH_AUTHENTICATION_ERROR: Too many login attempts.");
        }


        if($resp->isSuccess()) {
            $_SESSION["login_attempts"] = 0;
            $_SESSION["salesforce_instance_url"] = $resp->getInstanceUrl();
            $_SESSION["salesforce_access_token"] = $resp->getAccessToken();
        } else {
            throw new SalesforceAuthException("Not Authorized");
        }
    }
    


	private static function isValidSalesforceUsername($username) {
		//checking username for @ and .
		return strpos($username,"@") !== false && strpos($username,".") !== false;
	}



	private static function isValidOAuthTokenUrl($url) {

		//checking oauth url for .salesforce.com/services/oauth2/token
		return strpos(strtolower($url),".salesforce.com/services/oauth2/token") !== false;
	}

}