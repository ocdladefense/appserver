<?php

namespace Salesforce;

use Http\HttpHeader;
use Http\HttpRequest as HttpRequest;

class OAuth {

    public static function start($config, $flow){

        return $flow == "webserver" ? self::newOAuthResponse($config,$flow) : OAuthRequest::newAccessTokenRequest($config,$flow);
    }

    // This is step one.  We are going to make a request to the "auth_url".
    // We do this by redirecting the user agent to the auth_url.
    public static function newOAuthResponse($config,$flow) {

        $flowConfig = $config->getFlowConfig($flow);

        $resp = new OAuthResponse();

        $url = $flowConfig->getAuthorizationUrl();  // Since this is a web server oauth, there will be two oauth urls in the config.

        $state = array("connected_app_name" => $config->getName(), "flow" => $flow);

        $body = array(
            "client_id"		=> $config->getClientId(),
            "redirect_uri"	=> $flowConfig->getAuthorizationRedirect(),
            "response_type" => "code",
            "state"         => json_encode($state)
        );

        $url .= "?" . http_build_query($body);

        $resp->addHeader(new HttpHeader("Location", $url));

        return $resp;
    }

    public static function setSession($connectedApp, $flow, $instanceUrl, $accessToken, $refreshToken = null){

        if($refreshToken != null) \Session::set($connectedApp, $flow, "refresh_token", $refreshToken);
        

        \Session::set($connectedApp, $flow, "instance_url", $instanceUrl);
        \Session::set($connectedApp, $flow, "access_token", $accessToken);


        $userInfo = OAuth::getUser($connectedApp, $flow);
        \Session::set($connectedApp, $flow, "userId", $userInfo["user_id"]);
    }

    public static function getUser($connectedApp, $flow){

		$accessToken = \Session::get($connectedApp, $flow, "access_token");
		$instanceUrl = \Session::get($connectedApp, $flow, "instance_url");

		$url = "/services/oauth2/userinfo?access_token={$accessToken}";

		$req = new RestApiRequest($instanceUrl, $accessToken);

		$resp = $req->send($url);
		
		return $resp->getBody();
	}

    public static function logout($connectedApp, $flow, $sandbox = false){
		$accessToken = \Session::get($connectedApp, $flow, "access_token");
        $url = "https://login.salesforce.com/services/oauth2/revoke?token=";
        if($sandbox){
            $url = "https://test.salesforce.com/services/oauth2/revoke?token=";
        }
        

        $req = new \Http\HttpRequest();
        $req->setUrl($url.$accessToken);
        $req->setMethod("GET");
        $config = array(
            "returntransfer" 		=> true,
            "useragent" 				=> "Mozilla/5.0",
            "followlocation" 		=> true,
            "ssl_verifyhost" 		=> false,
            "ssl_verifypeer" 		=> false
        );

        $http = new \Http\Http($config);
    
        $resp = $http->send($req, true);
        if($resp->getStatusCode() == 200){
            $accessToken = \Session::set($connectedApp, $flow, "access_token",null);
            \Session::set($connectedApp, $flow, "user",null);
            return true;
        }
        return false;
    }
}