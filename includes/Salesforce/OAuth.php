<?php

namespace Salesforce;

use Http\HttpHeader;

class OAuth {

    public static function start($config){

        return $config["flow"] == "webserver" ? self::newOAuthResponse($config) : self::newOAuthRequest($config);
    }


    public static function newOAuthResponse($config) {

        $resp = new OAuthResponse();

        $url = $config["auth_url"];  // Since this is a web server oauth, there will be two oauth urls in the config.

        $params = array(
            "client_id"		=> $config["client_id"],
            "redirect_uri"	=> $config["auth_redirect_uri"],
            "response_type" => "code",
            "state"         => $config["state"]
        );

        $url .= "?" . http_build_query($params);

        $resp->addHeader(new HttpHeader("Location", $url));

        return $resp;
    }

    public static function newOAuthRequest($config) {
		
		switch ($config["flow"]) {
			case "webserver":
				return OAuthRequest::webServerFlowAccessTokenRequest($config);
			default:
				return OAuthRequest::usernamePasswordFlowAccessTokenRequest($config);
		}
	}
}