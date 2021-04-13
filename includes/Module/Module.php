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
    
    

    

    public function __construct($path = null){
    	$this->path = $path;
    	$this->className = get_class($this);
    }
    

		public function getPath() {
			$reflector = new \ReflectionClass($this->className);
			return $reflector->getFileName();
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

        $config = getOauthConfig($app);


        $requstedRoute = explode("/", $this->getRequest()->url)[1];
        $routes = $this->getInfo()["routes"];
        $route = $routes[$requestedRoute];

        $flow = isset($route["authorization"]) ? $route["authorization"] : "usernamepassword";  // This is questionable.
        
        $accessToken = Session::get($config->getName(), $flow, "access_token");
        $instanceUrl = Session::get($config->getName(), $flow, "instance_url");

        $req = new RestApiRequest($instanceUrl, $accessToken);

        return $req;
    }


    public function hasDependencies() {
    
        return !empty($this->dependencies);
    }


    public function getFiles(){
        return $this->files;
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