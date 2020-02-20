<?php
class Module {


    protected $routes = array();
    
    
    protected $dependencies = array();
    
    
    protected $files = array();
    
    
    protected $name;
    
    

    public function __construct($application = null){
    
    }


    public function getRoutes(){
        return $this->routes;
    }


    public function getDependencies(){
        return $this->dependencies;
    }


    public function requireDependencies(){

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
}