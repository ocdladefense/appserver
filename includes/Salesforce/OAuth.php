<?php

namespace Salesforce;

use Http\HttpHeader;

class OAuth {

    public static function start($config, $flow){

        return $flow == "webserver" ? self::newOAuthResponse($config) : OAuthRequest::usernamePasswordFlowAccessTokenRequest($config);
    }


    public static function newOAuthResponse($config) {

        $flowConfig = $flowConfig = $config["auth"]["oauth"]["webserver"];

        $resp = new OAuthResponse();

        $url = $flowConfig["auth_url"];  // Since this is a web server oauth, there will be two oauth urls in the config.

        $params = array(
            "client_id"		=> $config["client_id"],
            "redirect_uri"	=> $flowConfig["auth_redirect_url"],
            "response_type" => "code",
            "state"         => $config["name"]
        );

        $url .= "?" . http_build_query($params);

        $resp->addHeader(new HttpHeader("Location", $url));

        return $resp;
    }

    public static function setSession($connectedApp, $config, $resp){

        \Session::set($connectedApp, "access_token", $resp->getAccessToken());
        \Session::set($connectedApp, "instance_url", $resp->getInstanceUrl());
    }
}