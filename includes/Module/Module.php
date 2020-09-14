<?php
class Module {


    protected $routes = array();
    
    
    protected $dependencies = array();


    protected $files = array();
    
    
    protected $name;
    
    
    protected $path;


    protected $request;
    
    
    protected $theme;
    
    

    public function __construct($path = null){
    	$this->path = $path;
    }

		public function getPath() {
			return $this->path;
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


    public function requireDependencies(){

    }
    
    
    public function requireModuleFile($file){
        $path = getPathToModules()."/{$this->activeRoute->getModule()}/src/".$file;
				require_once($path);
    }


    public function hasDependencies(){
        if(empty($this->dependencies)){
            return false;
        }
        return true;
    }


    public function getFiles(){
        return $this->files;
    }

    public function loadFile($file){
        $path = getPathToModules()."/{$this->name}/src/".$file;
				require_once($path);
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