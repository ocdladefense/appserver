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


	public function showStatus($message) {

		
		$req = $this->getRequest();
		$body = $req->getBody();

		return $message;
		return $body;
	}



    // List all uploaded files.  Uploads have already happened in HttpRequest.
	public function upload() { 
		$req = $this->getRequest();
		$fileList = $req->getFiles();

		if(empty($fileList)) {
			$result = $this->saveBinaryData();
			$json = json_encode($result);
		}

		

		if(empty($fileList) && empty($json)) {
			throw new Exception("No files uploaded.");
		}

		$json = $json ?? $fileList->toJson();


		$resp = new Http\HttpResponse();
		$resp->addHeader(new Http\HttpHeader("Content-Type","application/json"));
		$resp->setBody($json);

		return $resp;
	}



	// https://babeljs.io/docs/en/#pluggable
	public function saveBinaryData() {
		$req = $this->getRequest();
		
		$headers = apache_request_headers();
		
		$length = $headers["Content-Length"];
		$contentType = $headers["Content-Type"];
		$cd = $headers["Content-Disposition"];

		// Parese the content-disposition header.
		$parts = array_map(function($p) { return trim($p); }, explode(";",$cd));
		$d["disposition"] = array_shift($parts); // Inline or attachment.
		foreach($parts as $p) {
			// Remove double-quotes from filename.
			list($field,$value) = explode("=",str_replace("\"", "", $p));
			$d[$field] = $value;
		}


		$filename = $d["filename"];

		$filepath = BASE_PATH. "/content/uploads/{$filename}";

		if(file_exists($filepath)) {
			// Rename the file with an index.
		}

		// var_dump($headers);exit;
		$hSource = fopen('php://input', 'r');
		$hDest = fopen($filepath, 'w');
		while (!feof($hSource)) {
			/*  
			 *  I'm going to read in 1K chunks. You could make this 
			 *  larger, but as a rule of thumb I'd keep it to 1/4 of 
			 *  your php memory_limit.
			 */
			$chunk = fread($hSource, 1024);
			fwrite($hDest, $chunk);
			$chunk = null;
			unset($chunk);
		}
		fclose($hSource);
		fclose($hDest);

		return array("filename" => $filename, "size" => 1024);
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

	public function pageNotFound($message) {

		$tpl = new Template("404");
		$tpl->addPath(__DIR__ . "/core-templates");

		$page = $tpl->render(array("message" => $message));

		$resp = new HttpResponse();
		$resp->setBody($page);
		$resp->setStatusCode(404);
		$resp->addHeader(new HttpHeader("X-Theme","default"));

		return $resp;
	}





	public function accessDenied() {

		$resp = new HttpResponse();
		$resp->setStatusCode(403);
		$resp->setBody("Access Denied! <a href='/login'>Login</a> for more info.");
		$resp->addHeader(new HttpHeader("X-Theme","default"));

		return $resp;
	}



	
	public function login() {

        //  This is the module flow not the route flow
        // $connectedAppName = $module->get("connectedApp"); @jbernal
        
		//get the connected app config from the module
        //if there is a default then include it on the module
        $config = get_oauth_config("default");

		// We should prevent the user from logging in a second time; 
		// If they are already logged in.
        // if(!is_user_authorized($module)) {


		$flow = "webserver";

		$_SESSION["login_redirect"] = $_SERVER["HTTP_REFERER"];
		
		$httpMessage = OAuth::start($config, $flow);

	
		if(!\Http\Http::isHttpResponse($httpMessage)) {
			throw new Exception("MALFORMED_RESPONSE_ERROR: An error occurred when parsing the server's response.");
		}

		return $httpMessage;
	}





	// Get the access token and save it to the session variables.
	public function oauthFlowAccessToken() {

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

		/*
        $api = $this->loadForceApi();

        $query = "SELECT Contact.AuthorizeDotNetCustomerProfileId__c FROM User WHERE Id = '{$user->getId()}'";

        $result = $api->query($query)->getRecord();
        
        $profileId = $result["Contact"]["AuthorizeDotNetCustomerProfileId__c"];

		*/
		// Step 1: Set up the session for the connected app.
		self::setSession($connectedApp, $flow, $resp->getInstanceUrl(), $resp->getAccessToken(), $resp->getRefreshToken());

		// Step 2: Declare who the current user is.  Create a user session.
		$userInfo = self::getUser($config->getName(), "webserver");
		
		// This is the authenticated user
		$user = new \User($userInfo);
		
		$req = new RestApiRequest($resp->getInstanceUrl(), $resp->getAccessToken());


		// If the user is an admin user, we want to set the user id for the contact info query to that of the admin user's "Linked Customer User".
		// This is the customer user
		if($user->isAdmin()){

			$query = "SELECT Id, LinkedCustomerUser__c FROM User WHERE Id = '{$user->getId()}'";
			$userId = $req->query($query)->getRecord()["LinkedCustomerUser__c"];

		} else {
			
			$userId = $user->getId();
		}

        
        $query = "SELECT ContactId, Contact.AccountId, Contact.Account.Name, Contact.AuthorizeDotNetCustomerProfileId__c FROM User WHERE Id = '$userId'";
		
		$record = $req->query($query)->getRecord();
		$contactId = $record["ContactId"];
		$profileId = $record["Contact"]["AuthorizeDotNetCustomerProfileId__c"];

		$user->setSObject($record);
		$user->setExternalCustomerProfileId($profileId);
		$user->setContactId($contactId);

		\Session::setUser($user);

		$redirect = $this->buildRedirect($_SESSION["login_redirect"]);

		return redirect($redirect);
	}


	/**
	 * @jbernal - setSession(), getUser(), logout() copied from OAuth.php.
	 */
    public static function setSession($connectedApp, $flow, $instanceUrl, $accessToken, $refreshToken = null){

        if($refreshToken != null) \Session::set($connectedApp, $flow, "refresh_token", $refreshToken);
        

        \Session::set($connectedApp, $flow, "instance_url", $instanceUrl);
        \Session::set($connectedApp, $flow, "access_token", $accessToken);


        $userInfo = self::getUser($connectedApp, $flow);
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
	// END FUNCTIONS COPIED FROM OAUTH.PHP.




	
	// Don't need an actual login function, because the route has specified the webserver flow ????
	public function userLogout() {

		$_COOKIE["PHPSESSID"] = array();
		$_SESSION = array();

		// We shouldn't redirect back to a page that would require authorization.
		// In order to do this we would need to compare the current URL to 
		// all available routes.
		// @TODO - supply this functionality.
		if(!USE_SALESFORCE_SLO_LOGOUT_ENDPOINT){
			$currentPage = $_SERVER["HTTP_REFERER"];
			$examplePage = "https://oclda.app/car/list"; // Shouldn't need to be a full URL.
			$defaultPage = "/car/list";
			$redirect = $this->buildRedirect($defaultPage);
			return redirect($defaultPage);
		}

		$config = get_oauth_config();

		// Here comes a crazy temporary fix for getting instance doamain.  Need to update the connected app config and lib-oauth-config
		// with a method for getting the instance url from the connected app config.  Whould be a lot more reliable then the current 
		// method of getting the instance url from the response.....i think.  I might be crazy, just thinking....

		$flow = $config->getFlowConfig();
		$instanceUrlparts = explode("/", $flow->getTokenUrl());
		$removeToken = array_pop($instanceUrlparts);
		$removeOauth = array_pop($instanceUrlparts);
		$removeServices = array_pop($instanceUrlparts);

		$instanceUrl = implode("/", $instanceUrlparts);
		
		$sloEndpoint = "$instanceUrl/services/auth/idp/oidc/logout";

		return redirect($sloEndpoint);
	}


	public function buildRedirect($ref = null){

		if(empty($ref)) $ref = $_SERVER["HTTP_REFERER"];
		
		$redirectParts = explode("/", $ref);

		array_shift($redirectParts); // Remove the protocol
		array_shift($redirectParts); // Remove empty element
		array_shift($redirectParts); // Remove domain

		$redirect = "/" . implode("/", $redirectParts);

		return $redirect;
	}


	public static function getRoutes() {

				
		$coreDef = array(
			"comment"      => "The core module",
			"connectedApp" => "default",
			"name"         => "core",
			"description"  => "holds routes for core functionality",
			"files"        => array(),
			"routes"       => array(
				"system/status/%msg" => array(
					"callback"      => "showStatus",
					"content-type"  => "text/html",
					"module"        => "core",
					"method"        => "get"
				),
				"system/404/%msg" => array(
					"callback"      => "pageNotFound",
					"content-type"  => "text/html",
					"module"        => "core",
					"method"        => "get"
				),
				"system/403" => array(
					"callback"      => "accessDenied",
					"content-type"  => "text/html",
					"module"        => "core",
					"method"        => "get"
				),
				"file/upload" => array(
					"callback"      => "upload",
					"content-type"  => "application/json",
					"path"          => "upload",
					"module"        => "core",
					"method"        => "get"
				),
				"file/download/%filename" => array(
					"callback"      => "download",
					"content-type"  => "application/json",
					"path"          => "download",
					"module"        => "core",
					"method"        => "get"
				),
				"file/list" => array(
					"callback"      => "list",
					"content-type"  => "application/json",
					"path"          => "list/files",
					"module"        => "core",
					"method"        => "get"
				),
				"oauth/start" => array(
					"callback"      => "oauthFlowStart",
					"content-type"  => "application/json",
					"path"          => "oauth/start",
					"module"        => "core",
					"method"        => "get"
				),
				"oauth/api/request" => array(
					"callback"      => "oauthFlowAccessToken",
					"content-type"  => "application/json",
					"path"          => "oauth/api/request",
					"module"        => "core",
					"method"        => "get"
				),
				"login"        => array(
					"callback"      => "login",
					"content-type"  => "application/json",
					"path"          => "login",
					"module"        => "core",
					"method"        => "get",
					"access"        => true,
					"authorization" => "webserver"
				),
				"logout"        => array(
					"callback"      => "userLogout",
					"content-type"  => "application/json",
					"path"          => "logout",
					"module"        => "core",
					"method"        => "get"
				),
				"status"        => array(
					"callback"      => "showStatus",
					"content-type"  => "text/html",
					"module"        => "core",
					"method"        => "get"
				),
				"user/profile"        => array(
					"callback"      => "userProfile",
					"content-type"  => "text/html",
					"path"          => "user/profile",
					"module"        => "core",
					"method"        => "get"
				)


			),
			//If the path is null the module loader will not try to load the file
			//core module is loaded in autoloader.php
			"path"     => null
		);

		return $coreDef;
	}





}