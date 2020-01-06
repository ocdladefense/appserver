<?php
class HttpResponse
{
    private $body;
    
    private $headers = array();

		private $statusCode;

    public function __construct($body = null){
    	$this->body = $body;
    }


    //Setters
    public function setBody($content){
        $this->body = $content;
    }
    
    public function setContentType($contentType){
			$this->headers["Content-Type"] = $contentType;
    }
    
    public function setHeaders($headers){
        $this->headers = $headers;
    }
    
    public function setHeader($name,$value) {
    	$this->headers[$name] = $value;
    }
    
    public function setStatusCode($code) {
    	$this->statusCode = $code;
    }
    
    public function setErrorStatus(){
    	$this->statusCode = "HTTP/1.1 500 Internal Server Error";
    }
    
    public function setNotFoundStatus(){
    	$this->statusCode = "HTTP/1.1 404 Page Not Found";
    }
    
    public function setRedirect($url){
    	$this->statusCode = "HTTP/1.1 301 Moved Permanently";
    	$this->headers["Location"] = $url;
    }

    //Getters
    public function getBody(){
        return $this->body;
    }
    
    public function getHeader($headerName){
    	if(!isset($this->headers[$headerName])) {
    		return null;
    	}
    	
			return $this->headers[$headerName];
    }
    
    
    public function getHeaders(){
        return $this->headers;
    }
    
    
    public function getPhpArray(){
        // Parsing the HTTP Response; by parsing we just mean the data has a known format and we can retrieve certain things from the Response.
			return json_decode($this->body, true);
    }

    //other methods

    //Send the value of the headers array at the key of content-type 
    public function sendHeaders(){

			foreach($this->headers as $headerName => $headerValue){
				header($headerName.": ".$headerValue);
			}
			if($this->statusCode != null){
				header($this->statusCode);
			}
    }
    public function __toString(){
        return $this->body;
    }

}