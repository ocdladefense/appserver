<?php

use Salesforce\OAuthRequest;
use Salesforce\RestApiRequest;
use Salesforce\OAuth;


class Module {

    const SESSION_ACCESS_TOKEN_EXPIRED_ERROR_CODE = "INVALID_SESSION_ID";

    // The list of Modules installed for this application.
    private static $index;

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
    






    public static function catalog($index) {

    	self::$index = $index;
    }



    // Load one or more modules for the specified paths.
    public static function loadModules($paths) {

        $previous = getcwd();

        foreach($paths as $mod)  {
            require_once($mod."/module.php");
        }
        
    }
    
    

    // Return a list of all modules matching the query.
    public function query($prop, $value = null) {

        return array_filter(self::$index, function($module) use($prop,$value) {
            return $module[$prop] == $value;
        });
    }


    
    public static function load($name) {
        
    	if(!isset(self::$index[$name])) {
    		throw new Exception("MODULE_NOT_FOUND_ERROR: {$name}.");
    	}
    	$info = self::$index[$name];
    	$path = $info["path"];
        
        if($path == null) return;
        
    	require_once($path."/module.php");
    	
    	foreach($info["files"] as $file) {
    		require($path . "/src/" . $file);
    	}
        
        return $info;
    }


    /**
     * Return an array of values for the given key
     * in all loaded module.json files.
     */
    public static function getKey($key) {

        if(null == $key) throw new Exception("MODULE_PARSE_ERROR: key not specified or null when attempting to retrieve value.");
        // Build an index for routes.
        $values = array();
        foreach(self::$index as $def) {
            if(isset($def[$key])) {
                $values = array_merge($values,$def[$key]);
            }
        }

        return array_filter($values);
    }
    
    
    
    public static function loadObject($name) {
    	$info = self::load($name);
    	return self::getInstance($name, $info);
    }
    
    
    
    // Require each of the dependencies for each module
    public static function getInstance($moduleName, $info = null) {

        if(empty($moduleName)) {
            throw new Exception("MODULE_ERROR: Cannot instantiate empty module class.");
        }
    	
        $className = ucwords($moduleName,"-\t\r\n\f\v");
        $className = str_replace("-","",$className)."Module";
        $moduleClass = new $className($info["path"]);
        $moduleClass->setInfo($info);
        $moduleClass->setName($info["name"]);
        $moduleClass->setPath($info["path"]);
        $moduleClass->setLanguages($info["languages"]);
        $moduleClass->setLanguageFiles($info["language-files"]);
        $dependencies = $moduleClass->getDependencies();

        foreach($dependencies as $d){
            $instance = self::getInstance($d);
            $instance->loadFiles();
        }
        return $moduleClass;
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



    public function getModule($name) {
        return self::$index[$name];
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



    
    protected function loadForceApi($app = null, $debug = false) {

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


    protected function loadForceApiFromFlow($flow, $connectedAppName = null) {

        $config = get_oauth_config($connectedAppName);
        
        $accessToken = Session::get($config->getName(), $flow, "access_token");
        $instanceUrl = Session::get($config->getName(), $flow, "instance_url");

        $req = new RestApiRequest($instanceUrl, $accessToken);

        return $req;
    }




    protected function loadApi($api) {


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