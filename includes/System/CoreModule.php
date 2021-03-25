<?php

use File\FileHandler as FileHandler;
use File\PhpFileUpload as PhpFileUpload;
use File\File as File;
use File\FileLIst as FileList;
use Http\HttpHeader as HttpHeader;
use Http\HttpResponse;
use Salesforce\OAuthResponse as OAuthResponse;
use Salesforce\OAuthRequest as OAuthRequest;
use Salesforce\RestApiRequest as RestApiRequest;
use Salesforce\OAuth as OAuth;

class CoreModule extends Module {


	private $deps = array();

    public function __construct() {
    
        parent::__construct();
        
        $this->dependencies = $this->deps;
        
        $this->name = "core";
    }


    //List all uploaded files.  Uploads have already happened in HttpRequest.
	public function upload(){

		return $this->request->getFiles();
	}

	public function download($filename){

		global $fileConfig;

		$handler = new FileHandler($fileConfig);

		$file = File::fromPath($handler->getTargetPath(). "/" . $filename);
				
		return $file;
	}

	public function list(){

		global $fileConfig;

		$handler = new FileHandler($fileConfig);

		$fList = FileList::fromHandler($handler);

		return $fList;
	}

	public function oauthFlowStart($configName = null){

		$configName = "ocdla-jobs";

		$config = getOauthConfig($configName);

		$config["state"] = $configName;

		return OAuth::start($config);
	}

	// Get the access token and save it to the session variables.
	public function oauthFlowAccessToken(){

		$config = getOauthConfig($_GET["state"]);

		$config["authorization_code"] = $_GET["code"];

		$oauth = OAuthRequest::newAccessTokenRequest($config);

		$resp = $oauth->authorize();

		if($resp->hasError){

			throw new Exception("OAUTH_ERROR: {$resp->errorMessage}.");
		}

		$_SESSION["access_token"] = $resp->getAccessToken();
		$_SESSION["instance_url"] = $resp->getInstanceUrl();

		//  After you get your access token, you can call userinfo() on the api
		$_SESSION["userId"] = $config["client_id"];

		$resp2 = new HttpResponse();
		$resp2->addHeader(new HttpHeader("Location", $config['final_redirect_uri']));

		return $resp2;
	}
}