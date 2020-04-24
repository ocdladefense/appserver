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
		$this->method = "POST";
	}
	
	
	public function setPatch(){
		$this->requestType = "PATCH";
	}
	
	
	public function setDelete(){
		$this->requestType = "DELETE";
	}
	
	
	public function getRequestType(){
		return $this->requestType;
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
		
		//$request->headers = apache_request_headers();
		$request->addHeader(new HttpHeader("Request-URI",$_SERVER["REQUEST_URI"]));
            
		$request->setBody(file_get_contents('php://input'));
		return $request;
	}

	public function addParameter($name, $value){
		$this->params[] .= $name ."=". $value;
	}
	
	
}
