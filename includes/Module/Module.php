<?php


use Salesforce\OAuthRequest;
use Salesforce\RestApiRequest;


class Module {


    protected $routes = array();
    
    
    protected $dependencies = array();


    protected $files = array();
    
    
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
    
    protected function loadForceApi($debug = false) {
    	return $this->loadApi($debug);
    }
    protected function loadApi($debug = false) {
    
        global $oauth_config;

        $oauth = OAuthRequest::fromConfig($oauth_config);

        $resp = $oauth->authorize();
        
        if($resp->hasError){

            if($debug){
                var_dump($oauth);
            }
            throw new Exception("OAUTH_RESPONSE_ERROR: {$resp->errorMessage}");
        }
    
        return new RestApiRequest($resp->getInstanceUrl(), $resp->getAccessToken());
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
    
    public function toJson() {


			return json_encode($this->getRoutes());
    }
}