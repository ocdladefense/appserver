<?php


namespace Http;


class HttpRequest extends HttpMessage {


	// Where should this HttpMessage be sent?
	private $url = null;
	
	private $params = array();

	private $headers = array();
	


	private $requestType = "GET";
	
	// Default to empty body for GET request.
	private $body = "";
	
	private $port;


	public function getRequestUri(){
		return $this->headers["Request-URI"];
	}



	public function __construct($url){
		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}





	public function setHeaders(array $headers) {
		$this->headers = $headers;
	}






	public function getHeader($headerName){
		//throw an exception
		return $this->headers[$headerName];
	}
	
	
	public function getHeaders(){
		return $this->headers;
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
		
		$request->headers = apache_request_headers();
		$request->headers["Request-URI"] = $_SERVER["REQUEST_URI"];
            
		$request->body = file_get_contents('php://input');
		return $request;
	}
	
	
}
