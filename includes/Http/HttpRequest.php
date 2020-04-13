<?php


/** @deprecate
  ** should be removed soon.
  */

class HttpRequest {

	public $handle = null;

	private $params = array();

	private $headers = array();
	
	private $headersSent = array();
	
	private $status; 
	
	private $errorString = null;
	
	private $errorNum = null;

	private $requestType = "GET";
	
	private $info;
	
	private $body;
	
	private $port;
	
	private $ua;
	
	public $out;
	 
	public function __construct($endpoint){
		// Return a handle to a process that can make an HTTP Request.

     //curl_setopt($this->handle, CURLOPT_ENCODING, '');
          ob_start();
     $this->out = fopen('php://output', 'w');
		//$f = fopen($logFile, 'a');
		if(!$this->out) throw new Exception("Could not open PHP output stream.");
		
				$this->handle = curl_init();
    curl_setopt($this->handle, CURLOPT_URL, $endpoint);
		 
		curl_setopt($this->handle, CURLOPT_VERBOSE,true);
		curl_setopt($this->handle, CURLOPT_STDERR ,$this->out);	
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
	
	public function setOpt($opt,$value) {
		curl_setopt($this->handle, $opt, $value);
	}



	
	public function setOptions($params){

		// curl_setopt($this->handle, CURLOPT_HEADER, false);
		curl_setopt($this->handle, CURLOPT_HEADER, true);
		
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
		
		if($this->getRequestType() == "POST") {
			curl_setopt($this->handle, CURLOPT_POST, true);
			curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
		}
		
		if($this->getRequestType() == "PATCH") {
			curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "PATCH");
			curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
		}
		
		if($this->getRequestType() == "DELETE") {
			curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, false);
		}


		curl_setopt($this->handle, CURLOPT_TIMEOUT, 10);
	}
	
	
	public function setCertificateAuthority($path) {
		curl_setopt($this->handle, CURLOPT_CAINFO, $path);
	}
	
	 
	public function getSentHeaders() {
		return $this->headersSent;
	}
	



	/**
	 * 	Make the actual Http Request.
	 *   returns an HttpResponse object.
	 */
	public function makeHttpRequest() {

		$this->setOptions($this->params);


		if(null != $this->port) {
			curl_setopt($this->handle,CURLOPT_PORT,$this->port);
		}

		if(null != $this->ua) {
			curl_setopt($this->handle, CURLOPT_USERAGENT, $this->ua);
		}
		
//		curl_setopt($this->handle, CURLINFO_HEADER_OUT, true);
		

		$this->sendHeaders();
		
     //$logFile = BASE_PATH.'/log/curl.log';

		curl_setopt($this->handle, CURLOPT_VERBOSE,true);
		curl_setopt($this->handle, CURLOPT_STDERR ,$f);	
		
		$_response = curl_exec($this->handle);
		
		fclose($this->out);  
		$debug = ob_get_clean();
		
		print "<h2>DEBUG IS:</h2>";
		print $debug;
		exit;
		
//		$this->headersSent = curl_getinfo($this->handle, CURLINFO_HEADER_OUT );
		// var_dump(debug_backtrace());
		
		$resp = new HttpResponse($_response);
		$resp->setStatusCode(curl_getinfo($this->handle, CURLINFO_HTTP_CODE));
		$resp->setContentType(curl_getinfo($this->handle, CURLINFO_CONTENT_TYPE));
		$resp->setCurlInfo(curl_getinfo($this->handle));
		
		
		$this->info = curl_getinfo($this->handle);

		
		$this->close();

		return $resp;	
	}
	
	
	/**
	 * Alias for makeHttpRequest.
	 */
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
	
	
	public function setHeader($name,$value) {
		$this->headers[$name] = $value;
	}
	
	
	public function setHeaders(array $headers){

		foreach( $headers as $header ) {
		
			if($header instanceOf HttpHeader) {
				$this->headers[$header->getName()] = $header->getValue();
			} else {
				$this->headers[] = $header;
			}
		}
	}


	
	/**
	 * $f = fopen('request.txt', 'w');
			curl_setopt($ch,CURLOPT_VERBOSE,true);
			curl_setopt($ch,CURLOPT_STDERR ,$f);	
	*/
	private function sendHeaders() {
	
		$headers = array();
		
		foreach($this->headers as $name => $value) {
			if(strpos($value,":") !== false) {
				$header = $value;
			} else {
				$header = ($name .": " .$value);
			}
			
			$headers[]= $header;
		}		
			
		curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);			
	}


	
	public function getRequestUri(){
		return $this->headers["Request-URI"];
	}


	/** 
	 * 	Ignore the SSL vaification
	 *
	 * For more information see:
	 *  https://curl.haxx.se/libcurl/c/CURLOPT_SSL_VERIFYPEER.html
	 */
	public function ignoreSSLVerification() {
		$this->verifyHost(false);
		$this->verifyPeer(false);
	}

	public function verifyHost($boolean = true) {
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYHOST, $boolean); 		
	}
	
	public function verifyPeer($boolean = true) {
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYPEER, $boolean);		
	}
	
	public function setCaInfo($path) {
		curl_setopt($this->handle, CURLOPT_CAINFO, $path);
	}
	
	public function setCaPath($path) {
		
	}


	public function userAgent($ua) {
		$this->ua = $ua;
	}


	
	public static function newFromEnvironment(){
		$request = new self($_SERVER["REQUEST_URI"]);
		
		$request->headers = apache_request_headers();
		$request->headers["Request-URI"] = $_SERVER["REQUEST_URI"];
            
		$request->body = file_get_contents('php://input');
		return $request;
	}
	
	
	public static function newAuthorization($url,$user,$pass) {
		$req = new HttpRequest($url);
		$req->setPost();
		$req->setOpt(CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
		// curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$base64 = base64_encode($user.":".$pass);
		$req->setOpt(CURLOPT_USERPWD, $user.":".$pass);// credentials goes here
		
		return $req;
	}
}
