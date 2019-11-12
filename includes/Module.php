<?php
class Module {


    protected $routes = array();
    protected $dependencies = array();

    public function __construct($application){}

    public function getRoutes(){
        return $this->routes;
    }
    public function getDependencies(){
        return $this->dependencies;
    }
    public function hasDependencies(){
        if(empty($this->dependencies)){
            return false;
        }
        return true;
    }
    public function requireDependencies(){

    }
}