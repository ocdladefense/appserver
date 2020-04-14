<?php


namespace Http;


class HttpRequest extends HttpMessage {



	protected $host;

	private $requestType = "GET";
	
	// Default to empty body for GET request.
	private $body = "";
	
	private $port;


	public function getRequestUri(){
		return $this->headers["Request-URI"];
	}



	public function __construct($url){
		$this->url = $url;


		$this->host = self::parseHostname($url);

		$this->headers[]= new HttpHeader("Host",$this->host);
	}



	public function getUrl() {
		return $this->url;
	}



	private static function parseHostname($url) {
		$host = explode("https://",$url)[1];
		$host = explode("/",$host)[0];
		
		return $host;
	}





	public function getHost() {
		return $this->host;
	}

	





	
	public function setPost(){
		$this->requestType = "POST";
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
		if($this->getHeader("Accept") == $contentType || stringContains($this->headers["Accept"], "*/*")){
			return true;
		}
		return false;
	}
	
	


	



	public function getMethod(){

	}
	
	public function getPath(){

	}




	
	
	
	
	public static function newFromEnvironment(){
		$request = new self($_SERVER["REQUEST_URI"]);
		
		$request->headers = apache_request_headers();
		$request->headers["Request-URI"] = $_SERVER["REQUEST_URI"];
            
		$request->body = file_get_contents('php://input');
		return $request;
	}
	
	
}
