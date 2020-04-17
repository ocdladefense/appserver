<?php

namespace Http;


class HttpResponse extends HttpMessage {
	
	
    public function __construct($body = null){
			parent::__construct();
    	$this->body = $body;
    }

    //Setters
    public function setBody($content){
        $this->body = $content;
    }
    
    public function setContentType($contentType){
            $header = new HttpHeader("Content-Type", $contentType);

            $this->headers->addHeader($header);
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
    	$this->headers->addHeader(new HttpHeader("Location",$url));
    }

    //Getters
    public function getStatusCode(){
        return $this->statusCode;
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

    public function getPhpArray(){
        // Parsing the HTTP Response; by parsing we just mean the data has a known format and we can retrieve certain things from the Response.
			return json_decode($this->body, true);
    }

    //other methods

    public function __toString(){
        return $this->body;
    }

}