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
use Salesforce\OAuthException;

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

		$config = get_oauth_config($connectedApp);

		$config->setAuthorizationCode($_GET["code"]);

		$oauth = OAuthRequest::newAccessTokenRequest($config, "webserver");

		$resp = $oauth->authorize();

		if(!$resp->success()){

			throw new OAuthException($resp->getErrorMessage());
		}

		// Step 1: Set up the session for the connected app.
		OAuth::setSession($connectedApp, $flow, $resp->getInstanceUrl(), $resp->getAccessToken(), $resp->getRefreshToken());

		// Step 2: Declare who the current user is.  Create a user session.
		$userInfo = OAuth::getUser($config->getName(), "webserver");
		$user = new \User($userInfo);
		\Session::setUser($user);

		$resp2 = new HttpResponse();

		$flowConfig = $config->getFlowConfig($flow);

		$resp2->addHeader(new HttpHeader("Location", $flowConfig->getCallbackUrl()));

		return $resp2;
	}

	public function userLogin($connectedAppName = null, $flow = "webserver"){

		$_COOKIE["PHPSESSID"] = array();

		$config = get_oauth_config($connectedAppName);

		OAuth::start($config, $flow);

		$redirect = $this->buildRedirect();

		return redirect($redirect);

	}

	public function userLogout(){

		$_COOKIE["PHPSESSID"] = array();
		$_SESSION = array();

		$redirect = $this->buildRedirect();

		return redirect($redirect);
	}

	public function userProfile(){

		$user = Session::getUser();

		if($user != null){

			$name = $user->getName();
			$username = $user->getUserName;
			$userType = $user->isAdmin() ? "Admin" : "Customer";
			$email = $user->getEmail();
			$geoZone = $user->getGeoZone();
			$country = $user->getCountry();
			$redirect = $this->buildRedirect();
		}

		$form = "
		<a href='$redirect'>Go Back</a>
		<p><strong>Name:</strong>$name</p><br />
		<p><strong>Username:</strong>$username</p><br />
		<p><strong>Email:</strong>$email</p><br />
		<p><strong>Geographical Zone:</strong>$geoZone</p><br />
		<p><strong>Country:</strong>$country</p><br />
		<p><strong>User Type:</strong>$userType</p><br />";

		return $form;
	}

	public function buildRedirect(){

		$redirectParts = explode("/", $_SERVER["HTTP_REFERER"]);

		array_shift($redirectParts); // Remove the protocol
		array_shift($redirectParts); // Remove empty element
		array_shift($redirectParts); // Remove domain

		$redirect = "/" . implode("/", $redirectParts);

		return $redirect;
	}
}