<?php

namespace Salesforce;

use Http\HttpHeader;

class OAuth {

    public static function start($config, $flow){

        return $flow == "webserver" ? self::newOAuthResponse($config,$flow) : OAuthRequest::usernamePasswordFlowAccessTokenRequest($config,$flow);
    }


    public static function newOAuthResponse($config,$flow) {

        $flowConfig = $config["auth"]["oauth"][$flow];

        $resp = new OAuthResponse();

        $url = $flowConfig["auth_url"];  // Since this is a web server oauth, there will be two oauth urls in the config.

        $state = array("connected_app_name" => $config["name"], "flow" => $flow);

        $params = array(
            "client_id"		=> $config["client_id"],
            "redirect_uri"	=> $flowConfig["auth_redirect_url"],
            "response_type" => "code",
            "state"         => json_encode($state)
        );

        $url .= "?" . http_build_query($params);

        $resp->addHeader(new HttpHeader("Location", $url));

        return $resp;
    }

    public static function setSession($connectedApp, $flow, $resp){

        \Session::set($connectedApp, $flow, "access_token", $resp->getAccessToken());
        \Session::set($connectedApp, $flow, "instance_url", $resp->getInstanceUrl());
        \Session::set($connectedApp, $flow, "userId", OAuth::getUserId($connectedApp, $flow));
    }

    public static function getUserId($connectedApp, $flow){

		$accessToken = \Session::get($connectedApp, $flow, "access_token");
		$instanceUrl = \Session::get($connectedApp, $flow, "instance_url");

		$url = "/services/oauth2/userinfo?access_token={$accessToken}";

		$req = new RestApiRequest($instanceUrl, $accessToken);

		$resp = $req->send($url);

		$userInfo = json_decode($resp->getBody());
		
		return $userInfo->user_id;
	}
}