<?php
class HttpRequest
{
	private $handle = null;

	private $params = array();
	private $status; 
	private $errorString = null;
	private $errorNum = null;
	private $headers = array();
	private $requestType = "GET";
	private $info;
	private $body;
	 
	public function __construct($endpoint){
		// Return a handle to a process that can make an HTTP Request.
		$this->handle = curl_init($endpoint);
	}


	// Set our HTTP Request parameters.
	// $params = "code=" . $code . "&grant_type = authorization_code&client_id=" 
	//. CLIENT_ID. "&client_secret=" . CLIENT_SECRET. "&redirect_uri=" .urlencode(REDIRECT_URI);
	public function setParams($p){
	  // name/value pairs
	  // each name/value pair is separate by ampersand
	  // each name/value pair is set by an `=` sign
	  if(is_array($p)){
		$_params = array();
		foreach($p as $key=>$value){
			$_params[] = $key ."=".$value;
		}		
		$this->params = implode('&',$_params);
	  }
	  else{
		  $this->params = $p;
	  }

	}
	public function setHeader($headerName,$value){
		$this->headers[$headerName] = $value;
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
	public function setOptions($params){
		// Set various options for our HTTP Request.
		curl_setopt($this->handle, CURLOPT_HEADER, false);
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
		if($this->getRequestType() == "POST")
		{
			curl_setopt($this->handle, CURLOPT_POST, true);
			curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
		}
		if($this->getRequestType() == "PATCH")
		{
			curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "PATCH");
			curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
		}
		if($this->getRequestType() == "DELETE")
		{
			curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, false);
		}

		if(count($this->headers)>0)
		{
			curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);
		}
	}
	public function addHeader($header){
		$this->headers[] = $header; 
	}
	public function ignoreSSLVerification(){
		//Ignore the SSL vaification
		// https://curl.haxx.se/libcurl/c/CURLOPT_SSL_VERIFYPEER.html
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYPEER, false);
	}

	public function makeHttpRequest(){
		$this->setOptions($this->params);
		$this->ignoreSSLVerification();
		// Make the actual HTTP Request AND it returns an HTTP Response.

		$_response = curl_exec($this->handle);
		
		$resp = new HttpResponse($_response);
		$resp->setStatusCode(curl_getinfo($this->handle, CURLINFO_HTTP_CODE));
		$resp->setContentType(curl_getinfo($this->handle, CURLINFO_CONTENT_TYPE));
		$resp->setCurlInfo(curl_getinfo($this->handle));
		
		
		$this->info = curl_getinfo($this->handle);

		
		$this->close();

		return $resp;	
	}
	
	public function send() {
		return $this->makeHttpRequest();
	}

	public function getStatus(){
		// Returns the status, e.g., 404 Not Found, 500 Internal Server Error of our HTTP Response.
		return $this->status;
	}

	public function getInfo() {
		return $this->info;
	}
	
	public function getBody() {
		return $this->body;
	}

	public function close(){
		// Closing the HTTP connection.
		curl_close($this->handle);
	}
	public function getError(){
		return $this->errorString;
	}

	public function getErrorNum(){
		return $this->errorNum;
	}

	public function success(){
		return $this->status == 200;
	}
	
	public function setHeaders($headers){
		$this->headers = $headers;
	}
	
	public function isSupportedContentType($contentType){
		if($this->getHeader("Accept") == $contentType || stringContains($this->headers["Accept"], "*/*")){
			return true;
		}
		return false;
	}
	
	public function getHeader($headerName){
		//throw an exception
		return $this->headers[$headerName];
	}
	
	public function getHeaders(){
		return $this->headers;
	}
	
	public function getRequestUri(){
		return $this->headers["Request-URI"];
	}
	
	public static function newFromEnvironment(){
		$request = new self($_SERVER["REQUEST_URI"]);
		
		$request->headers = apache_request_headers();
		$request->headers["Request-URI"] = $_SERVER["REQUEST_URI"];
            
		$request->body = file_get_contents('php://input');
		return $request;
	}
}
