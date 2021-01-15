<?php

namespace Http;


class HttpResponse extends HttpMessage {
	
    
    public function __construct($body = null){

        parent::__construct();

        $this->body = $body;
    }


    public function getFile(){

        return $this->isFile() ? $this->body : null;
    }

    public function isFile(){

        return $this->body != null && gettype($this->body) == "object" && get_class($this->body) == "File\File";
    }

    //Setters
    
    public function setContentType($contentType){
            $header = new HttpHeader("Content-Type", $contentType);

            $this->headers->addHeader($header);
    }

    public function setStatusCode($code){
    	$this->statusCode = $code;
    }
    
    public function setErrorStatus(){
        $this->setStatusCode(500);
        $this->statusMessage = "HTTP/1.1 500 Internal Server Error";
        
    }
    
    public function setNotFoundStatus(){
        $this->setStatusCode(404);
    	$this->statusMessage = "HTTP/1.1 404 Page Not Found";
    }
    
    public function setRedirect($url){
        $this->setStatusCode(301);
    	$this->statusMessage = "HTTP/1.1 301 Moved Permanently";
    	$this->headers->addHeader(new HttpHeader("Location",$url));
    }

    public function isSuccess(){
        return $this->getStatusCode() == 200 || $this->getStatusCode() == 201;
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

	public function success() {
		return $this->isSuccess();
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