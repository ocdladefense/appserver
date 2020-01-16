<?php

class Route {

	private $routeInfo;
	
	
	private $routeArgs;
	
	

	public function __construct($routeInfo, $args) {
		$this->routeInfo = $routeInfo;
		
		$this->routeArgs = $args;	
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
		return $this->routeInfo["Content-Type"];
	}
}