<?php


namespace Http;


class HttpRequest extends HttpMessage {


	protected $method = "GET";
	
	
	protected $host;
		
	
	protected $path;

	
	private $port;


	protected $params = array();


	public function getRequestUri() {
		return $this->getHeader("Request-URI")->getValue();
	}


	public function __construct($url) {
		parent::__construct();
		$this->url = $url;

		list($this->host, $this->path) = self::parseHostname($this->url);


		$this->headers->addHeader(new HttpHeader("Host",$this->host));
	}


	public function getUrl() {
		return $this->url;
	}

	public function getArguments(){
		$url = new \Url($this->url);

		return $url->getArguments();
	}

	public function getUrlNamedParameters(){
		$url = new \Url($this->url);

		return $url->getNamedParameters();
	}


	private static function parseHostname($url) {
		list($scheme,$address) = explode("://",$url);

		$parts = explode("/",$address);

		$host = array_shift($parts);
		return array($host, "/".implode("/",$parts));
	}


	public function getHost() {
		return $this->host;
	}

	
	public function setPost(){
		$this->method = HTTP_METHOD_POST;
	}
	
	public function isPost(){
		return $this->method == HTTP_METHOD_POST;
	}
	
	
	public function setPatch(){
		$this->method = "PATCH";
	}
	
	
	public function setDelete(){
		$this->method = "DELETE";
	}
	
	
	public function getRequestType(){
		return $this->method;
	}
	
	
	public function setPort($port) {
		$this->port = $port;
	}
	
	
	public function getBody() {
		return $this->body;
	}


	
	
	
	public function isSupportedContentType($contentType){

		return true;

		
		$accept = $this->getHeader("Accept")->getValue();

		return $accept == $contentType || stringContains($accept, "*/*");
	}
	



	public function getMethod(){
		return $this->method;
	}

	
	
	
	public function getPath(){

	}

	public function setParams($p){
		if(is_array($p)) {
			  $_params = array();
			  foreach($p as $key=>$value){
				  $_params[] = $key ."=".$value;
			  }		
			  $this->params = implode('&',$_params);
		}
		else {
			$this->params = $p;
		}
  
	  }

	public static function newFromEnvironment(){
		$request = new self($_SERVER["REQUEST_URI"]);
		// $_SERVER["HTTP_ACCEPT"] = "application/json";
		// var_dump($_SERVER["HTTP_ACCEPT"]);
		// var_dump(apache_request_headers()["Accept"]);


		if($_SERVER["REQUEST_METHOD"] == HTTP_METHOD_POST){
			$request->setPost();
		}
		$request->addHeader(new HttpHeader("Request-URI",$_SERVER["REQUEST_URI"]));

		if($request->method == HTTP_METHOD_POST){
			$request->addHeader(new HttpHeader("Content-Type", apache_request_headers()["Content-Type"]));
			//$request->addHeader(new HttpHeader("Accept", "application/json"));
		}


		if($request->method == HTTP_METHOD_POST && $request->getHeader("Content-Type")->getValue() == CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED){
			$request->setBody((object)$_POST);
		} elseif($request->method != HTTP_METHOD_GET) {
			$content = file_get_contents('php://input');
			$request->setBody(json_decode($content));
		}

		if($request->method == HTTP_METHOD_GET){
			$request->setBody(null);
		}
			
		return $request;
	}

	public function addParameter($name, $value){
		$this->params[] .= $name ."=". $value;
	}
	
	
}
