<?php

class Route {

	private $routeInfo;
	
	private $routeArgs;
	
	

	public function __construct($routeInfo) {
		$this->routeInfo = $routeInfo;
	}
	

	//require all of the necessary file in the route at the key of 'files'
	public function requireRouteFiles($basePath = ""){
			if(null == $this->getFiles())
					return;
					
			foreach($this->getFiles() as $file){
				$path = $basePath . "/" . $file;
				require_once($path);
			}
	}
	
	
	public function getMethod() {
		return $this->routeInfo["method"];
	}
	
	
	public function getCallback() {
		return $this->routeInfo["callback"];
	}
	
	public function getArgs(){
		return $this->routeArgs;
	}
	
	public function getModule() {
		return $this->routeInfo["module"];
	}
	
	public function getFiles() {
		return $this->routeInfo["files"];
	}
	
	public function getContentType() {
		return $this->routeInfo["content-type"];
	}

	public function getParameters(){
		return $this->routeInfo["parameters"];
	}

	public function isPost(){
		return $routeInfo["method"] == \Http\HTTP_METHOD_GET;
	}
}