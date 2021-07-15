<?php

use Salesforce\OAuthRequest;
use Salesforce\RestApiRequest;
use Salesforce\OAuth;


class Module {

    const SESSION_ACCESS_TOKEN_EXPIRED_ERROR_CODE = "INVALID_SESSION_ID";

    protected $routes = array();

    protected $currentRoute;
    
    protected $dependencies = array();

    protected $files = array();

    protected $info;

    protected $name;
    
    protected $path;

    protected $request;
    
    protected $theme;
    
    protected $user;
    
    protected $languages;

    protected $languageFiles;

    

    public function __construct($path = null){

    	$this->path = $path;
    	$this->className = get_class($this);
    }
    

    // Getters
    public function getPath() {

        return $this->path;
    }

    public function getRelPath() {
        
        return $this->path;
    }

    public function getFiles(){

        return $this->files;
    }

    public function getLanguages(){

        return $this->languages;
    }
    
	public function getLanguageFiles(){

        return $this->languageFiles;
    }

    public function getCurrentRoute(){

        return $this->currentRoute;
    }

    public function getDependencies(){

        return $this->dependencies;
    }

    public function getInfo(){

        return $this->info;
    }
    
    public function get($key){

        return $this->info[$key];
    }

    public function getRequest(){

        return $this->request;
    }
    
    
    public function getTheme() {

    	return $this->theme;
    }


    // Setters
    public function setPath($path){

        $this->path = $path;
    }

    public function setName($name){

        $this->name = $name;
    }

	public function setLanguages($languages){

        $this->languages = $languages;
    }	

	public function setLanguageFiles($languageFiles){

        $this->languageFiles = $languageFiles;
    }

    public function setCurrentRoute($route){

        $this->currentRoute = $route;
    }

    public function setInfo($info){

        $this->info = $info;
    }

    public function setRequest($request){

        $this->request = $request;
    }    
    
    public function setTheme($theme) {

    	$this->theme = $theme;
    }

    // Other functions
    
    protected function loadForceApi($app = null, $debug = false) {

    	return $this->loadApi($app, $debug);
    }


    protected function loadForceApiFromFlow($flow, $connectedAppName = null) {

        $config = get_oauth_config($connectedAppName);
        
        $accessToken = Session::get($config->getName(), $flow, "access_token");
        $instanceUrl = Session::get($config->getName(), $flow, "instance_url");

        $req = new RestApiRequest($instanceUrl, $accessToken);

        return $req;
    }




    protected function loadApi($connectedAppName = null, $debug = false) {

        if(empty($this->getInfo()["connectedApp"])){
            
            throw new Exception("CONFIGURATION_ERROR: No 'Connected App' sepecified.  Update the 'module.json' file for your module.");
        }


        $config = get_oauth_config($connectedAppName);
        
        $route = $this->getCurrentRoute();
        
        // If a OAuth flow is set on the route get that flow, and get the
        // access token that is stored in at the index of the flow for the connected app.
        // Refresh token does not work with the username password flow.
        $flow = isset($route["authorization"]) ? $route["authorization"] : "usernamepassword";
        
        $accessToken = Session::get($config->getName(), $flow, "access_token");
        $instanceUrl = Session::get($config->getName(), $flow, "instance_url");

        $req = new RestApiRequest($instanceUrl, $accessToken);

        return $req;
    }

    protected function execute($soql, $queryType, $debug = false) {

        $api = $this->loadForceApi();

        $resp = call_user_func(array($api, $queryType), $soql);

		if(!$resp->success() && $resp->getErrorCode() == self::SESSION_ACCESS_TOKEN_EXPIRED_ERROR_CODE){

            // Get the current route so that you can get the oauth flow if there is one set.
            $route = $this->getCurrentRoute();
            $flow = isset($route["authorization"]) ? $route["authorization"] : "usernamepassword";

			$config = get_oauth_config($this->getInfo()["connectedApp"]);
			$req = OAuthRequest::refreshAccessTokenRequest($config, $flow);
			$oauthResp = $req->authorize();

			$accessToken = $oauthResp->getAccessToken();

			\Session::set($config->getName(), $flow, "access_token", $accessToken);

            $api = $this->loadForceApi();
            $resp = call_user_func(array($api, $queryType), $soql);
			
			if($debug) $message = "ACCESS TOKEN WAS REFRESHED";

		} else if(!$resp->success()) {

			throw new Exception($resp->getErrorMessage());

		} else {

			if($debug) $message = "ACCESS TOKEN WAS NOT REFRESHED";
		}

        return $debug ? array("response" => $resp, "message" => $message) : $resp;

    }

    public function hasDependencies() {
    
        return !empty($this->dependencies);
    }

    public function loadFile($file){

        require_once("/{$this->path}/src/".$file);
    }

    public function loadFiles(){

        foreach ($this->files as $file){

            $this->loadFile($file);
        }
    }

    public function toJson() {
        
        return json_encode($this->getRoutes());
    }
}


// protected function loadApiOld($app = null, $debug = false) {

//     $config = get_oauth_config($app);
//     $oauth = OAuthRequest::usernamePasswordFlowAccessTokenRequest($config, "usernamepassword");

//     $resp = $oauth->authorize();
    
//     if($debug) var_dump($config, $oauth, $resp);
    
    
//     if(!$resp->success()) {
//         throw new Exception("OAUTH_RESPONSE_ERROR: {$resp->getErrorMessage()}");
//     }

//     return new RestApiRequest($resp->getInstanceUrl(), $resp->getAccessToken());
// }