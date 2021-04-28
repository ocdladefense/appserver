<?php

use Salesforce\OAuthRequest;
use Salesforce\RestApiRequest;
use Salesforce\OAuth;


class Module {


    protected $routes = array();
    
    
    protected $dependencies = array();


    protected $files = array();


    protected $info;
    
    
    protected $name;
    
    
    protected $path;


    protected $request;
    
    
    protected $theme;
    
    
    protected $user;
    
    protected $languages;

    

    public function __construct($path = null){
    	$this->path = $path;
    	$this->className = get_class($this);
    }
    

    public function getPath() {
        $reflector = new \ReflectionClass($this->className);
        return $reflector->getFileName();
    }

    public function getRelPath() {
        return $this->path;
    }

    public function setPath($path){
        $this->path = $path;
    }

    public function setName($name){
        $this->name = $name;
    }

	public function setLanguages($languages){
        $this->languages = $languages;
    }	
		
    public function getRoutes(){
        return $this->routes;
    }



    public function getDependencies(){
        return $this->dependencies;
    }


    public function setRequest($request){
        $this->request = $request;
    }


    public function getRequest(){
        return $this->request;
    }
    
    
    public function setTheme($theme) {
    	$this->theme = $theme;
    }
    
    
    
    public function getTheme() {
    	return $this->theme;
    }


    public function requireDependencies() {

    }
    
    protected function loadForceApi($app = null, $debug = false) {
    	return $this->loadApi($app, $debug);
    }

    protected function loadApi($app = null, $debug = false) {
    


        $config = get_oauth_config($app);
        $oauth = OAuthRequest::usernamePasswordFlowAccessTokenRequest($config, "usernamepassword");

        $resp = $oauth->authorize();
		
	    if($debug) var_dump($config, $oauth, $resp);
        
        
        if(!$resp->success()) {
            throw new Exception("OAUTH_RESPONSE_ERROR: {$resp->getErrorMessage()}");
        }
    
        return new RestApiRequest($resp->getInstanceUrl(), $resp->getAccessToken());
    }

    protected function loadApiV2($app = null, $debug = false) {

        $config = get_oauth_config($app);


        $requstedRoute = explode("/", $this->getRequest()->url)[1];
        $routes = $this->getInfo()["routes"];
        $route = $routes[$requestedRoute];

        $flow = isset($route["authorization"]) ? $route["authorization"] : "usernamepassword";  // This is questionable.
        
        $accessToken = Session::get($config->getName(), $flow, "access_token");
        $instanceUrl = Session::get($config->getName(), $flow, "instance_url");

        $req = new RestApiRequest($instanceUrl, $accessToken);
        $req->setConfig($config);

        return $req;
    }


    public function hasDependencies() {
    
        return !empty($this->dependencies);
    }


    public function getFiles(){
        return $this->files;
    }

    public function getLanguages(){
        return $this->languages;
    }



    public function loadFile($file){
				require_once("/{$this->path}/src/".$file);
    }


    public function loadFiles(){
        foreach ($this->files as $file){
            $this->loadFile($file);
        }
    }

    public function setInfo($info){

        $this->info = $info;
    }

    public function getInfo(){

        return $this->info;
    }
    
    public function get($key){

        return $this->info[$key];
    }
    public function toJson() {


			return json_encode($this->getRoutes());
    }
}