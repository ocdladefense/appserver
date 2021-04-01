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


	// Get the access token and save it to the session variables.
	public function oauthFlowAccessToken(){

		$info = json_decode($_GET["state"], true);
		$connectedApp = $info["connected_app_name"];
		$flow = $info["flow"];

		$config = getOauthConfig($connectedApp);

		$config["authorization_code"] = $_GET["code"];

		$oauth = OAuthRequest::newAccessTokenRequest($config, "webserver");

		$resp = $oauth->authorize();

		if(!$resp->success()){

			throw new OAuthException($resp->getErrorMessage());
		}

		OAuth::setSession($connectedApp, $flow, $resp->getInstanceUrl(), $resp->getAccessToken());

		$resp2 = new HttpResponse();
		$flowConfig = $config["auth"]["oauth"][$flow];
		$resp2->addHeader(new HttpHeader("Location", $flowConfig['final_redirect_url']));

		return $resp2;
	}
}