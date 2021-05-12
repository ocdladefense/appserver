<?php

namespace Salesforce;

use Http\HttpHeader;

class OAuth {

    public static function start($config, $flow){

        return $flow == "webserver" ? self::newOAuthResponse($config,$flow) : OAuthRequest::newAccessTokenRequest($config,$flow);
    }


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
        \User::add_session_data($userInfo,"salesforce",$connectedApp);
  
    }

    public static function getUser($connectedApp, $flow){

		$accessToken = \Session::get($connectedApp, $flow, "access_token");
		$instanceUrl = \Session::get($connectedApp, $flow, "instance_url");

		$url = "/services/oauth2/userinfo?access_token={$accessToken}";

		$req = new RestApiRequest($instanceUrl, $accessToken);

		$resp = $req->send($url);
		
		return $resp->getBody();
	}
}